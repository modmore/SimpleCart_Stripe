<?php

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

        // create stripe payment method
		$method = $modx->getObject('simpleCartMethod', array('name' => 'stripe', 'type' => 'payment'));
		if(empty($method) || !is_object($method)) {

            $modx->log(modX::LOG_LEVEL_INFO, '... Creating Stripe records');

			$method = $modx->newObject('simpleCartMethod');
			$method->set('name', 'stripe');
			$method->set('price_add', null);
			$method->set('type', 'payment');
			$method->set('sort_order', ($count+2));
			$method->set('ignorefree', false);
			$method->set('allowremarks', false);
			$method->set('default', false);
			$method->set('active', false);
            $method->save();
		}

        $list = array(
            'currency' => 'USD',
            'secret_key' => '',
            'publishable_key' => '',
            'cart_tpl' => 'scStripeCart',
            'cart_footer_tpl' => 'scStripeFooter',
        );

        foreach ($list as $key => $defaultValue) {

            // add some config records
            $property = $modx->getObject('simpleCartMethodProperty', array('method' => $method->get('id'), 'name' => $key));
            if (empty($property) || !is_object($property)) {

                $modx->log(modX::LOG_LEVEL_INFO, '... Creating "' . $key . '" property for Stripe method');

                $property = $modx->newObject('simpleCartMethodProperty');
                $property->set('method', $method->get('id'));
                $property->set('name', $key);
                $property->set('value', $defaultValue);
                $property->save();
            }
        }

        $chunks = array(
            'scStripeCart',
            'scStripeFooter'
        );

        $categoryId = 0;
        $category = $modx->getObject('modCategory', array('category' => 'SimpleCart'));
        if ($category instanceof modCategory) {
            $categoryId = $category->get('id');
        }
        foreach ($chunks as $name) {
            $chunk = $modx->getObject('modChunk', array('name' => $name));
            if (!$chunk) {
                /** @var modChunk $chunk */
                $chunk = $modx->newObject('modChunk');
                $chunk->fromArray(array(
                    'name' => $name,
                    'description' => 'Part of the Stripe Gateway for SimpleCart',
                    'static' => true,
                    'static_file' => '[[++core_path]]components/simplecart_stripe/elements/chunks/' . strtolower($name) . '.chunk.tpl',
                    'category' => $categoryId,
                ));
                if ($chunk->save()) {
                    $modx->log(modX::LOG_LEVEL_INFO, 'Added ' . $name . ' chunk.');
                }
                else {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not save ' . $name . ' chunk.');
                }
            }
            else {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Chunk ' . $name . ' already exists.');
            }
        }

        $success = true;
        break;

    case xPDOTransport::ACTION_UNINSTALL:

        $modx->log(modX::LOG_LEVEL_INFO, 'Remove Stripe method records...');

        /** @var simpleCartMethod $method */
        $method = $modx->getObject('simpleCartMethod', array('name' => 'stripe', 'type' => 'payment'));
		if(!empty($method) || is_object($method)) {
            $method->remove();
        }

        $success = true;
        break;
}

return $success;