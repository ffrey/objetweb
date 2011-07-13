<?php
/**
 * static methods to act as helper functions for routine array processing
 *
 * @throws Exception
 *
 */
class owArray
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
	 * @return array : 'key' => <slice> !
	 * @todo manage uneven lengths !!! / @see if enhance poss with array_chunk() !
	 */
	public static function _split_into($A, $slices) {
		$A_ret = array ();

		$size = count ( $A );
		if ($size < $slices) {
			$slices = $size;
		}
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

	/**
	 * unsets all $keys existing in $A
	 * @return array $A
	 */
	public static function multiUnset(array $A, array $keys) {
		foreach ($keys AS $k) {
			if (array_key_exists($k, $A) ) {
				unset($A[$k]);
			}
		}
		return $A;
	}

	/*
	 * Flattening a multi-dimensional array into a
	 * single-dimensional one. The resulting keys are a
	 * string-separated list of the original keys:
	 *
	 * a[x][y][z] becomes a[implode(sep, array(x,y,z))]
	 * @author : Carsten Milkau (http://php.net/manual/en/function.array-values.php)
	 */

	public static function flatten_sep($sep, $array) {
		$result = array();
		$stack = array();
		array_push($stack, array("", $array));

		while (count($stack) > 0) {
			list($prefix, $array) = array_pop($stack);

			foreach ($array as $key => $value) {
		  $new_key = $prefix . strval($key);

		  if (is_array($value))
		  array_push($stack, array($new_key . $sep, $value));
		  else
		  $result[$new_key] = $value;
			}
		}

		return $result;
	}

	/**
	 * adds keys+vals from $b onto $a
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function array_merge_with_numeric_keys(array $a, array $b)
	{
		foreach ($b AS $k => $v) {
			$a[$k] = $v;
		}
		return $a;
	}

	/**
	 * renvoie vide ou $default_val si $var_name_exists n'existe pas dans $tab ou est vide / la valeur sinon
	 * @param $var_name_exists
	 * @param $tab
	 * @param $default_val
	 */
	public static function g_if ($var_name_exists, array $tab, $default_val = '' )
	{
		$db = false;
		$ret = $default_val;
		do {
			if ($db) {
				echo 'existe ' . $var_name_exists . ' ? ';
			}
			$vals = $tab;
			if (array_key_exists($var_name_exists, $vals) ) {
				if ($db) { echo ' YES : ' . print_r($vals, true); }
				$ret = $vals[$var_name_exists];
			} 
			if (empty($ret) ) {
				$ret = $default_val;
			}
		} while (false);

		return $ret;
	}
}