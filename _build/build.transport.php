<?php

$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];

if (!defined('MOREPROVIDER_BUILD')) {
    /* define version */
    define('PKG_NAME', 'SimpleCart Stripe');
    define('PKG_NAMESPACE', 'simplecart_stripe');
    define('PKG_VERSION', '1.0.0');
    define('PKG_RELEASE', 'dev1');

    /* load modx */
    require_once dirname(dirname(__FILE__)) . '/config.core.php';
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx= new modX();
    $modx->initialize('mgr');
    $modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $modx->setLogTarget('ECHO');


    echo '<pre>';
    flush();
    $targetDirectory = dirname(dirname(__FILE__)) . '/_packages/';
}
else {
    $targetDirectory = MOREPROVIDER_BUILD_TARGET;
}

/* define build paths */
$root = dirname(dirname(__FILE__)).'/';

$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'validators' => $root . '_build/validators/',
    'resolvers' => $root . '_build/resolvers/',
    'install_options' => $root . '_build/setup/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER . '/',
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER . '/',
);
unset($root);


$modx->loadClass('transport.xPDOTransport', XPDO_CORE_PATH, true, true);


/** @var xPDOTransport $package */
$package = new xPDOTransport($modx, PKG_NAME_LOWER, $targetDirectory);
$package->signature = PKG_NAME_LOWER . '-' . PKG_VERSION . '-' . PKG_RELEASE;

$modx->log(xPDO::LOG_LEVEL_INFO, 'Transport package for ' . PKG_NAME. ' created.'); flush();

/* include namespace */
$namespace = $modx->newObject('modNamespace');
$namespace->set('name', PKG_NAME_LOWER);
$namespace->set('path', '{core_path}components/' . PKG_NAME_LOWER . '/');
$namespace->set('assets_path', '{assets_path}components/' . PKG_NAME_LOWER . '/');

$attributes = array(
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
);
$attributes['validate'][] = array (
    'type' => 'php',
    'source' => $sources['validators'] . 'preinstall.script.php',
);
$attributes['resolve'][] = array (
    'type' => 'php',
    'source' => $sources['resolvers'] . 'resolve.records.php',
);
$attributes['resolve'][] = array (
    'type' => 'php',
    'source' => $sources['resolvers'] . 'resolve.setup-options.php',
);

$package->put($namespace, $attributes);
unset($namespace);
$modx->log(xPDO::LOG_LEVEL_INFO, 'Namespace "' . PKG_NAME_LOWER . '" packaged.'); flush();


/** @var array $attributes */
$attributes = array(
    'vehicle_class' => 'xPDOFileVehicle',
);
$attributes['validate'][] = array ( /* validators are running before file resolvers below */
    'type' => 'php',
    'source' => $sources['resolvers'] . 'disable-logging.resolver.php',
);
$attributes['resolve'][] = array ( /* and resolvers are running after file resolvers below */
    'type' => 'php',
    'source' => $sources['resolvers'] . 'enable-logging.resolver.php',
);

$files = array();
$files[] = array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
);
$files[] = array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
);

foreach ($files as $fileset) {
    $package->put($fileset, $attributes);
}
unset ($files, $fileset);

$modx->log(xPDO::LOG_LEVEL_INFO, 'Files for "' . PKG_NAME_LOWER . '" packaged.'); flush();

/* now pack in the license file, readme and setup options */
$attributes = array(
    'readme' => file_get_contents($sources['source_core'] . '/docs/readme.txt'),
    'changelog' => file_get_contents($sources['source_core'] . '/docs/changelog.txt'),
    'setup-options' => array(
        'source' => $sources['install_options'] . 'input.options.php',
    )
);
foreach ($attributes as $k => $v) {
    $package->setAttribute($k, $v);
}

/* zip up the package */
$package->pack();


$tend = explode(" ", microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));

$modx->log(modX::LOG_LEVEL_INFO, "Package " . $package->signature . " built. Execution time: {$totalTime}\n");
