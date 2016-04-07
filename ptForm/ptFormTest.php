<?php
// php ./phpunit-4.8.phar ./ptForm/ptFormTest.php
require_once 'ptForm.class.php';
require_once dirname(__FILE__).'/../helper/owConsoleHelper.php';
error_reporting(E_ALL); ini_set('display_errors', true);
if (!class_exists('testForm') ) {
	class testForm extends ptForm
	{
		protected function clean()
		{
					$val = $this->getRawValue('chp1');
					$c   = trim($val);
					$this->setClean('chp1', $c);
		}
	}
}
class ptFormTest extends PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		
	}

	protected function setUp()
	{
		$this->form = new testForm();
	}
	/*	*/
	public function testBind()
	{
		step('testing binding mandatory prior to validation');
		$got = $this->form->isValid();
		$this->assertFalse($got);
		$msg = $this->form->getError('all');
		$ok = (false !== strpos($msg, 'Veuillez') );
		$this->assertTrue($ok);
		
		$this->form->bind(array() );
		$got = $this->form->isValid();
		$this->assertFalse($got);
		
		$data = array('chp1' => '');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertTrue($got);
		
		$this->form = null;
		$this->form = new ptForm();
		$data = array('chp1' => '');
		$got = $this->form->isValid($data);
		$this->assertTrue($got);
	}
	
	public function testRequired()
	{
		$msg = 'Chp1 obligatoire / vide ok';
		$key = 'chp1';
		$this->form->checkRequired($key, $msg, false);
		$data = array($key => '', 'field' => 'hello');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertTrue($got, $msg);
	
		$msg = 'Chp1 obligatoire / vide nok';
		$key = 'chp1';
		$this->form->checkRequired($key, $msg, true);
		$data = array($key => '');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertFalse($got, $msg);
		
		$msg = 'Chp1 obligatoire / vide nok par defaut !';
		$key = 'chp1';
		$this->form->checkRequired($key, $msg, true);
		$data = array($key => '');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertFalse($got, $msg);
		
		$this->assertTrue($this->form->isRequiredField($key) );
		$this->assertFalse($this->form->isRequiredField('field') );
		
		$sTag = '<strong>*</strong>';
		$this->form->setRequiredTag($sTag);
		$this->assertEquals($sTag, $this->form->showIfRequired($key) );
		$this->assertEquals('', $this->form->showIfRequired('field') );			
	}
	
	public function testNotVide()
	{
		$msg = 'Chp1 ne doit pas etre vide';
		$key = 'chp1';
		$this->form->checkNotVide($key, $msg);
		$data = array('anyfield' => 'anyval');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertTrue($got, 'field is not mandatory');
		
		$this->form->checkNotVide($key, $msg);
		$data = array($key => '');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertFalse($got, 'if present, field must not be empty');
		$got = $this->form->getError($key);
		$this->assertEquals($msg, $got);
	
		$val = $data['chp1'] = 'chp non vide ';
		$got = $this->form->isValid($data);
		$this->assertTrue($got);
		$errors = $this->form->getErrors();
		$this->assertEquals(0, count($errors) );
		
		$rawVal = $this->form->getRawValue($key);
		$this->assertEquals($val, $rawVal);
		$cleaned = $this->form->getCleanedValue($key);
		$this->assertEquals(trim($val), $cleaned);
			
	}
	
	public function testRegEx()
	{
		$msg = 'Chp2 ne respecte pas le format attendu.';
		$key = 'chp2';
		$reg = '#[a-z]{3}[0-9]{1}#';
		$this->form->checkRegEx($key, $msg, $reg);
		$data = array($key => 'abc4');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertTrue($got);
		
		$data = array($key => 'ab4');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertFalse($got);
		$got = $this->form->getError($key);
		$this->assertEquals($msg, $got);
	}
	
	public function testEmail()
	{
		$key = 'chp3';
		$this->form->checkEmail($key);
		$data = array($key => 'abc4');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertFalse($got);
		$got = $this->form->getError($key);
		$std_msg = 'Format d\'email invalide';
		$this->assertEquals($std_msg, $got);
		
		$data = array($key => 'ab4@gmail.com');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertTrue($got);
	}

	public function testNumber()
	{
		step(__FUNCTION__);
		$db = false;
		$key = 'chp3'; $key2 = 'chp4';
		$this->form->checkNumber($key);
		$data = array($key => 'abc4', $key2 => 'lkj');
		$msg_email = 'erreur de mail !';
		$this->form->checkEmail($key2, $msg_email);
		$this->form->bind($data);
		$got = $this->form->isValid();
		if ($db) { var_dump('FIRST', $got, $this->form->getErrors(), $this->form->getRawValues() ); }
		$this->assertFalse($got);
		$got = $this->form->getError($key);
		$std_msg = 'Seuls les nombres sont accept&eacute;s';
		$this->assertEquals($std_msg, $got);
		$errors = $this->form->getErrors();
		$this->assertTrue(2 == count($errors) );
		$this->assertEquals($msg_email, $errors[$key2]);
	
		$data = array($key => '45');
		$this->form->bind($data);
		$got = $this->form->isValid();
		// var_dump($this->form->getErrors() );
		$this->assertTrue($got);
		
		$this->assertFalse($this->form->isRequiredField($key2) );
		$this->form->checkRequired($key2, 'L\'email est obligatoire !');
		$this->form->checkRequired('jlk', 'MANDATORY !');
		$this->assertTrue($this->form->isRequiredField($key2) );
		$got = $this->form->isValid();
		// 
		if ($db) { var_dump('errors', $this->form->getErrors() ); }
		$this->assertFalse($got);
	}

	public function testMaxLength()
	{
		step(__FUNCTION__);
		$db = false;
		$aTests = array(
			array('chpOk' => 'kjk'), array('chpTooLong' => 'ouiouiouioui oiu') );
		$aExpected = array(
			true,
			false,
		);
		$msg = 'Le chp ne doit pas dépasser %s caractères';
		$i = 0;
		foreach ($aTests AS $i => $aTest) {
			$expected 	= $aExpected[$i];
			foreach ($aTest AS $field => $value) {
				$this->form->checkMaxLength($field, $msg, 5);
				$this->form->bind($aTest);
				$got = $this->form->isValid();
				$this->assertEquals($got, $expected, 
					sprintf('Value tested : %s / expected : %s => got %s', $value, $expected?'true':'false', $got?'true':'false')
				);
				if (false == $got) {
					$this->assertTrue(array_key_exists($field, $this->form->getErrors() ) );
				}
			}
		}
	}
	
	public function testMinLength()
	{
		step(__FUNCTION__);
		$db = false;
		$aTests = array(
			array('chpOk' => 'kjk'), array('chpTooLong' => 'ouiouiouioui oiu') );
		$aExpected = array(
			false,
			true,
		);
		$msg = 'Le chp ne doit pas dépasser %s caractères';
		$i = 0;
		foreach ($aTests AS $i => $aTest) {
			$expected 	= $aExpected[$i];
			foreach ($aTest AS $field => $value) {
				$this->form->checkMinLength($field, $msg, 5);
				$this->form->bind($aTest);
				$got = $this->form->isValid();
				$this->assertEquals($got, $expected, 
					sprintf('Value tested : %s / expected : %s => got %s', $value, $expected?'true':'false', $got?'true':'false')
				);
				if (false == $got) {
					$this->assertTrue(array_key_exists($field, $this->form->getErrors() ) );
				}
			}
		}
	}
	
	public function testSeveralRules()
	{
		$db = false;
		$key = 'chp3'; $key2 = 'chp4';
		if ($db) { 
			var_dump('AVANT', $got, $this->form->getErrors(), $this->form->getRawValues() );
		}
		$this->form->checkRequired($key);
		if ($db) { var_dump('APRES RULE REQUIRED', $got, $this->form->getRules(), $this->form->getRawValues() ); }
		// $this->form->checkNumber($key);
		$this->form->checkNotVide($key);
		$this->form->checkEmail($key, 'erreur mail');
		$data = array('lkj' => 'oiuio');
		$this->form->bind($data);
		$got = $this->form->isValid();
		$this->assertFalse($got);
		if ($db) { var_dump('FIRST', $got, $this->form->getErrors(), $this->form->getRawValues() ); }
// 		
		$data = array($key => 'lklj@gmail.com', 'lkjl' => 'oiuio');
 		$this->form->bind($data);
 		$got = $this->form->isValid();
 		$this->assertTrue($got);
 		if ($db) { var_dump('SECOND', $this->form->getErrors() ); }
	}
	
	public function testValeursPtEtreVidesSurChpNotRequired()
	{
		$key = 'chp3'; 
		$this->form->checkNumber($key);
		$data = array($key => '');
		$this->form->bind($data);
		$got = $this->form->isValid();
		// var_dump('FIRST', $got, $this->form->getErrors(), $this->form->getRawValues() );
		$this->assertTrue($got, 'un champ vide non obligatoire est tjs ok !');
	}
	/*	*/
	public static function tearDownAfterClass()
	{
		// wait('before teardown');
	}


}