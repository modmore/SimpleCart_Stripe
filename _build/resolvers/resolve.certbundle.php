<?php
/* @var modX $modx */

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;

            $corePath = $modx->getOption('core_path') . 'components/simplecart_stripe/';

            $certs = file_get_contents('https://curl.haxx.se/ca/cacert.pem');
            if (!empty($certs)) {
                if (false !== file_put_contents($corePath . 'vendor/stripe/stripe-php/data/ca-certificates.crt', $certs)) {
                    $modx->log(modX::LOG_LEVEL_INFO, 'Updated tls root certificates');
                }
                else {
                    $modx->log(modX::LOG_LEVEL_WARN, 'Could not write updated tls root certificates');
                }
            }
            else {
                $modx->log(modX::LOG_LEVEL_WARN, 'Could not download latest tls root certificates');
            }

            break;
    }

}
return true;

