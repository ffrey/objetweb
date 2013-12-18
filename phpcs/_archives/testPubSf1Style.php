<?php
// phpunit C:\wamp\lib\ow\phpcs\testPubSf1Style.php
if (is_file(dirname(__FILE__).'/../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../CodeSniffer.php';
} else {
    include_once 'PHP/CodeSniffer.php';
}
/**
 * 
 *
 */
class PubSf1StyleTest extends PHPUnit_Framework_TestCase
{
	public function testErrors()
	{
		$expected = array(
			'',
			'',
			'',
		);
		
	}	
}