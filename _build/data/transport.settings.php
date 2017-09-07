<?php

$settings = array();

$settings['simplecart_stripe.webhook_secret'] = $modx->newObject('modSystemSetting');
$settings['simplecart_stripe.webhook_secret']->fromArray(array(
    'key' => 'simplecart_stripe.webhook_secret',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'simplecart_stripe',
    'area' => 'authentication',
), '', true, true);

return $settings;