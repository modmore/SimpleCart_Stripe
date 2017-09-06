<?php
/* Get the core config */
if (!file_exists(dirname(__DIR__) . '/config.core.php')) {
    die('ERROR: missing '.dirname(__DIR__) . '/config.core.php file defining the MODX core path.');
}

echo "<pre>";
/* Boot up MODX */
echo "Loading modX...\n";
require_once dirname(__DIR__).'/config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
echo "Initializing manager...\n";
$modx->initialize('mgr');
$modx->getService('error','error.modError', '', '');

$componentPath = __DIR__;

$scPath = $modx->getOption('simplecart.core_path', null, MODX_CORE_PATH . 'components/simplecart/');
$SimpleCart = $modx->getService('simplecart','SimpleCart', $scPath . 'model/simplecart/');
if (!($SimpleCart instanceof SimpleCart)) {
    die('Could not load SimpleCart from ' . $scPath);
}

/* Namespace */
if (!createObject('modNamespace',array(
    'name' => 'simplecart_stripe',
    'path' => $componentPath.'/core/components/simplecart_stripe/',
    'assets_path' => $componentPath.'/assets/components/simplecart_stripe/',
),'name', false)) {
    echo "Error creating namespace simplecart_stripe.\n";
}

if (!createObject('modNamespace',array(
    'name' => 'simplecart_stripebancontact',
    'path' => $componentPath.'/core/components/simplecart_stripebancontact/',
    'assets_path' => $componentPath.'/assets/components/simplecart_stripe/',
),'name', false)) {
    echo "Error creating namespace simplecart_stripe.\n";
}

/* Path settings */
if (!createObject('modSystemSetting', array(
    'key' => 'simplecart_stripe.core_path',
    'value' => $componentPath.'/core/components/simplecart_stripe/',
    'xtype' => 'textfield',
    'namespace' => 'simplecart_stripe',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating simplecart_stripe.core_path setting.\n";
}

if (!createObject('modSystemSetting', array(
    'key' => 'simplecart_stripe.assets_path',
    'value' => $componentPath.'/assets/components/simplecart_stripe/',
    'xtype' => 'textfield',
    'namespace' => 'simplecart_stripe',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating simplecart_stripe.assets_path setting.\n";
}
/* Path settings */
if (!createObject('modSystemSetting', array(
    'key' => 'simplecart_stripebancontact.core_path',
    'value' => $componentPath.'/core/components/simplecart_stripebancontact/',
    'xtype' => 'textfield',
    'namespace' => 'simplecart_stripe',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating simplecart_stripe.core_path setting.\n";
}

if (!createObject('modSystemSetting', array(
    'key' => 'simplecart_stripebancontact.assets_path',
    'value' => $componentPath.'/assets/components/simplecart_stripe/',
    'xtype' => 'textfield',
    'namespace' => 'simplecart_stripe',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating simplecart_stripe.assets_path setting.\n";
}

/* Fetch assets url */
$url = 'http';
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
    $url .= 's';
}
$url .= '://'.$_SERVER["SERVER_NAME"];
if ($_SERVER['SERVER_PORT'] != '80') {
    $url .= ':'.$_SERVER['SERVER_PORT'];
}
$requestUri = $_SERVER['REQUEST_URI'];
$bootstrapPos = strpos($requestUri, '_bootstrap/');
$requestUri = rtrim(substr($requestUri, 0, $bootstrapPos), '/').'/';
$assetsUrl = "{$url}{$requestUri}assets/components/simplecart_stripe/";

if (!createObject('modSystemSetting', array(
    'key' => 'simplecart_stripe.assets_url',
    'value' => $assetsUrl,
    'xtype' => 'textfield',
    'namespace' => 'simplecart_stripe',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating simplecart_stripe.assets_url setting.\n";
}

// Stripe base method (card + 3DS card)
if (!createObject('simpleCartMethod', array(
    'name' => 'stripe',
    'price_add' => null,
    'type' => 'payment',
    'sort_order' => $modx->getCount('simpleCartMethod') + 1,
), 'name', false)) {
    echo "Error creating cbHasField snippet.\n";
}
$method = $modx->getObject('simpleCartMethod', ['name' => 'stripe']);
if (!$method) {
    die ('Failed to load or create simplecart_stripe payment method');
}
$methodId = $method->get('id');

$props = array(
    'currency' => 'USD',
    'secret_key' => '',
    'publishable_key' => '',
    'cart_tpl' => 'scStripeCart',
    'cart_footer_tpl' => 'scStripeFooter',
);
foreach ($props as $key => $value) {
    createObject('simpleCartMethodProperty', [
        'method' => $methodId,
        'name' => $key,
        'value' => $value
    ], ['method', 'name'], false);
}

// Stripe Bancontact method
if (!createObject('simpleCartMethod', array(
    'name' => 'stripebancontact',
    'price_add' => null,
    'type' => 'payment',
    'sort_order' => $modx->getCount('simpleCartMethod') + 1,
), 'name', false)) {
    echo "Error creating cbHasField snippet.\n";
}
$bancontact = $modx->getObject('simpleCartMethod', ['name' => 'stripebancontact']);
if (!$bancontact) {
    die ('Failed to load or create simplecart_stripebancontact payment method');
}
$bancontactId = $bancontact->get('id');

$bancontactProps = array(
    'currency' => 'EUR',
    'secret_key' => '',
    'cart_tpl' => 'scStripeBancontactCart',
);
foreach ($bancontactProps as $key => $value) {
    createObject('simpleCartMethodProperty', [
        'method' => $bancontactId,
        'name' => $key,
        'value' => $value
    ], ['method', 'name'], false);
}

$category = $modx->getObject('modCategory', ['category' => 'SimpleCart']);
$categoryId = $category instanceof modCategory ? $category->get('id') : 0;
if (!createObject('modChunk', array(
    'name' => 'scStripeCart',
    'static' => true,
    'static_file' => $componentPath.'/core/components/simplecart_stripe/elements/chunks/scstripecart.chunk.tpl',
    'category' => $categoryId,
), 'name', true)) {
    echo "Error creating scStripeCart chunk.\n";
}
if (!createObject('modChunk', array(
    'name' => 'scStripeFooter',
    'static' => true,
    'static_file' => $componentPath.'/core/components/simplecart_stripe/elements/chunks/scstripefooter.chunk.tpl',
    'category' => $categoryId,
), 'name', true)) {
    echo "Error creating scStripeCart chunk.\n";
}
if (!createObject('modChunk', array(
    'name' => 'scStripeBancontactCart',
    'static' => true,
    'static_file' => $componentPath.'/core/components/simplecart_stripe/elements/chunks/scstripebancontactcart.chunk.tpl',
    'category' => $categoryId,
), 'name', true)) {
    echo "Error creating scStripeBancontactCart chunk.\n";
}

// Refresh the cache
$modx->cacheManager->refresh();

echo "Done.";

/**
 * Creates an object.
 *
 * @param string $className
 * @param array $data
 * @param string $primaryField
 * @param bool $update
 * @return bool
 */
function createObject ($className = '', array $data = array(), $primaryField = '', $update = true) {
    global $modx;
    /* @var xPDOObject $object */
    $object = null;

    /* Attempt to get the existing object */
    if (!empty($primaryField)) {
        if (is_array($primaryField)) {
            $condition = array();
            foreach ($primaryField as $key) {
                $condition[$key] = $data[$key];
            }
        }
        else {
            $condition = array($primaryField => $data[$primaryField]);
        }
        $object = $modx->getObject($className, $condition);
        if ($object instanceof $className) {
            if ($update) {
                $object->fromArray($data);
                return $object->save();
            } else {
                $condition = $modx->toJSON($condition);
                echo "Skipping {$className} {$condition}: already exists.\n";
                return true;
            }
        }
    }

    /* Create new object if it doesn't exist */
    if (!$object) {
        $object = $modx->newObject($className);
        $object->fromArray($data, '', true);
        return $object->save();
    }

    return false;
}
