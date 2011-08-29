<?php
// phpunit C:\wamp\lib\ow\owStringTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'myString.php';

class owStringTest extends PHPUnit_Framework_TestCase
{
  public function testConvertir()
  {
  	$tests = array(
  	array('34.87', 'trente quatre euros et quatre-vingt sept centimes'),
  	array('7',  'sept euros'),
  	array('70', 'soixante-dix euros'),
  	array('74', 'soixante-quatorze euros'),
  	array('91', 'quatre-vingt onze euros'),
  	array('300495.77', 'trois-cent mille quatre-cent quatre-vingt quinze mille euros et soixante-dix-sept centimes'),
  	);
  	foreach ($tests AS $t) {
  		// var_dump($t);
  		$got = myString::convertir($t[0]);
  		$this->assertEquals($t[1], $got);
  	}
  }
  
  public function testKeyValueDecode()
  {
	$tests = array(
		array('hello;nom=hello;extreme=test=hello;', array('nom' => 'hello', 'extreme' => 'test=hello') ),
		array('sep;;tricky=;easy=no;kj',             array('tricky' => '', 'easy' => 'no') ),
		array('=nokey!;hello=bonjour',              array('hello' => 'bonjour') ),
	);
	foreach ($tests AS $t) {
		$got = myString::keyValueDecode($t[0]);
		$this->assertEquals($t[1], $got);
	}
  }
}