<?php
/**
 * @uses Zend_Translate
 */
$zend_dir = 'C:\wamp\lib\zend\library';
if (!class_exists('Zend') ) {
	set_include_path($zend_dir.PATH_SEPARATOR.get_include_path() );
	require_once $zend_dir.'\Zend\Loader\Autoloader.php';
	Zend_Loader_Autoloader::getInstance();
}
/**
 * cette classe statique vise a faciliter l'usage de Zend_Translate
 * options par defaut : 
 * - format csv
 * - auto-scan : il suffit d'indiquer le repertoire $dir qui contient les fichiers de traductions
 *... classes par langue pour demarrer
 *
 * @see http://framework.zend.com/manual/fr/zend.translate.html
 */
class ptLang 
{
	static protected 
    	$tr = null;
		
	static public function init($dir, $options = array() )
	{
		$defaults = array(
			'adapter' => 'csv',
			'locale'  => 'fr_FR',
			'content' => $dir,
			'scan'    => Zend_Translate::LOCALE_DIRECTORY,
		);
		$options = array_merge($defaults, $options);
		self::$tr = new Zend_Translate($options);
	}
	
	/**
	 * raccourci pour ptLang::get()->_($code)
	 */
	static public function __($code)
	{
		return self::$tr->_($code);
	}
	
	/**
	 * recupere l'instance de Zend_Translate
	 */
	static public function get()
	{
		if (!self::$tr instanceof Zend_Translate) {
			throw new Exception('ptLang_is_not_initialized');
		}
		return self::$tr;
	}
	
}