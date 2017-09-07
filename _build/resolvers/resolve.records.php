<?php
if (!function_exists('createSimpleCartMethodProperty')) {
    function createSimpleCartMethodProperty (modX $modx, simpleCartMethod $method, $key, $value) {

        $property = $modx->getObject('simpleCartMethodProperty', array('method' => $method->get('id'), 'name' => $key));
        if (empty($property)) {
            $modx->log(modX::LOG_LEVEL_INFO, '... Creating "' . $key . '" property for ' . $method->get('name') . ' method');
            $property = $modx->newObject('simpleCartMethodProperty');
            $property->set('method', $method->get('id'));
            $property->set('name', $key);
            $property->set('value', $value);
            $property->save();
        }
    }
}
/** @var modX|xPDO $modx */
$modx =& $transport->xpdo;
$success = false;

// load package
$modelPath = $modx->getOption('simplecart.core_path', null, $modx->getOption('core_path') . 'components/simplecart/') . 'model/';
$modx->addPackage('simplecart', $modelPath);

switch($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        $modx->log(modX::LOG_LEVEL_INFO, 'Creating payment gateway records...');

        // get count for next sort
        $count = $modx->getCount('simpleCartMethod', array('type' => 'payment'));
        $properties = array();

        $modx->log(modX::LOG_LEVEL_INFO, 'Currently ' . $count .' method(s) installed...');

        // create Stripe payment method
		$methodStripe = $modx->getObject('simpleCartMethod', array('name' => 'stripe', 'type' => 'payment'));
		if(empty($methodStripe) || !is_object($methodStripe)) {

            $modx->log(modX::LOG_LEVEL_INFO, '... Creating Stripe records');

			$methodStripe = $modx->newObject('simpleCartMethod');
			$methodStripe->set('name', 'stripe');
			$methodStripe->set('price_add', null);
			$methodStripe->set('type', 'payment');
			$methodStripe->set('sort_order', ($count+2));
			$methodStripe->set('ignorefree', false);
			$methodStripe->set('allowremarks', false);
			$methodStripe->set('default', false);
			$methodStripe->set('active', false);
            $methodStripe->save();
		}

		createSimpleCartMethodProperty($modx, $methodStripe, 'currency', 'USD');
		createSimpleCartMethodProperty($modx, $methodStripe, 'secret_key', '');
		createSimpleCartMethodProperty($modx, $methodStripe, 'publishable_key', '');
		createSimpleCartMethodProperty($modx, $methodStripe, 'cart_tpl', 'scStripeCart');
		createSimpleCartMethodProperty($modx, $methodStripe, 'cart_footer_tpl', 'scStripeFooter');
		createSimpleCartMethodProperty($modx, $methodStripe, 'use_3ds_if_optional', '1');

        // create Bancontact payment method
		$methodBancontact = $modx->getObject('simpleCartMethod', array('name' => 'stripebancontact', 'type' => 'payment'));
		if(empty($methodBancontact) || !is_object($methodBancontact)) {

            $modx->log(modX::LOG_LEVEL_INFO, '... Creating Bancontact records');

			$methodBancontact = $modx->newObject('simpleCartMethod');
			$methodBancontact->set('name', 'stripebancontact');
			$methodBancontact->set('price_add', null);
			$methodBancontact->set('type', 'payment');
			$methodBancontact->set('sort_order', ($count+4));
			$methodBancontact->set('ignorefree', false);
			$methodBancontact->set('allowremarks', false);
			$methodBancontact->set('default', false);
			$methodBancontact->set('active', false);
            $methodBancontact->save();
		}

		createSimpleCartMethodProperty($modx, $methodBancontact, 'currency', 'USD');
		createSimpleCartMethodProperty($modx, $methodBancontact, 'secret_key', '');

        // create Ideal payment method
		$methodIdeal = $modx->getObject('simpleCartMethod', array('name' => 'stripeideal', 'type' => 'payment'));
		if(empty($methodIdeal) || !is_object($methodIdeal)) {

            $modx->log(modX::LOG_LEVEL_INFO, '... Creating iDeal records');

			$methodIdeal = $modx->newObject('simpleCartMethod');
			$methodIdeal->set('name', 'stripeideal');
			$methodIdeal->set('price_add', null);
			$methodIdeal->set('type', 'payment');
			$methodIdeal->set('sort_order', ($count+6));
			$methodIdeal->set('ignorefree', false);
			$methodIdeal->set('allowremarks', false);
			$methodIdeal->set('default', false);
			$methodIdeal->set('active', false);
            $methodIdeal->save();
		}

		createSimpleCartMethodProperty($modx, $methodIdeal, 'currency', 'USD');
		createSimpleCartMethodProperty($modx, $methodIdeal, 'secret_key', '');
		createSimpleCartMethodProperty($modx, $methodIdeal, 'cart_tpl', 'scStripeIdealCart');

        $success = true;
        break;

    case xPDOTransport::ACTION_UNINSTALL:

        $modx->log(modX::LOG_LEVEL_INFO, 'Remove Stripe method records...');

        /** @var simpleCartMethod $methodStripe */
        $methodStripe = $modx->getObject('simpleCartMethod', array('name' => 'stripe', 'type' => 'payment'));
		if(!empty($methodStripe) || is_object($methodStripe)) {
            $methodStripe->remove();
        }

        /** @var simpleCartMethod $methodBancontact */
        $methodBancontact = $modx->getObject('simpleCartMethod', array('name' => 'stripebancontact', 'type' => 'payment'));
		if(!empty($methodBancontact) || is_object($methodBancontact)) {
            $methodBancontact->remove();
        }
        /** @var simpleCartMethod $methodIdeal */
        $methodIdeal = $modx->getObject('simpleCartMethod', array('name' => 'stripeideal', 'type' => 'payment'));
		if(!empty($methodIdeal) || is_object($methodIdeal)) {
            $methodIdeal->remove();
        }

        $success = true;
        break;
}

return $success;