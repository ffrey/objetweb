<?php
// phpunit C:\wamp\lib\ow\owNumberTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'owNumber.php';

/**
 * @see http://www.php.net/manual/fr/function.is-numeric.php
 */
class owNumberTest extends PHPUnit_Framework_TestCase
{
  public function testIsReallyInt()
  {
  /*
  var_dump(
  is_int( 9223372036854775807 ),
  is_float( 9223372036854775807 )
  );
  */
  	$tests = array(
	
		array(23, true),
		array("23", true),
		array(23.4, false),
		array('23.5', false),
		array(true, false),
		array(2147483647, true),
		array(2147483648, true), // !!!
		array('214748364845', true),
		array(21474836483.3, false),/**/
		array(array(), false),
		array(null, false),
	);
  	foreach ($tests AS $t) {
  		// var_dump($t);
  		$got = owNumber::isReallyInt($t[0]);
  		$this->assertEquals($t[1], $got, 'testing ' . $got);
  	}
  }
  
    public function testGetNoteArrondieALaDemiUnitePres()
  {
  /*
  var_dump(
  is_int( 9223372036854775807 ),
  is_float( 9223372036854775807 )
  );
  */
  	$tests = array(
	
		array(23, 23),
		array("24", 24),
		array(23.4, 23.5),
		array('23.5', 23.5),
		array(23.36, 23.5),
		array(10.1, 10),
		array(4.8, 5),
	);
  	foreach ($tests AS $t) {
  		// var_dump($t);
  		$got = owNumber::getNoteArrondieALaDemiUnitePres($t[0]);
  		$this->assertEquals($t[1], $got, 'testing ' . $t[0]);
  	}
  }
  
  
}