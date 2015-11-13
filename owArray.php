<?php

/**
 * @author : ffrey
 */
class owArray 
{
    /**
     * @author : anonymous on http://www.php.net/manual/fr/function.array-unshift.php
     * @param type $arr
     * @param type $key
     * @param
     */
    static public function array_unshift_assoc($arr, $key, $val)
    {
        $db = false; $origin = __CLASS__.'::'.__FUNCTION__;
        if ($db) {
            var_dump($origin, 'got', $arr);
        }
        $arr = array_reverse($arr, true);
        $arr[$key] = $val;
        if ($db) {
            var_dump($origin, 'new', $arr);
        }
        $ret = array_reverse($arr, true);
        if ($db) {
            var_dump($origin, 'return', $ret);
        }
        return $ret;
    }
    
	static public function removeFromArray(array $aKeyValue, array $arrayToFilter)
		{
			$db = false;
			$origin = __CLASS__ . '::' . __FUNCTION__;
						if ($db ){
				var_dump($origin
					, 'looking for ', print_r($aKeyValue, true) );
			}
			$arrayFiltered = $arrayToFilter;
			foreach ($arrayToFilter AS $k => $l) {
				if (!is_array($l) ) {
					continue;
				}
				$iRemove = self::isArrayContained($aKeyValue, $l);
				if ($iRemove) {
					if ($db) {
						var_dump($origin
							, 'REMOVE', $arrayFiltered[$k]
							);
					}
					unset($arrayFiltered[$k]);
				}
			}
			if ($db) {
				var_dump($origin
					, 'ret', $arrayFiltered
					);
			}
			return array_values($arrayFiltered);
		}

	// /_removeFromArray()

		static public function extractFromArray(array $aKeyValue, array $arrayToFilter)
		{
			$db = false;
			$origin = __CLASS__ . '::' . __FUNCTION__;
			$arrayFiltered = $arrayToFilter;
			foreach ($arrayToFilter AS $k => $l) {
				if (!is_array($l) ) {
					continue;
				}
				$iRemove = self::isArrayContained($aKeyValue, $l);
				if (!$iRemove) {
					unset($arrayFiltered[$k]);
				}
			}
			return array_values($arrayFiltered);
		}

		static public function isArrayContained(array $aKeyValues, array $arrayToSearch)
		{
			$db = false;
			$origin = __CLASS__ . '::' . __FUNCTION__;
			$ret = true;
			foreach ($aKeyValues AS $key => $val) {
				if ($db) {
					var_dump($origin
						, 'existe ' . $key . ' ?', array_key_exists($key, $arrayToSearch)
						);
				}
				if (!array_key_exists($key, $arrayToSearch)) {
					$ret = false;
					continue;
				}
								if ($db) {
					var_dump($origin
						, $arrayToSearch[$key] . ' != ' . $val . ' ?'
						);
				}
				if ($val != $arrayToSearch[$key]) {
					$ret = false;
					continue;
				}
				if ($db) {
					var_dump($origin, 'FOUND', $key . ' = ' . $val, $ret);
				}
			}
			return $ret;
		}

		static public function reindexLinesWithValueFromKey($keyToUse, array $arrayToReIndex) {
			$db = false;
			$origin = __CLASS__ . '::' . __FUNCTION__;
			$ret = array();
			foreach ($arrayToReIndex AS $a) {
				if (!isset($a[$keyToUse]) ) {
					throw new Exception('Unknown key : ' . $keyToUse);
				}
				$ret[$a[$keyToUse] ] = $a;
			}
			return $ret;
		}

		/**
		 * @source https://github.com/ramsey/array_column/blob/master/src/array_column.php
		 */
		static function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}