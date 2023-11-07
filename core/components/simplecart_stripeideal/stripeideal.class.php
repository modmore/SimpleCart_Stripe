<?php

use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\Source;

require_once dirname(__DIR__) . '/simplecart_stripe/shared.class.php';

class SimpleCartStripeiDealPaymentGateway extends SimpleCartStripeShared
{
    public function view() {
        $tpl = $this->getProperty('cart_tpl', 'scStripeiDealCart');
        return $this->simplecart->getChunk($tpl, []);
    }

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

        // Create a bancontact source
        try {
            $source = Source::create([
                'amount' => $amount, // amount in cents
                'currency' => $currency,
                'statement_descriptor' => $description,
                'type' => 'ideal',
                'ideal' => array(
                    'bank' => $this->getField('ideal_bank'),
                ),
                'metadata' => [
                    'order_nr' => $this->order->get('ordernr'),
                    'order_id' => $this->order->get('id'),
                ],
                'redirect' => array(
                    'return_url' => $this->getRedirectUrl(),
                ),
            ]);
            $this->order->addLog('[Stripe] iDeal Source', $source['id']);
            $this->order->save();
        } catch (CardException $e) {
            // The card has been declined
            $this->order->addLog('[Stripe] iDeal Declined', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();

            return false;
        } catch (ApiErrorException $e) {
            $this->order->addLog('[Stripe] iDeal Error', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();

            return false;
        } catch (Exception $e) {
            $this->order->addLog('[Stripe] Uncaught Exception', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();

            return false;
        }

        // Redirect the customer to the 3DS page
        if (!empty($source['redirect']) && !empty($source['redirect']['url'])) {
            $this->order->addLog('[Stripe] Redirecting to iDeal', $source['redirect']['url']);
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
