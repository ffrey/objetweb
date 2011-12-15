<?php
/**
 * 
 */
// phpunit C:\wamp\lib\ow\owExcTest.php

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
    	$easyMsg = 'easy man !';
    	try {
    		throw new owExc('too complicated for average user ## ' . $easyMsg);
    	} catch (owExc $e) {
    		$this->assertEquals($easyMsg, $e->getPublicMsg(), 'with sep, the initial msg is thrown');
    	}
		$sepEasyMsg = '## '.$easyMsg;
		    	try {
    		throw new owExc($sepEasyMsg);
    	} catch (owExc $e) {
    		$this->assertEquals($easyMsg, $e->getPublicMsg(), 'with prepended sep, the initial msg is thrown');
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