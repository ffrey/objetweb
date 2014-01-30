<?php
// C:\wamp\lib\ow\phpcs\phpunit PubSF1Test.php
/**
 * 
 */

require_once './PhpcsStandardTest.class.php';

/**
 * 
 */
class PubSF1Test extends PhpcsStandardTest
{
    public function testErreurs()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;

		$file = './fichiers_tests/erreurs.php';
		$aExpect 	= $this->_getExpectedErrors($file);
		$aGot 		= $this->_phpcs($file, 'PubSf1');
		if ($db) {
			var_dump($origin
			, 'errors got', $aGot
			, 'errors expected', $aExpect
			);
		}
		$this->_assertAllExpectedErrorsAreFound($aGot, $aExpect);
	}
	
	public function testNoErrors()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;

		$file = './fichiers_tests/no_errors.php';
		$aExpect 	= $this->_getExpectedErrors($file);
		$aGot 		= $this->_phpcs($file, 'PubSf1');
		if ($db) {
			var_dump($origin
			, 'errors got', $aGot
			, 'errors expected', $aExpect
			);
		}
		$this->_assertAllExpectedErrorsAreFound($aGot, $aExpect);
	}

}//end class