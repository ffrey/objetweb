<?php
/**
 * static methods to act as helper functions for routine array processing
 *
 * @throws Exception
 *
 */
class owNumber
{
/**
 * @author Roberto Rama on http://php.net/manual/fr/function.is-int.php
 *
 * @todo : optimize ! @see me at rexxars, pb of limit on 32 bit sys : 2147483647 !  
 */
  public static function isReallyInt($number)
  {
		$ret = false;
		if (is_int($number) ) {
			$ret = true;
		} else if (is_string($number) OR is_float($number) ) { // is_float because : is_int( 9223372036854775807 ) === false / is_float( 9223372036854775807 ) === true
			$ret = (preg_match( '/^\d*$/'  , $number) == 1 );
		}
		return $ret;
		/**
		$ret = false; $db = true; $origin = __CLASS__.'::'.__FUNCTION__;
		// First check if it's a numeric value as either a string or number
        if(is_numeric($number) === true){
            // It's a number, but it has to be an integer
            if((int)$number == $number){
                $ret = true;
            }        
        // It's a number, but not an integer, so we fail
        }
		
		$orig = $number;
		$int = (int)$number; // -2147483648 !
		if ($db) { var_dump($origin, $int, $orig); }
		// if false, we do an extra check
		// ... <= pb of limit on 32 bit sys : 2147483647 ! 
		if ( !$ret AND ($int < 0 AND is_float($number) ) OR is_string($orig) ) {
			if ($db) {
				print($origin . ' : checking over 32 bit limit !!!');
			}
			// $ret = (is_int($orig) OR is_float($orig) );
			$ret = (preg_match( '/^\d*$/'  , $orig) == 1 );
		}	
		
		return $ret;
		*/
	}
	
	public static function getNoteArrondieALaDemiUnitePres($fNb)
	{
		$fNote = $fNb;
		$iNote = (int) $fNb;
		$iDec = $fNote - $iNote;
		if (0.25 > $iDec) {
			$iArr = 0;
		} else if ($iDec <= 0.75) {
			$iArr = 0.5;
		} else {
			$iArr = 1;
		}
		return ($iNote + $iArr);
	}
	
}