<?php
// phpunit C:\wamp\lib\ow\helper\formFunctionsTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
$root = dirname(__FILE__);
require_once $root.'\..\owArray.php';
require_once 'form.functions.php';

class formFunctionsTest extends PHPUnit_Framework_TestCase
{
  public function testG_if()
  {
    $expect_date = '10/11/2010';
    $array = array(
	'don' => array('transac_date' => array('to' => $expect_date) ),
	'type' => 'pso',
	);
  	
	$got = g_if('type', $array);
	$this->assertEquals($got, $array['type']);
	
	$k = '[don][transac_date][to]';
	$got = g_if($k, $array);
	$this->assertEquals($got, $array['don']['transac_date']['to']);
	/**/
  }
  
  
}