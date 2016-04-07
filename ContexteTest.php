<?php
// php ./phpunit-4.8.phar ./ContexteTest.php
require 'Contexte.class.php';
error_reporting(E_ALL); ini_set('display_errors', true);

class ContexteTest extends PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		
	}

	protected function setUp()
	{
		
	}
	
	public function testIspage()
	{
		$sPage = 'accueil';
		Contexte::setPage($sPage);
		$aTests = array(
			'accueil',
			'accueils',
			'acc*',
			array('acc*'),
		);
		$aExpects = array(
			true,
			false,
			true,
			false,
		);
		for ($i = 0; $i < count($aTests); $i++) {
			$expect = $aExpects[$i];
			$test   = $aTests[$i];
			$got = Contexte::isPage($test);
			$this->assertEquals($got, $expect, 'testing with ' . print_r($test, true) );
		}
	}
	
		
	public static function tearDownAfterClass()
	{
		// wait('before teardown');
	}


}