<?php

require __DIR__ . '/shared.class.php';

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
        $amount = $this->order->get('total');
        $amount = (int)$amount * 100;

        // Get the currency
        $currency = $this->getProperty('currency', 'EUR');
        $currency = strtolower($currency);

        // Get the description
        $content = $this->modx->lexicon('simplecart.methods.yourorderat');
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setCacheable(false);
        $chunk->setContent($content);
        $description = $chunk->process();

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
                $this->order->save();
                $this->modx->sendRedirect($source['redirect']['url']);
            }
        }

        // If we made it here, either 3DS failed, or 3DS isn't supported.
        // So we do a normal charge.
        return $this->createCharge($sourceId);
    }

    public function verify() {

        if (!$this->initStripe()) {
            return false;
        }

        $chargeId = $this->order->getLog('[Stripe] Charge ID');
        if (empty($chargeId)) {
            $this->order->addLog('[Stripe] Verify Fail', 'No Charge ID set');
            $this->order->save();
            return false;
        }

        try {
            $charge = \Stripe\Charge::retrieve($chargeId);
        } catch (\Exception $e) {
            $this->order->addLog('[Stripe] Verify Fail', $e->getMessage());
            $this->order->save();
            return false;
        }

        if (!$charge) {
            $this->order->addLog('[Stripe] Verify Fail', 'Charge not found');
            $this->order->save();
            return false;
        }

        if ($charge['status'] !== 'succeeded') {
            $this->order->addLog('[Stripe] Verify Fail', 'Charge status: ' . $charge['status']);
            $this->order->save();
            return false;
        }

        return true;
    }

    public function createCharge($sourceId)
    {
        if (!$this->initStripe()) {
            return false;
        }

        // SimpleCart stores totals in decimal values, so we need to turn that into cents for Stripe
        $amount = $this->order->get('total');
        $amount = (int)$amount * 100;

        // Get the currency
        $currency = $this->getProperty('currency', 'EUR');
        $currency = strtolower($currency);

        // Get the description
        $content = $this->modx->lexicon('simplecart.methods.yourorderat');
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setCacheable(false);
        $chunk->setContent($content);
        $description = $chunk->process();

        try {
            $user = $this->modx->user;
            $customerId = '';
            if ($user && $profile = $user->getOne('Profile')) {
                $extended = $profile->get('extended');
                if (!is_array($extended)) {
                    $extended = [];
                }
                if (!empty($extended['stripe_customer_id'])) {
                    $customerId = $extended['stripe_customer_id'];
                }

                /** @var array $address */
                $address = $this->order->getAddress();
                if (is_array($address) && empty($customerId)) {
                    $customer = null;
                    try {
                        $customer = \Stripe\Customer::create([
                            'email' => $address['email'],
                            'description' => $address['firstname'] . ' ' . $address['lastname'],
                            'metadata' => [
                                'MODX User ID' => $user->get('id'),
                                'MODX Username' => $user->get('username'),
                            ]
                        ]);
                    } catch (\Stripe\Error\Base $e) {
                        $this->order->addLog('[Stripe] Customer Error', $e->getMessage());
                        $this->order->save();
                    }

                    if ($customer) {
                        $customerId = $customer['id'];
                        $extended['stripe_customer_id'] = $customerId;
                        $profile->set('extended', $extended);
                        $profile->save();
                    }
                }
            }

            $charge = \Stripe\Charge::create([
                'amount' => $amount, // amount in cents
                'currency' => $currency,
                'source' => $sourceId,
                'customer' => !empty($customerId) ? $customerId : null,
                'description' => $description,
                'metadata' => [
                    'order_nr' => $this->order->get('ordernr'),
                    'order_id' => $this->order->get('id'),
                ],
            ]);
        } catch(\Stripe\Error\Card $e) {
            // The card has been declined
            $this->order->addLog('[Stripe] Error', 'Card Declined: ' . $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();
            return false;
        } catch(\Stripe\Error\Base $e) {
            $this->order->addLog('[Stripe] Error', $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();
            return false;
        } catch(Exception $e) {
            $this->order->addLog('[Stripe] Error', 'Uncaught Exception: ' . $e->getMessage());
            $this->order->set('status', 'payment_failed');
            $this->order->save();
            return false;
        }

        // log the finishing status
        $this->order->addLog('[Stripe] Charge ID', $charge['id']);
//        $this->order->addLog('[Stripe] Card', $charge['source']['brand'] . ' ' . $charge['source']['last4']);
        $this->order->setStatus('finished');
        $this->order->save();
        return true;
    }
}