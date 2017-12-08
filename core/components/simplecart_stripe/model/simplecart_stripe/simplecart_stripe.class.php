<?php

class simplecart_stripe
{
    /** Version indexes **/
	public $version_major = '2';
	public $version_minor = '0';
	public $version_patch = '0';
	public $version_release = 'rc';
	public $version_index = '1';

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

    /**
	 * Returns the wanted version info
	 * @param string $type Of the wanted version
	 * @param string $separator Of the version keys
	 * @return string|boolean false
	 */
	public function getVersion($type='full', $separator='.') {
		switch($type) {
			case 'version': return $this->version_major.$separator.$this->version_minor.$separator.$this->version_patch; break;
			case 'major': return $this->version_major; break;
			case 'minor': return $this->version_minor; break;
			case 'patch': return $this->version_patch; break;
			case 'release': return $this->version_release; break;
			case 'index': return $this->version_index; break;

            case 'array': return array(
                'version_major' => $this->version_major,
                'version_minor' => $this->version_minor,
                'version_patch' => $this->version_patch,
                'release' => $this->version_release,
                'release_index' => $this->version_index,
            ); break;

			case 'full':
			default:
				return $this->version_major.$separator.$this->version_minor.$separator.$this->version_patch.'-'.$this->version_release.$this->version_index;
			break;
		}
	}
}