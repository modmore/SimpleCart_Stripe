<?php

/** @var modX|xPDO $modx */
$modx =& $object->xpdo;
$success = true;

switch($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        $modx->log(modX::LOG_LEVEL_INFO, 'Searching for SimpleCart...');

        /** @var modTransportPackage $simplecart */
        $simplecart = $modx->getObject('transport.modTransportPackage', array('package_name' => 'SimpleCart', 'installed:IS NOT' => null));
        if(empty($simplecart) || !is_object($simplecart)) {

            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Cannot continue installing: Please install the SimpleCart\'s core package first...');
            $success = false;
        }

        break;

    case xPDOTransport::ACTION_UNINSTALL:
        // nothing yet
        break;
}

return $success;