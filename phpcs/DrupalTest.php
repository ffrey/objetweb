<?php
// C:\wamp\lib\ow\phpcs\phpunit DrupalTest.php
/**
 * 
 */

require_once './PhpcsStandardTest.class.php';

/**
 * 
 */
class DrupalTest extends PhpcsStandardTest
{
    public function testErreurs()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;

		$file = './fichiers_tests/erreurs.php';
		$aExpect 	= $this->_getExpectedErrors($file);
		$aGot 		= $this->_phpcs($file, 'Drupal');
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
		$aGot 		= $this->_phpcs($file, 'Drupal');
		if ($db) {
			var_dump($origin
			, 'errors got', $aGot
			, 'errors expected', $aExpect
			);
		}
		$this->_assertAllExpectedErrorsAreFound($aGot, $aExpect);
	}

}//end class