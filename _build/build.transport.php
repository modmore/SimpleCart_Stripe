<?php

$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];

if (!defined('MOREPROVIDER_BUILD')) {
    /* define version */
    define('PKG_NAME', 'SimpleCart Stripe');
    define('PKG_NAMESPACE', 'simplecart_stripe');
    define('PKG_VERSION', '2.0.0');
    define('PKG_RELEASE', 'dev2');

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
    'lexicon' => $root . 'core/components/' . PKG_NAMESPACE . '/lexicon/',
    'docs' => $root . 'core/components/' . PKG_NAMESPACE . '/docs/',
    'source_assets' => $root . 'assets/components/' . PKG_NAMESPACE . '/',
    'source_core' => $root . 'core/components/' . PKG_NAMESPACE . '/',
    'source_core_root' => $root . 'core/components/',
    'chunks' => $root . 'core/components/' . PKG_NAMESPACE . '/elements/chunks/'
);
unset($root);


$modx->loadClass('transport.modPackageBuilder', '', false, true);

/** @var modPackageBuilder $builder * */
$builder = new modPackageBuilder($modx);
$builder->directory = $targetDirectory;
$builder->createPackage(PKG_NAMESPACE, PKG_VERSION, PKG_RELEASE);

// Namespace 1 = simplecart_stripe
$builder->registerNamespace(PKG_NAMESPACE,false,true,'{core_path}components/'.PKG_NAMESPACE.'/', '{assets_path}components/'.PKG_NAMESPACE.'/');
$builder->registerNamespace('simplecart_stripebancontact',false,true,'{core_path}components/simplecart_stripebancontact/', '{assets_path}components/simplecart_stripebancontact/');
$builder->registerNamespace('simplecart_stripeideal',false,true,'{core_path}components/simplecart_stripeideal/', '{assets_path}components/simplecart_stripeideal/');


/* @var modCategory $category - add category for our component */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);

/* add chunks */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in chunks...');
$chunks = include $sources['data'].'transport.chunks.php';
if (empty($chunks)) $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in chunks.');
if (is_array($chunks)) {
    $category->addMany($chunks);
}

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category,$attr);

$modx->log(modX::LOG_LEVEL_INFO,'Adding in Validators...');
$vehicle->validate('php', array('source' => $sources['validators'] . 'preinstall.script.php'));
$builder->putVehicle($vehicle);



/* pack in system settings */
$modx->log(modX::LOG_LEVEL_INFO,'Adding in System settings...');
$settings = include $sources['data'].'transport.settings.php';
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
foreach($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
unset($settings, $setting, $attributes);



// Add the validator to check server requirements
$vehicle->validate('php', array('source' => $sources['validators'] . 'requirements.script.php'));

// Add file resolvers
$modx->log(modX::LOG_LEVEL_INFO, 'Adding core/assets file resolvers to category...');
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core_root'] . 'simplecart_stripebancontact/',
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core_root'] . 'simplecart_stripeideal/',
    'target' => "return MODX_CORE_PATH . 'components/';",
));


$modx->log(modX::LOG_LEVEL_INFO, 'Adding other resolvers...');

$vehicle->resolve('php', array('source' => $sources['resolvers'].'resolve.records.php'));
$vehicle->resolve('php', array('source' => $sources['resolvers'].'resolve.setup-options.php'));

// Put it in
$builder->putVehicle($vehicle);
unset($vehicle);


/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['source_core'] . '/docs/license.txt'),
    'readme' => file_get_contents($sources['source_core'] . '/docs/readme.txt'),
    'changelog' => file_get_contents($sources['source_core'] . '/docs/changelog.txt'),
    'setup-options' => array(
        'source' => $sources['install_options'] . 'input.options.php',
    ),
    'requires' => array(
        'simplecart' => '>=2.5.0',
    )
));


$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...'); flush();
$builder->pack();


$tend = explode(" ", microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));

$modx->log(modX::LOG_LEVEL_INFO, "Package Built. Execution time: {$totalTime}\n");
$modx->log(modX::LOG_LEVEL_INFO, "\n-----------------------------\n".PKG_NAME . ' ' . PKG_VERSION.'-'.PKG_RELEASE." built\n-----------------------------");
