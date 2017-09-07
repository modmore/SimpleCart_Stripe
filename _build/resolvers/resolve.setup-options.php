<?php

/**
 * @var modX|xPDO $modx
 */
$modx =& $transport->xpdo;
$success = false;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        // load package
        $modelPath = $modx->getOption('simplecart.core_path', null, $modx->getOption('core_path') . 'components/simplecart/') . 'model/';
        $modx->addPackage('simplecart', $modelPath);

        $methods = [];
        /** @var simpleCartMethod $method */
        $method = $modx->getObject('simpleCartMethod', ['name' => 'stripe', 'type' => 'payment']);
        if ($method) {
            $methods[] = $method;
        }
        $methodBancontact = $modx->getObject('simpleCartMethod', ['name' => 'stripebancontact', 'type' => 'payment']);
        if ($methodBancontact) {
            $methods[] = $methodBancontact;
        }
        $methodIdeal = $modx->getObject('simpleCartMethod', ['name' => 'stripeideal', 'type' => 'payment']);
        if ($methodIdeal) {
            $methods[] = $methodIdeal;
        }
		if(count($methods) === 0) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[SimpleCart] Failed to find newly created record for the Stripe payment method');
            return false;
        }

        $configs = array(
            'currency',
            'secret_key',
            'publishable_key',
        );

        foreach ($configs as $key) {
            if (isset($options[$key]) && !empty($options[$key])) {
                /** @var simpleCartMethodProperty $property */
                foreach ($methods as $method) {
                    $property = $modx->getObject('simpleCartMethodProperty', ['method' => $method->get('id'), 'name' => $key]);
                    if ($property instanceof simpleCartMethodProperty) {
                        $property->set('value', $options[$key]);
                        $property->save();
                    }
                }
            }
        }

        $success = true;
        break;

    case xPDOTransport::ACTION_UNINSTALL:

        break;
}

return $success;