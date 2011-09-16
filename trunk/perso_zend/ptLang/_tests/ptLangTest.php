<?php
// phpunit C:\wamp\lib\ow\perso_zend\ptLang\_tests\ptLangTest.php
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once '\..\ptLang.class.php';
if (!defined('DS') ) { define('DS', DIRECTORY_SEPARATOR); }
/**
 * @uses Zend_Translate
 */
class ptLangTest extends PHPUnit_Framework_TestCase
{
	static protected 
    	$tr = null;
	
    public static function setUpBeforeClass()
    {
        $dir = dirname(__FILE__).DS.'languages';
		ptLang::init($dir);
    }
	
	public function testTranslate()
	{
		$tests = array(
		'fr' => array(
			'hello' => 'bonjour',
			),
		'es' => array(
			'hello' => 'buenos dias',
			),
		);

		$this->assertEquals('fr', ptLang::get()->getLocale(),
		'default locale is fr');
		foreach ($tests['fr'] AS $code => $expected)	{
			$got = ptLang::get()->_($code);
			$this->assertEquals($expected, $got);
		}
		$lang = 'es';
		ptLang::get()->setLocale($lang);
		$current_locale = ptLang::get()->getLocale();
		$this->assertEquals($lang, $current_locale,
		'all methods of Zend_Translate are available through ptLang::get()');
		foreach ($tests['es'] AS $code => $expected) {
			$got  = ptLang::get()->_($code);
			$got2 = ptLang::__($code);
			$this->assertEquals($expected, $got);
			$this->assertEquals($got, $got2);
		}
		/* */
	}
	
	public function testAddTranslation()
	{
		try {
			ptLang::get()->setLocale('de');
			$this->fail('setting an unavailable locale throws Exception');
		} catch (Exception $E) { }
		$root = dirname(__FILE__).DS;
		$non_existent_dir = $root.'unknown';
		try {
			ptLang::get()->addTranslation(array('content' => $non_existent_dir) );
			$this->fail('adding non existant dir throws Exception');
		} catch (Exception $E) { }
		$new_dir          = $root.'more_languages';
		ptLang::get()->addTranslation(array('content' => $new_dir) );
		ptLang::get()->setLocale('de');
		$got = ptLang::__('hello');
		$this->assertEquals('guten tag', $got);
	}
}