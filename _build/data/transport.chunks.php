<?php

$list = array(
	'scStripeCart' => 'Used for the Stripe credit card fields on the checkout form.',
	'scStripeFooter' => 'Used to register the Stripe JavaScript code to turn the credit card info into a safe token',
    'scStripeIdealCart' => 'Used for the Stripe credit card fields on the checkout form.',
);

$chunks = array();

$i = 1;
foreach($list as $chunk => $description) {
	
	$modx->log(modX::LOG_LEVEL_INFO, 'Adding chunk '.$chunk.'...');

    // determine chunkfile and get contents
    $chunkFile = $sources['chunks'].strtolower($chunk).'.chunk.tpl';
    if(!file_exists($chunkFile)) { $chunkFile = $sources['chunks'].strtolower($chunk).'.tpl'; }
    $contents = file_exists($chunkFile) ? file_get_contents($chunkFile) : '';

    $chunks[$i]= $modx->newObject('modChunk');
	$chunks[$i]->fromArray(array(
		'id' => 0,
		'name' => $chunk,
		'description' => $description,
		'snippet' => $contents,
		'properties' => '',
        'locked' => true,
	), '', true, true);
	
	$i++;
}

return $chunks;