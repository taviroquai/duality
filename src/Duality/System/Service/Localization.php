<?php

/**
 * Localization service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\DualityException;
use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceStorage;
use Duality\System\App;

/**
 * Default localization service
 * 
 * TODO: Add timezone to list
 */
class Localization 
implements InterfaceStorage, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Holds the directory for translations
	 * @var string
	 */
	protected $directory = './data/lang/';

	/**
	 * Holds the messages storage
	 * @var array
	 */
	protected $storage;

	/**
	 * Holds the current iso localization parameters
	 * @var string
	 */
	protected $current;

	/**
	 * Holds the localization options
	 * @var array
	 */
	protected $list = array(
		'en' => array('639-1' => 'en', 'label' => 'English', 	'native' => 'English'),
		'pt' => array('639-1' => 'pt', 'label' => 'Portuguese',	'native' => 'Português'),
		'ab' => array('639-1' => 'ab', 'label' => 'Abkhaz',		'native' => 'аҧсуа бызшәа, аҧсшәа'),
		'aa' => array('639-1' => 'aa', 'label' => 'Afar',		'native' => 'Afaraf'),
		'af' => array('639-1' => 'af', 'label' => 'Afrikaans',	'native' => 'Afrikaans'),
		'sq' => array('639-1' => 'sq', 'label' => 'Albanian',	'native' => 'Shqip'),
		'am' => array('639-1' => 'am', 'label' => 'Amharic',	'native' => 'አማርኛ'),
		'ar' => array('639-1' => 'ar', 'label' => 'Arabic',		'native' => 'العربية'),
		'an' => array('639-1' => 'an', 'label' => 'Aragonese',	'native' => 'Aragonés'),
		'hy' => array('639-1' => 'hy', 'label' => 'Armenian',	'native' => 'Հայերեն'),
		'as' => array('639-1' => 'as', 'label' => 'Assamese',	'native' => 'অসমীয়া'),
		'av' => array('639-1' => 'av', 'label' => 'Avaric',		'native' => 'авар мацӀ'),
		'ae' => array('639-1' => 'ae', 'label' => 'Avestan',	'native' => 'avesta'),
		'ay' => array('639-1' => 'ay', 'label' => 'Aymara',		'native' => 'aymar aru'),
		'az' => array('639-1' => 'az', 'label' => 'Azerbaijani', 'native' => 'azərbaycan dili'),
		'bm' => array('639-1' => 'bm', 'label' => 'Bambara',	'native' => 'bamanankan'),
		'ba' => array('639-1' => 'ba', 'label' => 'Bashkir',	'native' => 'башҡорт теле'),
		'eu' => array('639-1' => 'eu', 'label' => 'Basque',		'native' => 'euskara'),
		'be' => array('639-1' => 'be', 'label' => 'Belarusian',	'native' => 'беларуская мова'),
		'bn' => array('639-1' => 'bn', 'label' => 'Bengali',	'native' => 'বাংলা'),
		'bh' => array('639-1' => 'bh', 'label' => 'Bihari',		'native' => 'भोजपुरी'),
		'bi' => array('639-1' => 'bi', 'label' => 'Bislama',	'native' => 'Bislama')
	);

	/**
	 * Creates a new error handler
	 * @param Duality\System\App
	 */
	public function __construct(App &$app)
	{
		$this->app = & $app;
		$this->storage = array();
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		if (!extension_loaded('intl')) {
			throw new DualityException("Error: intl extension not loaded", 1);
		}
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{

	}

	/**
	 * Loads localizations params
	 * @param string $code
	 */
	public function setLocale($code)
	{
		if (!array_key_exists($code, $this->list)) {
			throw new DualityException("Language code does not exists", 2);
		}
		$directory = $this->directory.DIRECTORY_SEPARATOR.$code;
		if (!is_dir($directory)) {
			throw new DualityException("Translation directory does not exists", 3);
		}
		$this->storage = include($directory.DIRECTORY_SEPARATOR.'messages.php');
	}

	/**
	 * Returns translated string
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, $params = array())
	{
		$string = $this->get($key);
		return vsprintf($string, $params);
	}

	/**
	 * Save item
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value)
	{
		$this->storage[$key] = $value;
	}

	/**
	 * Return item
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->storage[$key]) ? $this->storage[$key] : NULL;
	}

	/**
	 * Reset a session
	 * @return boolean
	 */
	public function reset()
	{
		$this->storage = array();
		return true;
	}
}