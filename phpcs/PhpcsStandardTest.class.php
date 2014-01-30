<?php
// C:\wamp\lib\ow\phpcs\phpunit PubSF1Test.php
/**
 * 
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * 
 */
class PhpcsStandardTest extends PHPUnit_Framework_TestCase
{

    /**
     * path to PHP_CodeSniffer cli
     */
    protected static $phpcs_path = 'phpcs';

    protected function _assertAllExpectedErrorsAreFound($aGot, $aExpect)
	{
		$db = false;
		foreach ($aExpect AS $iExpectedLine => $aExpectedMsgs) 
		{
			$isLineWithErrorFound = array_key_exists($iExpectedLine, $aGot);
			if ($db) {
				var_dump('error exists at line ' . $iExpectedLine . ' ?', $isLineWithErrorFound);
			}
			$this->assertTrue($isLineWithErrorFound, sprintf('Line %s expected with errors (%s) but none found', $iExpectedLine, print_r($aExpectedMsgs, true) ) );
			if (!$isLineWithErrorFound) { 
				continue; 
			}
			$isFoundMsg = false;
			foreach ($aExpectedMsgs AS $i => $sExpectedMsg) {
				foreach ($aGot[$iExpectedLine] AS $j => $sGotMsg) {
					if ($db) {
						var_dump('expected msg ' . $sExpectedMsg . ' = ' . $sGotMsg . ' ?');
					}
					if (fnmatch($sExpectedMsg, $sGotMsg) ) {
						if ($db) { var_dump('YES !', 'unset ' . $iExpectedLine . ' > ' . $j, $aGot[$iExpectedLine][$j]); }
						$isFoundMsg = true;
						unset($aGot[$iExpectedLine][$j]);
						if (is_array($aGot[$iExpectedLine]) AND 0 == count($aGot[$iExpectedLine]) ) {
							unset($aGot[$iExpectedLine]);
						}
						break;
					}
				}
			}
			if ($db) {
				var_dump('end', $aGot);
			}
			$this->assertTrue($isFoundMsg, sprintf('"%s" different from got : "%s" ?', $sExpectedMsg, $sGotMsg) );
		}
		$this->assertTrue(0 == count($aGot), 'Unexpected errors at lines : ' . implode(', ', array_keys($aGot) ) . "\r\n");
	}
	
	protected function _phpcs($file, $standard)
	{
		$db = true; $origin = __CLASS__ . '::' . __FUNCTION__;
		// $cmd = 'phpcs -v --standard=PubSf1 ./fichiers_tests/erreurs.php';
		$cmd = sprintf(self::$phpcs_path . ' -n --standard=%s --report=csv %s', $standard, $file); 
		if ($db) {
			var_dump($origin, 'cmd launched : ' . $cmd);
		}
		exec($cmd, $csv, $ret);
		$header = NULL; $aGot = array();
		foreach ($csv AS $row) 
        {
			$row = str_getcsv ($row);
            if(!$header) {
				$header = $row;
				continue;
            } 
			$d = array_combine($header, $row);
            $aGot[$d['Line']][] = $d['Message'];
        }
		return $aGot;
	}
	
	protected function _getExpectedErrors($file)
	{
		$db = false;
		$ret = array();
		$file .= '.expect';
		if (!file_exists($file) ) {
			throw new Exception ('Expect file non-existent : ' . $file);
		}
		if ( ($handle = fopen($file, "r") ) !== FALSE) {
			while ( ($data = fgetcsv($handle, 20000, ";") ) !== FALSE) {
				$num = count($data);
				if (1 >= $num) { continue; }
				$ret[$data[0] ][] = $data[1];
			}
			fclose($handle);
			if ($db) { var_dump(__FUNCTION__, $ret); exit; }
		}
		return $ret;
	}

}//end class