<?php
/**
 * @package pt
 * @subpackage web-services
 *
 * @see 
 * - infos sur BOSS : http://developer.yahoo.com/yql/guide/index.html
 * - exemples de code php : http://developer.yahoo.com/yql/guide/yql-code-examples.html#yql_php
 *
 * @author ffrey.web@gmail.com
 *
 * @todo : ajouter option cache/log ?
 */
class ptYahoo
{
	protected 
		$urlYahoo= 'select * from geo.placefinder where text = ';

	public function __construct($iTauxQualiteMinimum = 80) 
	{
		$this->iTauxQualiteMinimum = $iTauxQualiteMinimum;
	}
	
	/**
	 * 
	 * @param string $adresse
	 * @return mixed : array('lat', 'long') / false si rien trouvé
	 * @example : http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.placefinder%20where%20text%3D%222%20passage%20louis-philippe%2C%20paris%22
	 */
	public function geolocalize($adresse) {
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__; $log = true;
		$ret = false; // array
		
		$adresse = '"'.trim($adresse).'"';
		if ($db) {
            var_dump($origin
			, 'looking for : ', $adresse
			);
		}
		$url='http://query.yahooapis.com/v1/public/yql?q='.urlencode($this->urlYahoo.$adresse);
		$xml = $this->_curl($url);
		if ($db) {
            var_dump($origin
				, 'url', $url
				, 'response', $xml
			);			
			// 
			exit;
		}                
		try {
			$oXml = new SimpleXMLElement($xml);
			if ($db) { var_dump('SimpleXMLElement', $oXml); }
			if(is_null($oXml->results) ) {  
				throw new Exception('pas de resultats');
			}
			$result = $oXml->results->Result;
			if ($db) {
				var_dump($origin
					, 'result', $result
				);
				// exit;
			}
			$iQual = (int) $result->quality;
			if ($db) { var_dump($origin, 'QUALITE', $iQual); }
			if ($this->iTauxQualiteMinimum > $iQual) {
				throw new Exception ('Taux d\'incertidude trop grand : ' . $iQual);
			}
			$ret = array('lat' => (float) $result->latitude, 'long' => (float) $result->longitude);
		} catch (Exception $E) {
			if ($db) { var_dump('EXCEPTION', $E->getMessage() ); }
		}
		if ($db) {
			var_dump($origin
					, 'return', $ret
					);
			exit;
		}
		return $ret;
	} // /geolocalize()

	protected function _curl($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if (isset($_SERVER['HTTP_USER_AGENT']) ) {
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		}
		$curl_response = curl_exec($curl);
		curl_close($curl);
		$xml = $curl_response;
		return $xml;
	}
	
	/*
	public static function init($url,$oCacheFile=null){
		self::$url=$url;
	self::$oCacheFile=$oCacheFile;
	}
	
	public static function findCpFromVilleWithYahoo($ville){
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
	}
	*/
}