<?php

class simplecart_stripebancontact
{
    /**
     * Constructor
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX &$modx, array $config=array()) {
        $this->modx =& $modx;

        $basePath = $this->modx->getOption('simplecart_stripe.core_path', $config, $this->modx->getOption('core_path').'components/simplecart_stripe/');
		$assetsPath = $this->modx->getOption('simplecart_stripe.assets_path', $config, $this->modx->getOption('assets_path').'components/simplecart_stripe/');
        $assetsUrl = $this->modx->getOption('simplecart_stripe.assets_url', $config, $this->modx->getOption('assets_url').'components/simplecart_stripe/');

        $this->config = array_merge(array(
			'basePath' => $basePath,
			'corePath' => $basePath,
			'lexiconPath' => $basePath.'lexicon/',
			'modelPath' => $basePath.'model/',
			'elementsPath' => $basePath.'elements/',
			'chunksPath' => $basePath.'elements/chunks/',
			'assetsPath' => $assetsPath,
			'assetsUrl' => $assetsUrl,

            'jsUrl' => $assetsUrl.'js/',
			'cssUrl' => $assetsUrl.'css/',
			'imgsUrl' => $assetsUrl.'images/',
		), $config);
    }

}