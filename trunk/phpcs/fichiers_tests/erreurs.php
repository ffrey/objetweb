<?php
/**
 * erreurs : 
 */


 class example_class extends PHPUnit_Framework_TestCase
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
    protected function setUp()
    {
        if (self::$phpcs ===  null) { // erreur : 2 espace apres ===
            self::$phpcs=new PHP_CodeSniffer(); // erreur : pas d'espace autour de = 
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

?>
