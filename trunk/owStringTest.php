<?php
// phpunit C:\wamp\lib\ow\owStringTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'owString.php';

class owStringTest extends PHPUnit_Framework_TestCase
{
  public function testConvertir()
  {
  /*
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
  		$got = owString::convertir($t[0]);
  		$this->assertEquals($t[1], $got);
  	}
	*/
  }
  
  public function testKeyValueDecode()
  {
	$tests = array(
		array('hello;nom=hello;extreme=test=hello;', array('nom' => 'hello', 'extreme' => 'test=hello') ),
		array('sep;;tricky=;easy=no;kj',             array('tricky' => '', 'easy' => 'no') ),
		array('=nokey!;hello=bonjour',              array('hello' => 'bonjour') ),
	);
	foreach ($tests AS $t) {
		$got = owString::keyValueDecode($t[0]);
		$this->assertEquals($t[1], $got);
	}
  }
  
  public function testAddToUrl()
  {
  
    /**
	 * @author : http://www.vnoel.com/PHP/Adding-Parameters-To-A-URL-in-PHP.html
	 */
	function addGetParamToUrl($url, $varName, $value = null)
	{
		$db = false; $origin = __FUNCTION__;
		// build new param
		$newVar = $varName;
		if (null != $value) {
			$newVar .= '='.$value;
		}
		
		$sep = '?';
		$isQuestionMark = strpos($url,'?');
		if ($db) { var_dump($origin, $url, $isQuestionMark, strlen($url) ); }
		if (false !== $isQuestionMark) {
			$sep = '&';
		} 
		if (strlen($url) == 1+$isQuestionMark) {
			$sep = '';
		}
		$insertPosition = strlen($url); 
		if (strpos($url,'#')!==false) {
			$insertPosition = strpos($url,'#');
		}
		// Build the new url
		$newUrl = substr_replace($url, $sep.$newVar, $insertPosition, 0);
		
		return $newUrl;
	} // /addGetParamToUrl()
	
	$tests = array(
		array('/hello.fr/?hello&g=3', 'bonjour=3', '/hello.fr/?hello&g=3&bonjour=3'),
		array('http://hello.fr', 'bonjour=5', 'http://hello.fr?bonjour=5'),
		array('http://bjr.eu?hello=b#hell', 'bonjour=5', 'http://bjr.eu?hello=b&bonjour=5#hell'),
		array('/frontend_dev.php/prestations/choixGarage?', 'b=7', '/frontend_dev.php/prestations/choixGarage?b=7'),
	);
	foreach ($tests AS $t) {
		// $p = parse_url($t[0]);
		// var_dump($p);
		// continue;
		$got = addGetParamToUrl($t[0], $t[1]);
		$this->assertEquals($t[2], $got);
	}
  
  }
  
  function testStripAccents()
  {
	$t = array(
		"l'orienté à gauche ne s'entend pas avec Noël" => "l'oriente a gauche ne s'entend pas avec Noel"
	);
	foreach ($t AS $t => $expect)
	{
		$got = owString::stripAccents($t);
		$this->assertEquals($expect, $got);
	}
  }
}