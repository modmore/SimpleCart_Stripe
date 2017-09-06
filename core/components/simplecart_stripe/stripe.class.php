<?php

require_once __DIR__ . '/shared.class.php';

class SimpleCartStripePaymentGateway extends SimpleCartStripeShared
{
    public function view() {
        $tpl = $this->getProperty('cart_tpl', 'scStripeCart');
        $footerTpl = $this->getProperty('cart_footer_tpl', 'scStripeFooter');
        $phs = array(
            'publishable_key' => addslashes($this->getProperty('publishable_key', 'ENTER YOUR KEY')),
            'method_id' => $this->method->get('id'),
        );

        $head = $this->simplecart->getChunk($footerTpl, $phs);
        $this->modx->regClientHTMLBlock($head);

        return $this->simplecart->getChunk($tpl, $phs);
    }

    public function submit() {
        if (!$this->initStripe()) {
            return false;
        }

        $sourceId = $this->getField('stripeSource');
        if (empty($sourceId)) {
            $this->order->addLog('[Stripe] Source', 'Not submitted');
            $this->order->set('status', 'payment_failed');
            $this->order->save();
            return false;
        }
        $this->order->addLog('[Stripe] Source', $sourceId);

        // SimpleCart stores totals in decimal values, so we need to turn that into cents for Stripe
        $amount = $this->getAmountInCents();

        // Get the currency
        $currency = $this->getProperty('currency', 'EUR');
        $currency = strtolower($currency);

        // Get the description
        $description = $this->getDescription();

        $source = \Stripe\Source::retrieve($sourceId);

        $use3DS = false;
        if ($source['card']['three_d_secure'] !== 'not_supported') {
            // The card supports 3D Secure
            $use3DS = $source['card']['three_d_secure'] === 'required';
            if (!$use3DS && $this->getProperty('use_3ds_if_optional', 1)) {
                $use3DS = true;
            }
        }

        $this->order->addLog('[Stripe] 3DS Support', $source['card']['three_d_secure']);

        // If we go ahead and use 3DS, create a new source of type three_d_secure
        if ($use3DS) {
            try {
                $source = \Stripe\Source::create([
                    'amount' => $amount, // amount in cents
                    'currency' => $currency,
                    'statement_descriptor' => $description,
                    'type' => 'three_d_secure',
                    'three_d_secure' => array(
                        'card' => $sourceId,
                    ),
                    'metadata' => [
                        'order_nr' => $this->order->get('ordernr'),
                        'order_id' => $this->order->get('id'),
                    ],
                    'redirect' => array(
                        'return_url' => $this->getRedirectUrl(),
                    ),
                ]);
                $this->order->addLog('[Stripe] 3DS Source', $source['id']);
                $this->order->save();
            } catch (\Stripe\Error\Card $e) {
                // The card has been declined
                $this->order->addLog('[Stripe] 3DS Card Declined', $e->getMessage());
                $this->order->set('status', 'payment_failed');
                $this->order->save();

                return false;
            } catch (\Stripe\Error\Base $e) {
                $this->order->addLog('[Stripe] 3DS Error', $e->getMessage());
                $this->order->set('status', 'payment_failed');
                $this->order->save();

                return false;
            } catch (Exception $e) {
                $this->order->addLog('Uncaught Exception', $e->getMessage());
                $this->order->set('status', 'payment_failed');
                $this->order->save();

                return false;
            }

            // Redirect the customer to the 3DS page
            if (!empty($source['redirect']) && !empty($source['redirect']['url'])) {
                $this->order->addLog('[Stripe] Redirecting for 3DS', $source['redirect']['url']);
                $this->order->set('async_payment_confirmation', true);
                $this->order->save();
                $this->modx->sendRedirect($source['redirect']['url']);
            }
        }

        // If we made it here, either 3DS failed, or 3DS isn't supported.
        // So we do a normal charge.
        return $this->createCharge($sourceId);
    }
}