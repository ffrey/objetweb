<?php
// phpunit C:\wamp\lib\ow\web-services\Yahoo\ptYahooTest.php

// require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'ptYahoo.class.php';

class ptYahooTest extends PHPUnit_Framework_TestCase
{/**/
  public function testGeolocalizeOk() 
  {
	$db = false;
	$oYahoo = new ptYahoo();
	$aAdresses = array(
		'4 passage louis-philippe, 75011 Paris' => array('lat' => 48.85392, 'long' => 2.37293),
		
		// '4 passage louis-philippe, 75011 Paris' => array('lat' => 48.85392, 'long' => 2.37293),
		// '4 passage louis-philippe, 75011 Paris' => array('lat' => 48.85392, 'long' => 2.37293),
	);
	foreach ($aAdresses AS $sAdr => $expected) {
		$got = $oYahoo->geolocalize($sAdr);
		$this->assertEquals($got, $expected, 'adresse correctement geolocalisee');
	}
	
  }
  
  public function testGeolocalizeErreurs() 
  {
	$db = false;
	$oYahoo = new ptYahoo();
	$aAdresses = array(
		false => false,
		458 => false,
		'ouiou iuyy' => false,
	);
	foreach ($aAdresses AS $sAdr => $expected) {
		$got = $oYahoo->geolocalize($sAdr);
		$this->assertEquals($got, $expected, 'une adresse au mauvais format renvoie false');
	}
	/*
	$aAdresses = array(
		'adresse us' => false,
		'adresse france metro' => array('', ''),
	);
	foreach ($aAdresses AS $aAdr => $expected) {
		$got = ptYahoo::geolocalize($sAdr, 'france_metropolitaine');
		$this->assertEquals($got, $expected, 'le parametre zone_geo permet de detecter les erreurs de geocodage');
	}
	$aZones = array(
	'france_metropolitaine' => true,
	'france_metro'          => true, // abbreviations acceptees !
	'metro_france'     		=> false,
	'us_zone'				=> false,
	);
	foreach ($aZones AS $zone) {
	try {
		$got = ptYahoo::geolocalize($sAdr, $zone);
		$this->assertTrue(false, 'un parametre zone_geo inconnu envoie une exception !');
	} catch (Exception $E) {
		
	}
	
	}
	*/
  }
  /**/
}