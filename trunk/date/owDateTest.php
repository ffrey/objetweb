<?php
// phpunit C:\wamp\lib\ow\date\owDateTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'owDate.php';

/**
 * @see http://www.php.net/manual/fr/function.is-numeric.php
 */
class owDateTest extends PHPUnit_Framework_TestCase
{/**/
  public function testIsPassee() 
  {
	$db = false;
	date_default_timezone_set('Europe/Paris');
    $iNowPlusUneSeconde  = time() + 1;
	$iNowMoinsUneSeconde = time() - 1;
	list($sNowDate, $sNowHeure) = $this->formatIntoFullDate($iNowPlusUneSeconde);
	$sNowPlusUneSeconde = $sNowDate . ' ' . $sNowHeure;
	list($sNowDate, $sNowHeure) = $this->formatIntoFullDate($iNowMoinsUneSeconde);
	$sNowMoinsUneSeconde = $sNowDate . ' ' . $sNowHeure;
	if ($db) { 
	    var_dump('TIME ZONE',  date_default_timezone_get() );
		var_dump('date full', $sNowFull); exit;
	}
	$tests = array(
		 
		array('10/01/2020', false,),
		array('10/05/2010', true),
		array($sNowDate, true), // date auj sans heure est consideree egale a 00:00:00	
		array($sNowMoinsUneSeconde, true),
		array($sNowPlusUneSeconde,   false),
	);
	foreach ($tests AS $t) {
		$date = $t[0];
		$expect = $t[1];
		$got = owDate::isPassee($date);
		$this->assertEquals($expect, $got,
			printf('isPassee(%s) should be %s'."\n", $date, $expect) 
		);
	}
  }
  
  public function testErreurs()
  {
	$db = false;
	$tests = array(
		array('10-01-2020', 'nok'),
		array('10/01/2020', 'ok'),
	);
	foreach ($tests AS $d) {
	    if ($db) { print 'testing ' . $d[0] . "\n"; }
		try {
			$got = owDate::isPassee($d[0]);
		} catch (Exception $E) { continue; }
		if ('nok' == $d[1]) {
			$this->fail('An expected exception has not been raised.');
		}
	}
  }

  private function formatIntoFullDate($time)
  {
	$i = getDate($time);
	$sNow = implode(array(str_pad($i['mday'], 2, "0", STR_PAD_LEFT), str_pad($i['mon'], 2, "0", STR_PAD_LEFT), $i['year']), '/');
	$sNowHeure = implode(array(str_pad($i['hours'], 2, "0", STR_PAD_LEFT), str_pad($i['minutes'], 2, "0", STR_PAD_LEFT), str_pad($i['seconds'], 2, "0", STR_PAD_LEFT) ), ':');
	
	return array($sNow, $sNowHeure);
  }
  
}