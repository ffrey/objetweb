<?php
/**
 * static methods to act as helper functions for routine url processing
 * 
 * @package    b2c
 * @subpackage utils
 * @author FFreyssenge
 * @version 1.1  / 2009 03 17
 * * added : 
 * * * function stripSpaces()
 * @throws Exception
 */
class owUrl
{
  /**
 * 
 *
 * @param mixed $mixedQuery
 * @return string
 */
  public static function addQuery($url, $mixedQuery)
  {
		$db = true; $origin = __CLASS__.'::'.__FUNCTION__;
		$ret = $url;
		$sFragment = $sQuery = '';
		// cas des ?/# trailing
		$sLastChar = substr($url, -1);
		$aMixedQueryTmp = self::_makeStringQueryIntoArray($mixedQuery);
		$aQueryAlreadyPresent = self::_extractExistingQuery($url);
		$aMixedQuery = array_merge($aQueryAlreadyPresent, $aMixedQueryTmp);
		if ($db) {
			var_dump($origin
			, 'existing query ?', $aQueryAlreadyPresent
			, 'new query', $aMixedQueryTmp
			, 'final query', $aMixedQuery
			);
		}
		if (is_array($aMixedQuery) ) {
			$s = '';
			foreach ($aMixedQuery AS $var => $val) {
				$s .= $var . '=' . $val . '&';
			}
			$s = trim($s, '&');
			$mixedQuery = $s;
		}
		if ('?' == $sLastChar) {
			$url = substr($url, 0, strlen($url)-1);
		} else if ('#' == $sLastChar) {
			$sFragment = '#'; 
			$url = substr($url, 0, strlen($url)-1);
		} else if ('&' == $sLastChar) {
			$url = substr($url, 0, strlen($url)-1);
		}
		
		// y a-t-il un fragment
		$aParts = explode('#', $url);
		if (1 < count($aParts) ) { // fragment present
			$sFragment = '#' . $aParts[1];
			$url = $aParts[0];
		} 
		
		// ajout de la query
		// y a-t-il deja une query ?
		$aParts = explode('?', $url);
		
		if (1 < count($aParts) ) { // query deja presente
			// $sQuery = $aParts[1] . '&';
			$url = $aParts[0] . '?';
		} else {
			$url .= '?'; 
		}
		
		if ($db) {
			var_dump($origin
			, 'mixedQuery', $mixedQuery
			);
		}
		
		$ret = $url . $sQuery . $mixedQuery . $sFragment;
		return $ret;
  }
  
  static protected function _makeStringQueryIntoArray($mixedQuery)
  {
  $db = false; $origin = __CLASS__.'::'.__FUNCTION__;
	if (!is_array($mixedQuery) ) {
			$aMixedQuery = array();
			$aMixedQueryTmp = explode('&', $mixedQuery);
			foreach ($aMixedQueryTmp AS $q) {
				if ($db) {
					var_dump($origin
					, 'explode', $aMixedQueryTmp
					);
				}
				$a = explode('=', $q);
				if (2 != count($a) ) { $a[1] = ''; }
				$aMixedQuery[$a[0]] = $a[1];
			}
			if ($db) {
				var_dump($origin, 'got', $mixedQuery, 'array from string', $aMixedQuery);
			}
		} else {
			$aMixedQuery = $mixedQuery;
		}
		return $aMixedQuery;
  }
  
  static protected function _extractExistingQuery($url)
  {
  $db = true; $origin = __CLASS__.'::'.__FUNCTION__;
	$ret = array();
	$a = parse_url($url);
	$mixedTmp = isset($a['query'])? $a['query'] : $ret;
	if (!is_array($mixedTmp) ) {
		$ret = self::_makeStringQueryIntoArray($mixedTmp);
	}
	return $ret;
  }
  
}