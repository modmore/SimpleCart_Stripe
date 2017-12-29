<?php

require_once dirname(__DIR__) . '/simplecart_stripe/shared.class.php';

class SimpleCartStripeBancontactPaymentGateway extends SimpleCartStripeShared
{
    public function submit() {
        if (!$this->initStripe()) {
            return false;
        }

        // SimpleCart stores totals in decimal values, so we need to turn that into cents for Stripe
        $amount = $this->getAmountInCents();

        // Get the currency
        $currency = $this->getProperty('currency', 'EUR');
        $currency = strtolower($currency);

        // Get the description
        $description = $this->getDescription();

        // Get the language
        $lang = $this->getProperty('language', 'en');
        $lang = strtolower($lang);

        /** @var array $address */
        $address = $this->order->getAddress();
        // Create a bancontact source
        try {
            $source = \Stripe\Source::create([
                'amount' => $amount, // amount in cents
                'currency' => $currency,
                'statement_descriptor' => $description,
                'type' => 'bancontact',
                'bancontact' => [
                    'preferred_language' => $lang,
                ],
                'owner' => [
                    'name' => is_array($address) ? $address['firstname'] . ' ' . $address['lastname'] : '',
                    'email' => is_array($address) ? $address['email'] : '',
                ],
                'metadata' => [
                    'order_nr' => $this->order->get('ordernr'),
                    'order_id' => $this->order->get('id'),
                ],
                'redirect' => [
                    'return_url' => $this->getRedirectUrl(),
                ],
            ]);
            $this->order->addLog('[Stripe] Bancontact Source', $source['id']);
            $this->order->save();
        } catch (\Stripe\Error\Card $e) {
            // The card has been declined
            $this->order->addLog('[Stripe] Bancontact Declined', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();

            return false;
        } catch (\Stripe\Error\Base $e) {
            $this->order->addLog('[Stripe] Bancontact Error', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();

            return false;
        } catch (Exception $e) {
            $this->order->addLog('[Stripe] Uncaught Exception', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();

            return false;
        }

        // Redirect the customer to Bancontact
        if (!empty($source['redirect']) && !empty($source['redirect']['url'])) {
            $this->order->addLog('[Stripe] Redirecting to Bancontact', $source['redirect']['url']);
            $this->order->set('async_payment_confirmation', true);
            $this->order->save();
            $this->modx->sendRedirect($source['redirect']['url']);
        }

        $this->order->addLog('[Stripe] Source Error', 'No redirect provided in ' . print_r($source,true));
        $this->order->set('status', 'payment_failed');
        $this->order->save();
        return false;
    }
}