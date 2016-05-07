<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

// cd C:\Users\paco\Documents\perso-google-drive\PROJETS_WEB\objetweb
// php .\phpunit-4.8.phar .\perso_zend\owLog\_test\owLogTest.php
function cmd($msg, $show = true, $stop = false) {
	// BUG : printing out of messages only occurs after ALL fgets(STDIN) have been made !

	do {
		if (!$show)
			break;
		print("\n\r");
		if (is_string($msg)) {
			print($msg);
		} elseif (is_object($msg)) {
			var_dump($msg);
		} else {
			print $s = print_r($msg, true);
		}
		print("\n\r");
	} while (false);
	if ($stop) {
		fgets(STDIN);
	}
}

// bootstrap
date_default_timezone_set('Europe/Paris');
$zend_lib = __DIR__ . '\..\..';
set_include_path(get_include_path() . PATH_SEPARATOR . $zend_lib);
require_once $zend_lib . '\Zend\Loader\Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
require_once dirname(__FILE__) . '/../owLogManager.class.php';
require_once dirname(__FILE__) . '/../owLog.class.php';

class owLogManagerW extends owLogManager {

	// pour permettre de supprimer l'initialisation entre chaque test !
	static public function flush() {
		self::$loggers = NULL;
		unset(self::$config['path_to_logs']);
	}

	static public function getConfig()
	{
		return self::$config;
	}

	static public function makeFilename($prefix)
	{
		return parent::makeFilename($prefix);
	}

	// static public function getPriorities() { return self::getPriorities(); }
//	static public function getLogger()     { return self::$loggers; }
//	static public function isValidPriority ($p) { 
//		return self::isValidPriority($p); 
//	}
}

// exit;
class owLogTest extends PHPUnit_Framework_TestCase {

	protected static
		$d,
		$default_config,
		$files_to_delete = array();

	public static function setUpBeforeClass() {
		$root = dirname(__FILE__);
		$d = array(
		    'path_to_logs' => $root . '\logs\\',
		    'suffix' => '.log',
		);
		self::$d = $d;
		$oFinder = new Finder();
		$oFinder->in($d['path_to_logs']);
		$oFS = new Filesystem();
		$oFS->remove($oFinder);
	}

	public function setUp() {
		
	}

	public function testInit() {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		$pathToLogsDir = self::$d['path_to_logs'];
		$aConfig = array('path_to_logs' => $pathToLogsDir);
		$this->assertTrue(class_exists('Zend_Log'));
		$s = 'hello';
		$sExpectedFile = $pathToLogsDir . DIRECTORY_SEPARATOR . $s . '_ow.log';
		$this->assertFileNotExists($sExpectedFile, sprintf('%s exists', $sExpectedFile));
		owLogManagerW::init($aConfig);
		$oLog = owLogManagerW::setPrefix($s);

		if ($db) {
			var_dump($origin
				, 'got', $oLog
			);
		}
		$this->assertTrue($oLog instanceof owLog, 'Log is initialized : ' . get_class($oLog));
		$this->assertFileNotExists($sExpectedFile, sprintf('No file is created until an actual logging is performed ! (%s)', $sExpectedFile));
//
	}

	public function testLog() {
		$pathToLogsDir = self::$d['path_to_logs'];
		$s = 'bonjour';
		$sExpectedFile = $pathToLogsDir . DIRECTORY_SEPARATOR . $s . '_ow.log';
		$oLog = owLogManagerW::setPrefix($s);
		$oLog->log($s, Zend_Log::EMERG);
		$this->assertFileExists($sExpectedFile, sprintf('%s exists', $sExpectedFile));
		$this->_mustBeIn($s, $sExpectedFile);

		$s = 'au-revoir';
		$prefix = 'bye';
		$oLogAurevoir = owLogManagerW::setPrefix($prefix);
		$sExpectedAurevoirFile = $pathToLogsDir . DIRECTORY_SEPARATOR . $prefix . '_ow.log';
		$oLogAurevoir->log($s, Zend_Log::ALERT);
		$this->assertFileExists($sExpectedAurevoirFile, sprintf('%s exists', $sExpectedAurevoirFile));
		$this->_mustBeIn($s, $sExpectedAurevoirFile);

		$s = 're-bonjour';
		$oLog->log($s, Zend_Log::EMERG);
		$this->_mustBeIn($s, $sExpectedFile);
		$this->_mustBeIn('EMERG', $sExpectedFile);
	}
	public function testLogAll() {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		$pathToLogsDir = self::$d['path_to_logs'];
		$aConfig = array('path_to_logs' => $pathToLogsDir);
		$this->assertTrue(class_exists('Zend_Log'));
		$s = 'helloall';
		$sExpectedFile = $pathToLogsDir . DIRECTORY_SEPARATOR . $s . '_ow.log';

		$oLog = owLogManagerW::setPrefix($s);

		$msg1 = 'ici';
		$msg2 = 'et là';
		$msg_seul = 'seul !-(';
		$this->assertFileNotExists($sExpectedFile, sprintf('%s exists', $sExpectedFile));
		$oLog->logAll($msg_seul);
		$this->_mustBeIn($msg_seul, $sExpectedFile);

		$this->_mustNotBeIn($msg1, $sExpectedFile);
		//
		$oLog->logAll($msg1, $msg2);
		$this->_mustBeIn($msg1, $sExpectedFile);
		$this->_mustBeIn($msg2, $sExpectedFile);
		//
		$oLog->logAll($msg1, Zend_Log::CRIT);
		$this->_mustBeIn('CRIT', $sExpectedFile);
	}

	public function testSetLevel()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		owLogManagerW::flush();
		self::$d['level'] = Zend_Log::CRIT;
		$pathToLogsDir = self::$d['path_to_logs'];
		self::$d['path_to_logs'] = $pathToLogsDir;
		$s = 'level';
		
		owLogManagerW::init(self::$d);
		$sExpectedFile = owLogManagerW::makeFilename($s); // $pathToLogsDir . DIRECTORY_SEPARATOR . $s . '_ow.log';
		$aSetConfig = owLogManagerW::getConfig();
		if ($db) {
			var_dump($origin
				, 'set default config', $aSetConfig
				);
		}
		$oLog = owLogManagerW::setPrefix($s);
		$this->assertFileNotExists($sExpectedFile);

		$msg1 = 'info click ici !';
		$oLog->log($msg1, Zend_Log::INFO);
		$this->assertFileExists($sExpectedFile);	
		$msg1 = 'emerg click ici !';
		$oLog->log($msg1, Zend_Log::EMERG);
		$this->_mustBeIn($msg1, $sExpectedFile);
	}
//

	public function _mustBeIn($str, $file, $must = true) {
		$apres = file_get_contents($file);
		$is = (false !== strpos($apres, $str) );
		$sCond = $must? '' : 'NOT';
		$msg = $str . ' MUST '.$sCond.' BE in ' . $file;
		if ($must) {
			$this->assertTrue($is, $msg);
		} else {
			$this->assertFalse($is, $msg);
		}
	}

	public function _mustNotBeIn($str, $file) {
		$this->_mustBeIn($str, $file, false);
	}

	protected function tearDown() {
		
	}

	public static function tearDownAfterClass() {
		$db = false;
		$origin = __CLASS__ . '::' . __FUNCTION__;
		foreach (self::$d AS $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
		if (count(self::$files_to_delete)) {
			if ($db) {
				var_dump($origin, self::$files_to_delete);
			}
			foreach (self::$files_to_delete AS $f) {
				if ($db) {
					var_dump('trying to delete', $f);
				}
				if (!file_exists($f)) {
					continue;
				}
				unlink($f);
				array_shift(self::$files_to_delete);
			}
		}
	}

	/**/
}
