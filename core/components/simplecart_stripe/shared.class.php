<?php

require __DIR__ . '/vendor/autoload.php';

class SimpleCartStripeShared extends SimpleCartGateway
{
    protected function initStripe() {
        $key = $this->getProperty('secret_key');
        if (empty($key)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to initiate Stripe gateway for SimpleCart: no secret key is set on the payment method.');
            return false;
        }

        \Stripe\Stripe::setApiKey($key);
        return true;
    }
}