<?php
/**
 * erreurs : 
 
 */

class exampleClass extends PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer object used for testing.
     *
     * @var PHP_CodeSniffer
     */
    protected static $phpcs = null;


    /**
     * Sets up this unit test.
     *
     * @return void
     */
    protected function useOfOperators()
    {
        if (self::$phpcs === null) { // operateurs de comparaison doivent etre 
			//... entoures d'un espace de chaque cote
            self::$phpcs = new PHP_CodeSniffer();
        }
		
		$a = array();
		if (count($a) ) { // )] doubles doivent etre separes par au moins un espace
			
		}
		

    }//end setUp()


    /**
     * Should this test be skipped for some reason.
     *
     * @return void
     */
    protected function testErreurs()
	{
		$cmd = '';
	}


}//end class