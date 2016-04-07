<?php
// php ./phpunit-4.8.phar ./owContexteTest.php
require 'owContexte.class.php';
error_reporting(E_ALL); ini_set('display_errors', true);

class owContexteTest extends PHPUnit_Framework_TestCase
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
		owContexte::setPage($sPage);
		$aTests = array(
			'accueil',
			'accueils',
			'acc*',
			array('acc*'), // glob format NOT accepted in arrays !
			array('accueils', 'accueil', 'acc*'), 
		);
		$aExpects = array(
			true,
			false,
			true,
			false,
			true,
		);
		for ($i = 0; $i < count($aTests); $i++) {
			$expect = $aExpects[$i];
			$test   = $aTests[$i];
			$got = owContexte::isPage($test);
			$this->assertEquals($got, $expect, 'testing with ' . print_r($test, true) );
		}
		$return = 'class';
		for ($i = 0; $i < count($aTests); $i++) {
			$expect = ($aExpects[$i])? $return : $aExpects[$i];
			$test   = $aTests[$i];
			$got = owContexte::isPage($test, $return);
			$this->assertEquals($got, $expect, 'testing with ' . print_r($test, true) );
		}
	}
		
	public static function tearDownAfterClass()
	{
		// wait('before teardown');
	}

}