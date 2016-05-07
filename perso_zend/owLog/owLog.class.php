<?php

/**
 * adds following benefits to Zend_Log : 
 * - no writer is instantiated if no log is performed (in the case 
 * of file writer, it means no empty file is created !)
 * - self::logAll() : can take several loggings at once
 */
class owLog extends Zend_Log {

	protected
		$default_priority = Zend_Log::DEBUG,
		$loggers = NULL;

	/**
	 * @overrides
	 * 
	 * instantiates a logger without adding the writers !
	 * 
	 * Benefit : in case of a file writer, the file is not created until
	 * ... the first call to self::log() !
	 * @see self::log()
	 * 
	 * @param type $config
	 * @return \Zend_Log
	 * @throws Zend_Log_Exception
	 */
	static public function factory($config = array()) {
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		}

		if (!is_array($config) || empty($config)) {
			/** @see Zend_Log_Exception */
			require_once 'Zend/Log/Exception.php';
			throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
		}

		if (array_key_exists('className', $config)) {
			$class = $config['className'];
			unset($config['className']);
		} else {
			$class = __CLASS__;
		}

		$log = new $class;

		if (!$log instanceof Zend_Log) {
			/** @see Zend_Log_Exception */
			require_once 'Zend/Log/Exception.php';
			throw new Zend_Log_Exception('Passed className does not belong to a descendant of Zend_Log');
		}

		if (array_key_exists('timestampFormat', $config)) {
			if (null != $config['timestampFormat'] && '' != $config['timestampFormat']) {
				$log->setTimestampFormat($config['timestampFormat']);
			}
			unset($config['timestampFormat']);
		}

		$log->setConfig($config);

		return $log;
	}

	/**
	 * sole purpose is to allow posponement of writer additions until
	 * ... firts call to self::log()
	 * 
	 * @see self::factory()
	 * 
	 * @param type $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}

	/**
	 * @see self::factory
	 * 
	 * Add the writers
	 */
	protected function initWriters() {
		$config = $this->config;
		if (!is_array(current($config))) {
			$this->addWriter(current($config));
		} else {
			foreach ($config as $writer) {
				$this->addWriter($writer);
			}
		}
	}

	public function log($message, $priority, $extras = null) {
		if (empty($this->_writers)) {
			$this->initWriters();
		}
		if (is_array($message)) {
			$message = print_r($message, true);
		}
		parent::log($message, $priority, $extras);
	}

	/**
	 * ecrit tous les arguments passes (nb variable)
	 * ! si le dernier arg est une priorite Zend_Log valide, tous les autres arguments
	 * seront passes avec cette priorite !
	 */
	public function logAll() {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		$lines = func_get_args();
		if ($db) {
			var_dump($origin, 'LINES', $lines, 'COUNT', count($lines));
		}
		if (1 > count($lines)) { // aucun argument !
			return null;
		}
		$tab = array();
		$last = count($lines) - 1;

		// var_dump('prio', $lines[$last]);
		$p = $this->default_priority;
		$priority = $lines[$last];
		if ($this->isValidPriority($priority)) {
			$p = $lines[$last];
			unset($lines[$last]);
		}
		foreach ($lines AS $v) {
			$this->log($v, $p);
		}
	}

	protected function isValidPriority($priority) {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		if ($db) {
			var_dump($origin
				, 'priorities', $this->_priorities
				, 'got', $priority
			);
		}
		$ret = false;
		$priorities = $this->_priorities;
		if (isset($this->_priorities[$priority])
		// || false !== array_search($name, $this->_priorities) 
		) {
			$ret = true;
		}
		return $ret;
	}

}
