<?php
/**
 * SimpleCart Stripe Connector
 *
 * @package simplecart_stripe
 * @author Mark Hamstra <support@modmore.com>
 */

// define the valid web actions
$validWebActions = array(
    'GET' => array(         /* specify valid $_GET actions */
        'webhook', // TEMP
    ),
    'POST' => array(        /* specify valid $_POST actions */
        'webhook',
    )
);

// when valid "web" action, accept it!
$validGetAction = (isset($_GET['action']) && in_array($_GET['action'], $validWebActions['GET'])) ? true : false;
$validPostAction = (isset($_POST['action']) && in_array($_POST['action'], $validWebActions['POST'])) ? true : false;
$validWebAction = ($validGetAction || $validPostAction) ? true : false;

if(isset($_REQUEST['action']) && $validWebAction) {
    @session_cache_limiter('public');
    define('MODX_REQP', false);
    $_REQUEST['ctx'] = 'web';
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('simplecart.core_path', null, $modx->getOption('core_path') . 'components/simplecart/');
require_once $corePath . 'model/simplecart/simplecart.class.php';

$modx->simplecart = new SimpleCart($modx);
$modx->lexicon->load('simplecart:default', 'simplecart:statuses', 'simplecart:methods');

// when valid "web" action, accept it!
if(isset($_REQUEST['action']) && $validWebAction) {
    if ($modx->user->hasSessionContext($modx->context->get('key'))) {
        $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
    } else {
        $_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
        $_SERVER['HTTP_MODAUTH'] = 0;
    }
    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

$corePath = $modx->getOption('simplecart_stripe.core_path', null, $modx->getOption('core_path') . 'components/simplecart_stripe/');
require_once $corePath . 'model/simplecart_stripe/simplecart_stripe.class.php';

$modx->simplecart_stripe = new simplecart_stripe($modx);

// figure out wich path we want to load the action from
$cp = 'processorsPath';
if (isset($_REQUEST['cp']) && !empty($_REQUEST['cp']) && array_key_exists($_REQUEST['cp'], $modx->simplecart->config)) {
    $cp = $_REQUEST['cp'];
}

/* handle request */
$path = $modx->getOption($cp, $modx->simplecart_stripe->config, $corePath . 'processors/') . (($validWebAction) ? 'web/' : '');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));