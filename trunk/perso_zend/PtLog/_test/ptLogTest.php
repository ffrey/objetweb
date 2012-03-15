<?php
// phpunit C:\PROJETS_WEB\www\publicis_projets\projet_ptLib\LIB\ptLog\_test\ptLogTest.php
// require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Autoload.php';
function cmd($msg, $show = true, $stop = false)
{
  // BUG : printing out of messages only occurs after ALL fgets(STDIN) have been made !
  
  do {
    if (!$show) break;
    print("\n\r");
    if (is_string($msg) )  { print($msg); }
	elseif (is_object($msg) )  { var_dump($msg); }
    else { print $s = print_r($msg, true); }
    print("\n\r");
  } while (false);
  if ($stop) { fgets(STDIN); }
  
}
// bootstrap
$zend_lib = "C:\wamp\lib\zend\library\\";
set_include_path(get_include_path() . PATH_SEPARATOR . $zend_lib);
require_once $zend_lib.'Zend\Loader\Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

require_once dirname(__FILE__).'/../ptLog.class.php';

class ptLogW extends ptLog {
	// pour permettre de supprimer l'initialisation entre chaque test !
	static public function flush()         { self::$loggers = NULL; }
	static public function getPriorities() { return self::getPriorities(); }
	static public function getLogger()     { return self::$loggers; }
	static public function isValidPriority ($p) { 
		return self::isValidPriority($p); 
	}
}

// exit;
class ptLogTest extends PHPUnit_Framework_TestCase
{
	protected static
	$d,
	$default_config,
	$files_to_delete = array();

	public static function setUpBeforeClass()
	{
		$root = dirname(__FILE__);
		$d = array(
    	'logFile'   => $root.'\logs\zend.log',
    	'otherFile' => $root.'\logs\autre_fichier.log',
		);
		self::$d = $d;

		self::$default_config = array(
			'default' => array(
		        'writerParams' => array(
		            'stream'   => $d['logFile'],
		),
		),
		);
	}

	public function setUp()
	{
	}

	public function testInit()
	{
		$logFile = self::$d['logFile'];
		if (is_file($logFile) ) {
			unlink($logFile);
		}
		$log_config = array(
			'default' => array(
		        'writerParams' => array(
		            'stream'   => $logFile,
		),
		),
		);
		$log_config = self::$default_config;
		$this->assertTrue(class_exists('Zend_Log') );
		ptLogW::init($log_config);
		$this->assertTrue(ptLogW::getLogger() instanceof Zend_Log,
		'ptLog is initialized');
		//
		$s = 'hello';
		$this->_mustNotBeIn($s, $logFile);
		ptLogW::log($s, Zend_Log::EMERG);
		ptLogW::log('autre !', Zend_Log::ERR);
		$this->_mustBeIn($s, $logFile);
	}
	
	public function testDeuxWriters()
	{
		$logFile   = self::$d['logFile'];
		$otherFile = self::$d['otherFile'];
		// var_dump($logFile, $otherFile);
		$log_configs = array(
			'default' => array(
		        'writerParams' => array(
		            'stream'   => $logFile,
					),
			),
			'autre_ficher' => array(
		        'writerName'   => 'Stream',
				'writerParams' => array(
		            'stream'   => $otherFile,
				),
			        'formatterName' => 'Simple',
			        'formatterParams' => array(
			            'format'   => '%timestamp%: %message% -- %priorityName% (%priority%)'."\r",
				),
			),
		);
		ptLogW::init($log_configs);
		$s = 'autre hello';
		$this->_mustNotBeIn($s, $otherFile);
		ptLogW::log($s);
		$this->_mustBeIn($s, $logFile);
		$this->_mustBeIn($s, $otherFile);
	}
	//
	public function testLogAll()
	{
		// var_dump(ptLogW::getPriorities() );
		$logFile = self::$d['logFile'];
		$msg1 = 'ici'; $msg2 = 'et là';
		$msg_seul = 'seul !-(';
		ptLogW::init(self::$default_config);
		$this->_mustNotBeIn($msg_seul, $logFile);
		ptLogW::logAll($msg_seul);
		$this->_mustBeIn($msg_seul, $logFile);
		
		$this->_mustNotBeIn($msg1, $logFile);
		//
		ptLogW::logAll($msg1, $msg2);
		$this->_mustBeIn($msg1, $logFile);
		$this->_mustBeIn($msg2, $logFile);
		//
		ptLogW::logAll($msg1, Zend_Log::CRIT);
		$this->_mustBeIn('CRIT', $logFile);
	}

	public function testChange()
	{
		// var_dump(ptLogW::getPriorities() );
		$logFile = self::$d['logFile'];
		$msg1 = 'ici'; $msg2 = 'et là';
		$msg_seul = 'seul !-(';
		ptLogW::init(self::$default_config);
		/*
		 * on veut modifier l'emplacement du fichier de log
		 */
		$root = dirname(__FILE__);
		$nouveau_fichier = $root.'\toto\nouveau_fichier.txt';

		$fichier_existe = file_exists($nouveau_fichier);
		$this->assertFalse($fichier_existe);
		ptLogW::change('stream', $nouveau_fichier);
		ptLogW::logAll($msg1, $msg2);

		$fichier_existe = file_exists($nouveau_fichier);
		$this->assertTrue($fichier_existe);
		$this->_mustBeIn($msg1, $nouveau_fichier);
		cmd('look into ' . $nouveau_fichier, true, true);
		self::$files_to_delete[] = $nouveau_fichier;
	}
	
	public function testLog()
	{
		// var_dump(ptLogW::getPriorities() );
		$logFile = self::$d['logFile'];
		$msg1 = 'essai';
		ptLogW::init(self::$default_config);
		
		ptLogW::log($msg1, array('essai avec un array') );
		$this->_mustBeIn($msg1, $logFile);
		
		
		$msg = 'test avec niveau de priorite';
		ptLogW::log($msg, ZEND_LOG::WARN);
		cmd('look into ' . $logFile, true, true);
		$this->_mustBeIn('WARN', $logFile);
		/**/
		
	}
	
	public function isValidPriority()
	{
		$got = ptLogW::isValidPriority(ZEND_LOG::WARN);
		$this->assertTrue($got);
		
		$got = ptLogW::isValidPriority('WARN');
		$this->assertFalse($got);
	}
	
	//
	public function testClick()
	{
		// var_dump(ptLogW::getPriorities() );
		$logFile = self::$d['logFile'];
		$msg1 = 'click ici !';
		ptLogW::init(self::$default_config);
		$this->_mustNotBeIn($msg1, $logFile);
		//
		ptLogW::clickOff();
		ptLogW::log($msg1);
		$this->_mustBeIn($msg1, $logFile, false);
		ptLogW::clickOn();
		ptLogW::log($msg1);
		$this->_mustBeIn($msg1, $logFile, true);
	}

	public function testSetPrefix()
	{
		$logFile = self::$d['logFile'];
		$msg1 = 'new prefix';
		ptLogW::init(self::$default_config);
		$file = ptLogW::setPrefix('PREFIX');
		ptLog::log($msg1);
		$this->assertTrue(file_exists($file),
		'setPrefix creates a new file : ' . $file);

		self::$files_to_delete[] = $file;
		$this->_mustBeIn($msg1, $file);

		$f = ptLog::setPrefix();
		$this->assertEquals($f, $logFile,
		'setPrefix(empty) reverts to default file');

		$msg2 = 'aprés suppr du préfixe';
		ptLog::log($msg2);
		$logFile = self::$d['logFile'];
		$this->_mustBeIn($msg2, $logFile);
		$this->_mustNotBeIn($msg2, $file);

		$newFile = ptLogW::setPrefix('NEW_PREFIX');
		ptLog::log($msg1);
		$this->assertTrue(file_exists($newFile),
		'setPrefix creates a new file : ' . $newFile);
		$this->_mustBeIn($msg1, $newFile);
		self::$files_to_delete[] = $newFile;
	}

	public function _mustBeIn($str, $file, $must = true)
	{
		$apres = file_get_contents($file);
		$is    = (false !== strpos($apres, $str) );
		$msg   = $str . ' MUST BE in ' . $file;
		if ($must) {
			$this->assertTrue($is, $msg);
		} else {
			$this->assertFalse($is, $msg);
		}
	}

	public function _mustNotBeIn($str, $file)
	{
		$this->_mustBeIn($str, $file, false);
	}

	protected function tearDown()
	{
		// simuler que la classe statique n'est pas initialisée à chaq nouveau test
		// indispensable pour fermer les ressources fichiers à détruire
		ptLogW::flush();
	}

	public static function tearDownAfterClass()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		foreach (self::$d AS $file) {
			if (is_file($file) ) {
				unlink($file);
			}
		}
		if (count(self::$files_to_delete) ) {
			if ($db) {
				var_dump($origin, self::$files_to_delete);
			}
			foreach (self::$files_to_delete AS $f) {
				if ($db) { var_dump('trying to delete', $f); }
				if (!file_exists($f) ) { continue; }
				unlink($f);
				array_shift(self::$files_to_delete);
			}
		}
	}
	/**/
}
