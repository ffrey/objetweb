<?php
/**
 * 
 */
// phpunit C:\wamp\lib\ow\Exception\owExcTest.php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'owExc.class.php';

class owExcTest extends PHPUnit_Framework_TestCase
{
    public function testSet()
    {
    	try {
        	owExc::set('hello', 'bonjour');
    	} catch (owExc $e) {
    		$this->assertTrue($e instanceof owExc);
    	}
    	
    	$newDefault = 'Oups un bleme !!!';
    	owExc::set('defaultPublicMsg', $newDefault);
    	
    	try {
    		throw new owExc('hello again ##');
    	} catch (owExc $e) {
    		$this->assertEquals($newDefault, $e->getPublicMsg() );
    	}
    }
    
    public function testGetPublicMsg()
    {
		$newDefault = 'oups !-(';
		owExc::set('defaultPublicMsg', $newDefault);
		$aPublicMsg = array();
		$aPublicMsg[] = 'easy man !';
		$aPublicMsg[] = 'La ville ne correspond pas au code postal renseign&eacute;.';
		$tests = array(
			'too complicated for average user ## ' . $aPublicMsg[0],
			sprintf('%s / %s ## '.$aPublicMsg[1], '95000', 'paris'),
		);
		$i = 0;
    	foreach ($tests AS $t) {
			try {
				throw new owExc($t);
			} catch (owExc $e) {
				$this->assertEquals($aPublicMsg[$i], $e->getPublicMsg(), 'with sep, the initial msg is thrown');
			}
			$i++;
		}

		$sepEasyMsg = '## '.$aPublicMsg[0];
		try {
    		throw new owExc($sepEasyMsg);
    	} catch (owExc $e) {
    		$this->assertEquals($aPublicMsg[0], $e->getPublicMsg(), 'with prepended sep, the initial msg is thrown');
    	}
    }
	
	public function testGetPublicMsgWithoutSep()
    {
		$newDefault = 'oups !-(';
		owExc::set('defaultPublicMsg', $newDefault);
    	$easyMsg = 'easy man !';
    	try {
    		throw new owExc($easyMsg);
    	} catch (owExc $e) {
    		$this->assertEquals(		$newDefault, $e->getPublicMsg(), 'without sep, default msg is thrown');
    	}
    }
    
    public function testStrictMode()
    {
    	$easyMsg = 'easy man !';
    	$severalMsgSeps = 'too complicated for average user ## ' . $easyMsg . ' ## hello again ';
    	try {
    		throw new owExc($severalMsgSeps);
    	} catch (owExc $e) {
    		$this->assertEquals($easyMsg, $e->getPublicMsg() );
    	}
    	
    	owExc::set('strict', true);
    	try {
    		$e = new owExc($severalMsgSeps);
    		$e->getPublicMsg();
    	} catch (owExc $e) {
    		// print $e->getPublicMsg();
    		$this->assertTrue(FALSE !== strpos($e->getMessage(), 'msgSep found') );
    	}
    }
    
}