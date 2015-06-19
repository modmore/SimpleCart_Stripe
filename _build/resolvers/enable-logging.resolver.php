<?php

/** @var modX|xPDO $modx */
$modx =& $transport->xpdo;
$success = false;

switch($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UNINSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);

        $success = true;
        break;
}

return $success;