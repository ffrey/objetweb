<?php
// phpunit C:\wamp\lib\ow\_etude_php5\_basics\operatorsTest.php
require_once dirname(__FILE__).'/../_bootstrap.php';

class owDateTest extends PHPUnit_Framework_TestCase
{
	public function testEither()
	{
		step('operator EITHER-OR');
		$tests = array(
		array(1 ^ 0, 1),
		array(1 ^ 1, 0),
		);
		foreach ($tests AS $t) {
			
			$this->assertEquals($t[0], $t[1]);
		}
	}
}
