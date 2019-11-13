<?php

require __DIR__ . '/vendor/autoload.php';

class SimpleCartStripeShared extends SimpleCartGateway
{
    /**
     * @return int|mixed
     */
    public function getAmountInCents()
    {
        $amount = $this->order->get('total');
        $amount = (int)($amount * 100);
        return $amount;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->modx->lexicon('simplecart.methods.yourorderat', array(
            'site_name' => $this->modx->getOption('site_name'),
            '+site_name' => $this->modx->getOption('site_name'),
            'site_url' => $this->modx->getOption('site_url'),
            'ordernr' => $this->order->get('ordernr'),
        ));
    }

    public function initStripe() {
        $key = $this->getProperty('secret_key');
        if (empty($key)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to initiate Stripe gateway for SimpleCart: no secret key is set on the payment method.');
            return false;
        }

        \Stripe\Stripe::setApiKey($key);
        \Stripe\Stripe::setAppInfo('SimpleCart/Stripe', (string)$this->simplecart->version, 'https://www.modmore.com/simplecart/');
        return true;
    }

    public function createCharge($sourceId)
    {
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

        try {
            $userId = $this->order->get('user_id');
            $user = $this->modx->getObject('modUser', ['id' => $userId]);

            $customer = null;
            if ($user && $profile = $user->getOne('Profile')) {
                $extended = $profile->get('extended');
                if (!is_array($extended)) {
                    $extended = [];
                }

                if (!empty($extended['stripe_customer_id'])) {
                    $customerId = $extended['stripe_customer_id'];
                    try {
                        $customer = \Stripe\Customer::retrieve($customerId);
                    } catch (\Stripe\Error\Base $e) {
                        $this->order->addLog('[Stripe] Existing Customer Error', $e->getMessage());
                        $this->order->save();
                    }
                }

                /** @var array $address */
                $address = $this->order->getAddress();
                if (is_array($address) && !$customer) {
                    try {
                        $customer = \Stripe\Customer::create([
                            'email' => $address['email'],
                            'description' => $address['firstname'] . ' ' . $address['lastname'] . ' (' . $user->get('username') . ')',
                            'metadata' => [
                                'MODX User ID' => $userId,
                                'MODX Username' => $user->get('username'),
                            ]
                        ]);
                    } catch (\Stripe\Error\Base $e) {
                        $this->order->addLog('[Stripe] Customer Create Error', $e->getMessage());
                        $this->order->save();
                    }
                }
            }

            if (!empty($customer)) {
                $this->order->addLog('[Stripe] Customer ID', $customer->id);
            }

            $charge = \Stripe\Charge::create([
                'amount' => $amount, // amount in cents
                'currency' => $currency,
                'source' => $sourceId,
                'customer' => $customer ? $customer->id : null,
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
        if (!$this->order->get('confirmation_sent')) {
            $this->order->resendConfirmation();
        }
        $this->order->save();
        return true;
    }

    public function verify() {

        if (!$this->initStripe()) {
            return false;
        }

        $chargeId = $this->order->getLog('[Stripe] Charge ID');
        if (empty($chargeId)) {
            if ($this->order->get('async_payment_confirmation')) {
                $this->order->addLog('[Stripe] Verify Fail', 'No Charge ID set yet, source still pending approval.');
            }
            else {
                $this->order->addLog('[Stripe] Verify Fail', 'No Charge ID for order');
            }
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
}