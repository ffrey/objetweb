<?php
// phpunit C:\wamp\lib\ow\owCodesTest.php
/**
 * 
 *
 */
 require_once 'owCodes.php';
class owCodesTest extends PHPUnit_Framework_TestCase
{
	public function testGetCode()
	{
		$codes = array(
			'CB' => 'OWC',
			'PayPal' => 'PWC',
			'default' => 'OWE',
		);
		$C = new owCodes($codes);
		$n = 'CB';
		$code = $C->getCode($n, $ns = null, 'def_val');
		$this->assertEquals($codes[$n], $code);
		
		$def = 'def_val';
		$code = $C->getCode('any', $ns = null, $def);
		$this->assertEquals($def, $code);
	}	
}