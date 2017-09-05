<?php
class SimpleCartStripeWebHookProcessor extends modProcessor
{
    public function process() {
        // Retrieve the request's body and parse it as JSON
        $payload = @file_get_contents('php://input');
        $endpoint_secret = $this->modx->getOption('simplecart_stripe.webhook_secret');
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;

        // Load the stripe method classes
        /** @var simpleCartMethod $method */
        $method = $this->modx->getObject('simpleCartMethod', array('name' => 'stripe'));
        if (empty($method) || !($method instanceof simpleCartMethod)) {
            http_response_code(400);
            return json_encode(['processed' => false, 'message' => 'Could not load the base payment method.']);
        }

        /** @var SimpleCartStripePaymentGateway|SimpleCartStripeShared $gateway */
        $gateway = $method->getGateway();
        if (!($gateway instanceof SimpleCartStripeShared)) {
            http_response_code(400);
            return json_encode(['processed' => false, 'message' => 'Failed to load Stripe Payment Gateway instance.']);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            return json_encode(['processed' => false, 'message' => 'Invalid payload']);
        } catch(\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            http_response_code(400);
            return json_encode(['processed' => false, 'message' => 'Invalid signature']);
        }


        $type = $event['type'];
        if (!in_array($type, [
            'source.chargeable',
            'source.failed',
            'source.cancelled'
            ], true)
        ) {
            // Return a 200 response to indicate the hook was received properly to prevent it from looping
            http_response_code(200);
            // But send a response that indicates the event type isn't supported
            return json_encode(['processed' => false, 'message' => 'Unsupported event type, ignoring request.']);
        }

        /** @var SimpleCartStripePaymentGateway gateway */
        $gateway->setProperties($method->getProperties());
        $gateway->initStripe();

        $source = $event['data']['object'];

        $orderId = $source['metadata']['order_id'];
        $orderNr = $source['metadata']['order_nr'];

        /** @var simpleCartOrder $order */
        $order = $this->modx->getObject('simpleCartOrder', array('id' => $orderId, 'ordernr' => $orderNr));
        if (!($order instanceof simpleCartOrder)) {
            http_response_code(400);
            return json_encode(['processed' => false, 'message' => 'Could not load order from source metadata.']);
        }

        $gateway->setOrder($order);

        switch ($type) {
            case 'source.chargeable':
                $value = $order->getLog('[Stripe] Charge ID');
                if (!empty($value)) {
                    http_response_code(200);
                    return json_encode(['processed' => true, 'message' => 'Order already has a charge assigned.']);
                }

                $success = $gateway->createCharge($source['id']);
                if ($success) {
                    http_response_code(200);
                    return json_encode(['processed' => true, 'message' => 'Created charge from source ' . $source['id'] . ' with ID ' . $order->getLog('[Stripe] Charge ID')]);
                }
                else {
                    http_response_code(500);
                    return json_encode(['processed' => false, 'message' => 'Could not create charge from source ' . $source['id'] . ' with ID ' . $order->getLog('[Stripe] Error')]);
                }
                break;

            case 'source.failed':
                $order->addLog('[Stripe] Source Error', 'Failed');
                $order->setStatus('payment_failed');
                $order->save();

                // Acknowledge
                http_response_code(200);
                return json_encode(['processed' => true, 'message' => 'Order marked as failed.']);

            case 'source.cancelled':
                $order->addLog('[Stripe] Source Error', 'Cancelled');
                $order->setStatus('payment_failed');
                $order->save();

                // Acknowledge
                http_response_code(200);
                return json_encode(['processed' => true, 'message' => 'Order marked as cancelled.']);

            default:
                http_response_code(400);
                return json_encode(['processed' => false, 'message' => 'Missing event type in switch']);
        }
    }
}

return 'SimpleCartStripeWebHookProcessor';