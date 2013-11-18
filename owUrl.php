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
		$ret = $url;
		$sFragment = $sQuery = '';
		// cas des ?/# trailing
		$sLastChar = substr($url, -1);
		if (is_array($mixedQuery) ) {
			$s = '';
			foreach ($mixedQuery AS $var => $val) {
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
			$sQuery = $aParts[1] . '&';
			$url = $aParts[0] . '?';
		} else {
			$url .= '?'; 
		}
		

		
		$ret = $url . $sQuery . $mixedQuery . $sFragment;
		return $ret;
  }
  
  
}