<?php
/**
 * @package b2c
 * @subpackage model
 *
 * https://developers.google.com/maps/documentation/geocoding/
 *
 */
class ptGoogle
{
	public static $urlGoogle="http://maps.googleapis.com/maps/api/geocode/xml?address=";
	public static $finUrlGoogle="&sensor=false&region=fr";

	/*public static function init($url,$oCacheFile=null){
		self::$url=$url;
	self::$oCacheFile=$oCacheFile;
	}*/

	protected static function isCodePostal($v)
	{
		return preg_match('#\d{1,5}#', $v);
	}


	/**
	 *
	 * retourne array(lat,lng,status) pour une adresse donne
	 * @param unknown_type $adresse
	 * @param unknown_type $ville
	 * @param unknown_type $cp
	 * @param unknown_type $verif_cp -> si l'on souhaite verifier que l'adresse trouve correspond bien au code postal donné
	 */
	public static function geolocalize($adresse = '')
	{
		$db = true; $origin = __CLASS__.'::'.__FUNCTION__; $log = true;
		$ret = false;
		$adr = '';
		if ($adresse != '' ) {
			$adr = urlencode($adresse);
		}
		if ('' == $adr) {
			// throw new Exception(sprintf('%s : adresse vide ! # Erreur technique', $origin, $adr) );
		}
		$url =self::$urlGoogle.$adr.self::$finUrlGoogle;
		if ($db) {
			var_dump($origin, $adresse, $url);
			// exit;
		}
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER) ) {
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		}		
		$curl_response = curl_exec($curl);
		curl_close($curl);
		$xml = $curl_response;
		try {
		// $xml = file_get_contents($url);
		if (!$xml) {
			throw new Exception(sprintf('reponse Google vide : %s (%s) # Erreur technique', $xml, $url) );
		}
		$sxe = new SimpleXMLElement($xml);
		if( 'OK' === (string)$sxe->status) {
				$lat = (float) $sxe->result->geometry->location->lat;
				$lng = (float) $sxe->result->geometry->location->lng;
				$status=true;
			
			$ret = array("lat"=>$lat,"lng"=>$lng);
		} 
		} catch (Exception $E) {
		
		}
		return $ret;
	} // /rechercheApiGoogle()
}