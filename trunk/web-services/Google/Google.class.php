<?php
/**
 * @package b2c
 * @subpackage model
 *
 * https://developers.google.com/maps/documentation/geocoding/
 *
 * @author YRaoul
 * @uses parsing de l'api google pour récupérer :
 * @uses - la ville, si on envoi un CP
 * @uses - le cp, si on envoi une ville
 * @uses ptLog
 */
class Google
{
	public static $urlGoogle="http://maps.googleapis.com/maps/api/geocode/xml?address=";
	public static $finUrlGoogle="&sensor=false&region=fr";
	/*
	 * @TODO 2013 07 19 : il est necessaire de modifier l'appel à l'api Yahoo 
	 * car leur web-service a totalement changé !
	 * nouvelle url : http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.placefinder%20where%20text%3D%222%20passage%20louis-philippe%2C%20paris%22&diagnostics=true
	 * @see http://developer.yahoo.com/yql/guide/index.html
	 */
	public static $urlYahoo="http://where.yahooapis.com/geocode?q=";
	public static $finUrlYahoo="+fr";

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
	 * @param string $adresse : ville ou cp !
	 * @return mixed : array('cp', 'ville') / string 'problème technique' si rien trouve !
	 */
	public static function findVilleCp($adresse) {
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__; $log = true;
		$url=self::$urlGoogle.urlencode($adresse).self::$finUrlGoogle;
		$adresse = trim($adresse);
		if ($db) {
                    echo "<br />$origin<br />";
                    echo "looking for : $adresse<br />";
		}
		if (self::isCodePostal($adresse) ) {
			$cp = $adresse; $ville = '';
		} else {
			$cp = ''; $ville = $adresse;
		}
		$ret = array();
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$curl_response = curl_exec($curl);
		curl_close($curl);
		$xml = $curl_response;
		
		if ($db) {
                    echo "<br />$origin<br />";
                    echo "url : $url<br />";
                    echo "curl response : <br />";
                    echo "<pre>$xml</pre>";
		}                
                
		// $xml = file_get_contents($url);
		if ($log) {
			ptLog::setPrefix(date('Ymd-').'Google');
			ptLog::logAll($origin, $adresse, $url, $xml);
		}
		try {
			if ($db) { var_export($xml); }
			$sxe = new SimpleXMLElement($xml);
			if( 'OK' === (string)$sxe->status) {
				foreach ($sxe->result as $result) {
					if ('postal_code' == $result->type) { break; }
				}
				foreach ($result->address_component as $node){
					if ($db) {
						var_dump('node', $node, 'type', $node->type);
					}
					if( (string) $node->type == 'postal_code') {
						$cp = (string) $node->long_name;
					} else if($node->type=='locality'){
						$ville = (string) $node->long_name;
					}
				}
				if($cp==''){
					if ($db) { var_dump('echec rech code postal Google'); }
					$cp=self::findCpFromVilleWithYahoo($ville);
				}
// 				return '<p>'.$cp.'</p><p>'.strtoupper($ville).'</p>';
				$ret = array('cp' => $cp, 'ville' => strtoupper($ville) );
			} else {
				$ret = self::fallbackApiYahoo($adresse);
				return ($ret)? $ret:'probl&egrave;me technique';
			}
		} catch (Exception $E) {
			ptLog::setPrefix(date('Ymd-').'Google');
			ptLog::logAll($E->getMessage(), $adresse, $url, $xml);
			$ret = self::fallbackApiYahoo($adresse);
			$ret = ($ret)? $ret:'probl&egrave;me technique';
		}
		if ($db) {
			var_dump($origin
					, 'return', $ret
					);
			exit;
		}
		return $ret;
	} // /findVilleCp()

	public static function findCpFromVilleWithYahoo($ville){
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$ret = '';
		$url=self::$urlYahoo.urlencode($ville).self::$finUrlYahoo;
		try {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			$curl_response = curl_exec($curl);
			curl_close($curl);
			$xml = $curl_response;
			// $xml = file_get_contents($url);
			$sxe = new SimpleXMLElement($xml);
			$ret = $sxe->Result->uzip;
		} catch (Exception $E) {
			ptLog::setPrefix(date('Ymd-').'Yahoo');
			ptLog::logAll($E->getMessage(), $ville, $url, $xml);
		};
		return $ret;
	}

	/**
	 *
	 * @param string $cpOrVille
	 * @return array : vide si echec
	 */
	public static function fallbackApiYahoo ($cpOrVille)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__; $log = true;
		$ret = array();
		$url=self::$urlYahoo.urlencode($cpOrVille).self::$finUrlYahoo;
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$curl_response = curl_exec($curl);
		curl_close($curl);
		$xml = $curl_response;
		
                if ($db) {
                    echo "<br />";
                    echo "url : $url<br />";
                    echo "curl response : <br />";
                    echo "<pre>$xml</pre>";
                }
                
		// $xml = file_get_contents($url);
		if ($log) {
			ptLog::setPrefix(date('Ymd-').'Yahoo');
			ptLog::logAll($origin, 'ville ou cp : ' . $cpOrVille, $url, $xml);
		}
                
                if (!empty($xml)) {
                $sxe = new SimpleXMLElement($xml);
                $cp    = (string) $sxe->Result->uzip;
                $ville = (string) $sxe->Result->city;
                if ($cp) {
// 			$ret = '<p>'.$cp.'</p><p>'.strtoupper($ville).'</p>';
                        $ret = array('cp' => $cp, 'ville' => strtoupper($ville) );
                }
                } else {
                    $ret = array();
                }
		if ($log) {
			ptLog::logAll($origin, $cp, $ville, $ret, $xml);
		}
		return $ret;
	}


	/**
	 *
	 * retourne array(lat,lng,status) pour une adresse donne
	 * @param unknown_type $adresse
	 * @param unknown_type $ville
	 * @param unknown_type $cp
	 * @param unknown_type $verif_cp -> si l'on souhaite verifier que l'adresse trouve correspond bien au code postal donné
	 * @throws owExc
	 */
	public static function rechercheLatLngGoogle($adresse='',$ville='',$cp='',$verif_cp=false)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__; $log = true;
		$adr="";
		if ($adresse!='' ) {
			$adr.=urlencode($adresse)."+";
		}
		if ($ville!='' ) {
			$adr.=urlencode($ville)."+";
		}
		if ($cp!='' ) {
			$adr.=$cp."+";
		}

		if ($adr=='' ) {
			throw new owExc(sprintf('%s : adresse vide ! # Erreur technique', $origin, $adr) );
		}
		$adr=substr($adr,0,strlen($adr)-1);

		$url =self::$urlGoogle.$adr.self::$finUrlGoogle;
		if ($db) {
			var_dump($origin, $adresse, $url);
			exit;
		}
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER) ) {
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		}		
		$curl_response = curl_exec($curl);
		curl_close($curl);
		$xml = $curl_response;
		// $xml = file_get_contents($url);
		if (!$xml) {
			throw new owExc(sprintf('reponse Google vide : %s (%s) # Erreur technique', $xml, $url) );
		}
		$sxe = new SimpleXMLElement($xml);
		$lat="0";
		$lng="0";
		$status=false;
		if( 'OK' === (string)$sxe->status) {
			// if (false) {
			if($verif_cp && $cp!=''){
				foreach ($sxe->result->address_component as $node){
					if($node->type=='postal_code' && substr($node->short_name,0,2)==substr($cp,0,2)){
						$lat=$sxe->result->geometry->location->lat;
						$lng=$sxe->result->geometry->location->lng;
						$status=true;
						break;
					}
					if($node->type=='postal_code' && substr($node->short_name,0,2)!=substr($cp,0,2)){
						$status=false;
						break;
					}
				}
				$lat=$sxe->result->geometry->location->lat;
				$lng=$sxe->result->geometry->location->lng;
				$status=true;
			}
			else{
				$lat=$sxe->result->geometry->location->lat;
				$lng=$sxe->result->geometry->location->lng;
				$status=true;
			}

		} else {
			try {
				if ($log) {
					ptLog::setPrefix(date('Ymd-').'ECHEC_Google');
					ptLog::logAll($adresse, $url, $xml);
				}
				$url=self::$urlYahoo.$adr.self::$finUrlYahoo;
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				if (array_key_exists('HTTP_USER_AGENT', $_SERVER) ) {
					curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
				}	
				$curl_response = curl_exec($curl);
				curl_close($curl);
				$xml = $curl_response;
				// $xml = @file_get_contents($url);
				if (!$xml) {
					throw new Exception('Pas de reponse valide du serveur.');
				}
				$sxe = new SimpleXMLElement($xml);
				$error = (int) $sxe->Error; $nbFound = (int) $sxe->Found;
				if ($log) {
					ptLog::setPrefix(date('Ymd-').'fallback_Yahoo');
					ptLog::logAll($origin, 'ville ou cp : ' . $adresse, $url, $xml, 'error : ' . $error, 'found : ' . $nbFound);
				}
				if (0 !== $error OR 0 === $nbFound) {
					throw new Exception ('Erreur Yahoo : %s / Nombre resultats : %s', $error, $nbFound);
				}
				$lat = $sxe->Result->offsetlat;
				$lng = $sxe->Result->offsetlon;
				$status = true;
			} catch (Exception $E) {
				if ($log) {
					ptLog::setPrefix(date('Ymd-').'ECHEC_Yahoo');
					ptLog::logAll($E->getMessage() );
					ptLog::logAll('ville ou cp : ' . $adresse, $url, $xml);
					ptLog::logAll(array("lat"=>$lat,"lng"=>$lng,"status"=>$status) );
				}
			}
		}

		return array("lat"=>$lat,"lng"=>$lng,"status"=>$status);
	} // /rechercheApiGoogle()
}