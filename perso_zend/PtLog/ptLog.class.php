<?php
/**
 * 
 * classe statique permettant de logger des messages depuis des classes de modèle
 * @author FFreyssenge
 * @uses Zend_Log + plusieurs classes du fw Zend
 * 
 * le plus simple pour garantier l'inclusion de toutes les dépendances est 
 * ... d'utiliser l'autoloading de Zend
 * // ex d'autoloading du Zend framework
 * $zend_lib = "C:\wamp\lib\zend\library\\";
 * set_include_path(get_include_path() . PATH_SEPARATOR . $zend_lib);
 * require_once $zend_lib.'Zend\Loader\Autoloader.php';
 * $autoloader = Zend_Loader_Autoloader::getInstance();
 * 
 * // ex d'utilisation de ptLog
 * // dans fichier de configuration
 * ptLog::init(array <$config>); // @see ptLog::init() def pour le format de $config
 * // à l'intérieur du modèle
 * ptLog::log($msg1[, Zend_Log::<priority>]);
 * ptLog::logAll($msg1, [$msg2, ..., ][, Zend_Log::<priority>]);
 * // change de fichier : le nouveau fichier reprend le nom du fichier défini 
 * //... lors de l'initialisation + préfixé par $prefix
 * ptLog::setPrefix($prefix);
 * // retourne au fichier log sans $prefix
 * ptLog::setPrefix();
 *  * // stoppe tout logging
 * ptLog::clickOff();
 * // ré-active le logging
 * ptLog::clickOn();
 */
class ptLog
{
	static protected
	$loggers = NULL,
	$default_priority = Zend_Log::NOTICE,
	$config           = NULL,
	$log     = TRUE,
	$prefix  = NULL;

	/**
	 *
	 * init() obligatoire avant tout appel à self::log() ou self::logAll()
	 *
	 * @param array $config : @see http://framework.zend.com/manual/fr/zend.log.factory.html
	 * @throws Zend_Log_Exception
	 */
	static public function init(array $config)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$default = array(
		'default' => array(
        'writerName'   => 'Stream',
        'formatterName' => 'Simple',
        'formatterParams' => array(
            'format'   => '%timestamp%: %message% -- %priorityName% (%priority%)'."\r",
		),
		),
		);
		if (!array_key_exists('default', $config) ) {
			throw new Zend_Log_Exception ('At least one "default" writer must be defined');
		}
		$merged = array_replace_recursive ($default, $config);
		if ($db) { var_dump($default, $config, $merged); }
		self::$loggers = Zend_Log::factory($merged);
		self::$loggers->setTimestampFormat('d/m/Y');
		self::$config  = $merged;
	}

	static public function log($msg, $priority = NULL)
	{
		if (!self::$log) {
			return;
		}
		self::_checkInit();
		$p = self::$default_priority;
		if (self::isValidPriority($priority) ) {
			$p = strtoupper($priority);
		}
		// var_dump('PRIORITE', $p);
		$loggers = self::$loggers;

		if (!self::$loggers instanceof Zend_Log) {
			throw new Zend_Log_Exception ('Loggers must inherit Zend_Log');
		}
		if (is_array($msg) ) {
			$msg = print_r($msg, true);
		}
		$loggers->log($msg, $p);
	}

	static protected function _checkInit()
	{
		if (is_null(self::$loggers) ) {
			throw new Zend_Log_Exception(__CLASS__ . ' not initialized : please use init()');
		}
		if (!array_key_exists('default', self::$config) ) {
			throw new Zend_Log_Exception ('At least one "default" writer must be defined');
		}
	}

	/**
	 * ecrit tous les arguments passes (nb variable)
	 * ! si le dernier arg est une priorite Zend_Log valide, tous les autres arguments
	 * seront passes avec cette priorite !
	 */
	static public function logAll()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$lines = func_get_args();
		if ($db) {
			var_dump($origin, 'LINES', $lines, 'COUNT', count($lines) );
		}
		if (1 > count($lines) ) { // aucun argument !
			return null;
		}
		$tab = array();
		$last = count($lines)-1;

		$priorities = self::getPriorities();
		// var_dump('prio', $lines[$last]);
		$p = NULL; $priority = $lines[$last];
		if (self::isValidPriority($priority) ) {
			$p = $lines[$last];
			unset($lines[$last]);
		}
		foreach ($lines AS $v) {
			self::log($v, $p);
		}
	}

	static public function clickOff()
	{
		self::click(false);
	}

	static public function clickOn()
	{
		self::click(true);
	}

	/**
	 * 
	 * creates a new file : $prefix + 
	 * @param unknown_type $prefix
	 */
	static public function setPrefix($prefix = null)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		self::_checkInit();
		$d = self::$config['default'];
		if ('Stream' == $d['writerName']) {
			$stream = $d['writerParams']['stream'];
			$path   = dirname($stream).DIRECTORY_SEPARATOR;
			$filename = basename($stream);
			if (!is_null(self::$prefix) ) {
				// strip current prefix
				$current_prefix = self::$prefix;
				$filename = substr($filename, strlen($current_prefix.'_') );
			}
			if (is_null($prefix) ) {
				$add = '';
			} else {
				$add = $prefix . '_';
			}
			$prefixed = $path . $add .$filename;
			if ($db) {
				var_dump($origin, $d, $prefixed);
			}
			self::$config['default']['writerParams']['stream'] = $prefixed;
			self::init(self::$config);
			self::$prefix = $prefix;
			return $prefixed;
		} else if ($db) {
				var_dump($origin, 'NO STREAM !', $d, $prefix);
		}
		return NULL;
	}
	
	/**
	 * Change format (formatage de chaque ligne de log)
	 *
	 * @see http://framework.zend.com/manual/fr/zend.log.factory.html
	 * @param string $sFormat
	 */
	static public function setFormat($sFormat = null, $writer = 'default')
	{
		self::_checkInit();
		
		$aConfig = self::$config[$writer];

		$sCurrentFormat = $aConfig['formatterParams']['format'];
		
		if ($sCurrentFormat != $sFormat && !is_null($sFormat)) {
			self::$config[$writer]['formatterParams']['format'] = $sFormat;
			self::init(self::$config);
		}
		
		return $sFormat;
	}
	
	/**
	 * Change stream : le chemin absolu + nom du fichier, dans lequel les logs seront
	 *... enregistres
	 * 
	 * @param string $sStream
	 */
	static public function setStream($sStream = null, $writer = 'default')
	{
		self::_checkInit();
		
		$aConfig = self::$config[$writer];
		
		if ('Stream' == $aConfig['writerName']) {
			$sCurrentStream = $aConfig['writerParams']['stream'];
			
			if ($sCurrentStream != $sStream && !is_null($sStream)) {
				self::$config[$writer]['writerParams']['stream'] = $sStream;
				self::init(self::$config);
			}
		}
		
		return $sStream;
	}

	/**
	 * allows to change property of desired writer ('default' by default)
	 * presently, 2 props can be changed : 'stream' (file where log is output) + 'format' (of each log line)
	 *
	 * @throws Zend_Log_Exception : if $prop unknown !
	 */
	static public function change($prop, $val, $writer = 'default')
	{
		$prop = strtolower($prop);
		switch ($prop) {
			case 'stream':
			self::setStream($val, $writer);
			break;
			case 'format':
			self::setFormat($val, $writer);
			break;
			default:
				throw new Zend_Log_Exception (sprintf('[%s] Unknown property : %s', __CLASS__.'::'.__FUNCTION__, $prop) );
			break;
		} 
	}

	/*** PROTECTED ***/

	static protected function click($bool)
	{
		self::$log = $bool;
	}

	static protected function getPriorities()
	{
		$Zl = new Zend_Log();
		$r = new ReflectionClass($Zl);
		return array_flip($r->getConstants());
	}
	
	static protected function isValidPriority($priority)
	{
		$ret = false;
		$priorities = self::getPriorities();
		if ((is_string($priority) OR is_int($priority) ) AND array_key_exists($priority, $priorities) ) 
		{
			$ret = true;
		}
		return $ret;
	}
}

/**
 * array_replace_recursive >= php 5.3
 */
if (!function_exists('array_replace_recursive'))
{
	function recurse($array, $array1)
	{
		foreach ($array1 as $key => $value)
		{
			// create new key in $array, if it is empty or not an array
			if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key])))
			{
				$array[$key] = array();
			}

			// overwrite the value in the base array
			if (is_array($value))
			{
				$value = recurse($array[$key], $value);
			}
			$array[$key] = $value;
		}
		return $array;
	}
	
	function array_replace_recursive($array, $array1)
	{
		// handle the arguments, merge one by one
		$args = func_get_args();
		$array = $args[0];
		if (!is_array($array))
		{
			return $array;
		}
		for ($i = 1; $i < count($args); $i++)
		{
			if (is_array($args[$i]))
			{
				$array = recurse($array, $args[$i]);
			}
		}
		return $array;
	}
}