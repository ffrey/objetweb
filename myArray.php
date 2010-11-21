<?php
/**
 * static methods to act as helper functions for routine array processing
 *
 * @throws Exception
 *
 */
class myArray
{
/**
* @param $newKeys array : [<old key>] => [<new key>]
*/
  public static function translateKeys(array $withKeysToBeTranslated, array $newKeys)
  {
	$ret = array();
	// $keys = array_flip($newKeys);
    foreach ($withKeysToBeTranslated AS $key => $val) {
      if (!key_exists($key, $newKeys ) ) {
	     $ret[$key] = $val; continue;
	  }
	  $ret[$newKeys[$key] ] = $val;
	}
	return $ret;
  }
  public static function notEmpty()
  {
// use array_filter(array <$a>) without a callback !
  }
  
	/**
	 * split $A into $slices sub arrays
	 * @todo manage uneven lengths !!! / see if enhance poss with array_chunk() !
	 */
	public static function _split_into($A, $slices) {
		$A_ret = array ();
		
		$size = count ( $A );
		$sizeOfSlice = $size / $slices;
		$slice = floor ( $sizeOfSlice );
		$offset = 0;
		$surplus = 0;
		if ($sizeOfSlice != $slice)
			$surplus = ($sizeOfSlice - $slice) * $slice;
		for($i = 1; $i <= $slices; $i ++) {
			$s = $slice;
			if (0 < $surplus) {
				$surplus --;
				$s = $slice + 1; // if uneven => odd values added to first array(s)
			}
			$A_ret [] = array_slice ( $A, $offset, $s );
			$offset += $s;
		}
		return $A_ret;
	}
}