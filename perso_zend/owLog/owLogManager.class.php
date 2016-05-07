<?php

/**
 * 
 * classe statique permettant de logger des messages dans des fichiers depuis des classes de modele
 * @author FFreyssenge
 * @uses fw Zend 1.12.17 : Zend_Log + plusieurs classes du fw Zend
 * 
 * le plus simple pour garantir l'inclusion de toutes les dependances est 
 * ... d'utiliser l'autoloading de Zend
 * // ex d'autoloading du Zend framework
 * $zend_lib = "C:\wamp\lib\zend\library\\";
 * set_include_path(get_include_path() . PATH_SEPARATOR . $zend_lib);
 * require_once $zend_lib.'Zend\Loader\Autoloader.php';
 * $autoloader = Zend_Loader_Autoloader::getInstance();
 * 
 * // ex d'utilisation de owLog
 * // dans fichier de configuration
 * owLogManager::init(array <$config>); // @see owLog::init() def pour le format de $config
 * // a l'intérieur du modèle
 *  * //... lors de l'initialisation + pr�fix� par $prefix
 * <owLog> $oLog = owLogManager::setPrefix($prefix);
 * 
 * owLog::log($msg1[, Zend_Log::<priority>]);
 * owLog::logAll($msg1, [$msg2, ..., ][, Zend_Log::<priority>]);
 */
class owLogManager {

	static protected
		$loggers = NULL,
		$config = array(
		    'suffix' => 'ow.log',
		    'level'  => Zend_Log::DEBUG
		),
		$default = array(
		    'default' => array(
			'writerName' => 'Stream',
			'formatterName' => 'Simple',
			'formatterParams' => array(
			    'format' => '%timestamp%: %message% -- %priorityName% (%priority%)' . "\n\r",
			),
		    ),
	);

	/**
	 * init() obligatoire avant tout appel � self::log() ou self::logAll()
	 *
	 * @param array $config : 
	 * 'path_to_logs'	: mandatory => folder where all logs will be stored
	 * 'suffix' 		: optional => suffix fo all log filenames (ie : ow.log) 
	 * 'level'		: optional => level of logging
	 * 
	 * @throws Zend_Log_Exception
	 */
	static public function init(array $config) {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		if (!array_key_exists('path_to_logs', $config)) {
			throw new Zend_Log_Exception('At least one "path_to_logs" writer must be defined');
		}
		$sPathToLog = $config['path_to_logs'];
		if (!is_dir($sPathToLog)) {
			throw new Exception('path unknown : ' . $sPathToLog);	
		}
		if (is_array(self::$config) && array_key_exists('path_to_logs', self::$config)) {
			throw new Exception('only one call to owLogManager::init() allowed');
		}
		self::$config = array_merge(self::$config, $config);
		if ($db) {
			var_dump($origin
				, 'got', $config
				, 'init', self::$config
			);
		}
	}

	static protected function _checkInit() {
		if (!array_key_exists('path_to_logs', self::$config)) {
			throw new Zend_Log_Exception('At least one "path_to_logs" must be defined');
		}
	}

	/**
	 * 
	 * creates a new file : $prefix + 
	 * @param unknown_type $prefix
	 */
	static public function setPrefix($prefix) {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		self::_checkInit();
		$ret = null;
		do {
			if (is_array(self::$loggers) && array_key_exists($prefix, self::$loggers)) {
				$ret = self::$loggers[$prefix];
				break;
			}
			$ret = self::$loggers[$prefix] = self::instantiateLogger($prefix);
		} while (false);
		return $ret;
	}

	static public function instantiateLogger($prefix) {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		$d = self::$default;

		$sFilename = self::makeFileName($prefix);
		$d['default']['writerParams']['stream'] = $sFilename;
		$d['className'] = 'owLog';
		if ($db) {
			var_dump($origin
				, 'default config', $d
			);
			exit($origin);
		}
		self::$loggers[$prefix] = owLog::factory($d);
		self::$loggers[$prefix]->setTimestampFormat('d/m/Y his');
		$filtre = new Zend_Log_Filter_Priority(self::$config['level']);
		self::$loggers[$prefix]->addFilter($filtre);
		return self::$loggers[$prefix];
	}

	static protected function makeFilename($prefix) {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		self::_checkInit();
		$end = self::$config['suffix'];
		$path = self::$config['path_to_logs'];
		$add = $prefix . '_';

		$filename = $path . $add . $end;

		if ($db) {
			var_dump($origin, self::$config, $prefix, $filename);
		}
		return $filename;
	}

}
