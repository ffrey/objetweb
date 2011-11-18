<?php
// phpunit C:\wamp\lib\ow\preg_matchTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

/**
 * @see http://php.net/manual/fr/function.preg-match.php
 */
class preg_matchTest extends PHPUnit_Framework_TestCase
{
  public function test_preg_match()
  {
  $subject ='hello man';
  $pattern = '#(hello){0}#';
  $got = preg_match($pattern, $subject, $matches);
  var_dump($got,$matches); exit;
  	$tests = array(
		array('hello man',        0),
		/*
		array('bonjour monsieur', 1),
		array('man, hello !',     0),
		array('koikehellokok',    0),
		array('koikehelelokok',   1),
		*/
	);
  	foreach ($tests AS $t) {
		$subject = $t[0];
		$expect  = $t[1];
		$pattern = '#(hello){0}#';
  		// var_dump($subject);
  		$got = preg_match($pattern, $subject, $matches);
  		$this->assertEquals($expect, $got, 
			sprintf('testing if %s has NOT %s', $subject, 'hello')
		);
  	}
  }
  
  
}