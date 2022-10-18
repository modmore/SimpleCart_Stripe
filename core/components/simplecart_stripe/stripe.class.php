<?php

use Stripe\PaymentIntent;

require_once __DIR__ . '/shared.class.php';

class SimpleCartStripePaymentGateway extends SimpleCartStripeShared
{
    public function view() {
        $this->initStripe();
        $tpl = $this->getProperty('cart_tpl', 'scStripeCart');
        $footerTpl = $this->getProperty('cart_footer_tpl', 'scStripeFooter');
        $phs = array(
            'publishable_key' => addslashes($this->getProperty('publishable_key', 'ENTER YOUR KEY')),
            'method_id' => $this->method->get('id'),
            'intent_secret' => $this->getPaymentIntent(true)->client_secret,
        );

        $head = $this->simplecart->getChunk($footerTpl, $phs);
        $this->modx->regClientHTMLBlock($head);

        return $this->simplecart->getChunk($tpl, $phs);
    }

    public function getPaymentIntent($view = false)
    {
        // If we have an existing intent, use it
        $id = $_SESSION['stripe_payment_intent'];
        if (empty($id) && $this->order) {
            $id = $this->order->getLog('[Stripe] Payment Intent');
        }

        $intent = null;
        if ($id) {
            $intent = PaymentIntent::retrieve($id);
        }

        $intentData = [
            'currency' => $this->getProperty('currency', 'EUR'),
            'description' => $this->modx->getOption('site_name'),
            'metadata' => [
//                'order_id' => $order->get('id'),
//                'order_reference' => $order->get('reference') ?: 'n/a',
//                'user_id' => (int)$order->get('user'),
//                'context' => $order->get('context'),
            ]
        ];
        if ($this->order instanceof simpleCartOrder) {
            $this->modx->lexicon->load('simplecart_stripe:default');
            $intentData['description'] = $this->modx->lexicon('simplecart_stripe.payment_desc', [
                'ordernr' => $this->order->get('ordernr'),
                'site_name' => $this->modx->getOption('site_name'),
            ]);
            $intentData['metadata']['order_id'] = $this->order->get('id');
            $intentData['metadata']['order_nr'] = $this->order->get('ordernr');
        }
        if (($user = $this->modx->getUser()) && ($user->get('id') > 0)) {
            $intentData['metadata']['user_id'] = $user->get('id');
        }

//        if ($customer = $this->getCustomer($order)) {
//            $intentData['customer'] = $customer->id;
//        }

        $intentData['amount'] = round(($this->method->cartTotal * 100) + ($this->method->priceAdd * 100));

        // Create the intent if it doesn't exist or was cancelled
        if (!$intent || $intent->status === 'canceled' || ($view && $intent->status === 'succeeded')) {
            $intent = PaymentIntent::create($intentData);
        }
        // Update description and metadata on success
        elseif ($intent->status === 'succeeded') {
            PaymentIntent::update($id, [
                'description' => $intentData['description'],
                'metadata' => $intentData['metadata']
            ]);
        }
        // Make sure all info is up-to-date otherwise
        else {
            PaymentIntent::update($id, $intentData);
        }

        // Store the intent ID in session for later access
        $_SESSION['stripe_payment_intent'] = $intent->id;

        // And in the order when available
        if ($this->order) {
            $this->order->addLog('[Stripe] Payment Intent', $intent->id, true);
        }

        return $intent;
    }

    public function submit() {
        if (!$this->initStripe()) {
            return false;
        }

        $intent = $this->getPaymentIntent();
        if (!$intent) {
            return false;
        }

        if ($intent->status === 'requires_confirmation') {
            $this->order->addLog('[Stripe] Payment Intent RQ', 'Attempting to confirm payment intent');
            $this->modx->log(2, 'Payment Intent status is requires_confirmation; attempting to confirm ' . $intent->id . ' with parameters: ' . $intent->serializeParameters());
            $intent->confirm();
        }

        $this->order->addLog('[Stripe] Submit Status', $intent->status);

        return $intent->status === 'succeeded';
    }

    public function verify()
    {
        $this->initStripe();

        $intent = $this->getPaymentIntent();
        if ($intent && $intent->status === 'succeeded') {
            $this->order->addLog('[Stripe] Verify Status', $intent->status);
            $this->order->setStatus('finished');
            $this->order->save();
            return true;
        }
        $this->order->addLog('[Stripe] Verify Status', $intent ? $intent->status : 'Intent not found');
        $this->order->setStatus('payment_failed');
        $this->order->save();
        return false;
    }
}
