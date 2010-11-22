<?php
/**
 * @see test in C:\wamp\lib\ow
 */
class owExc extends Exception
{
	static protected $d = array(
	 'msgSep' => '##', 
	 'defaultPublicMsg' => 'Un probl&egrave;me technique est survenu.',
	 'strict' => false,
	);
	
	 static public function set($var, $val)
	 {
	 	$origin = __CLASS__.'::'.__FUNCTION__;
	 	if (!array_key_exists($var, self::$d) ) {
	 		throw new owExc($origin . ' : unknown var ' . $var . ' ## ');
	 	}
	 	self::$d[$var] = $val;
	 }
	 
	/**
	 * provides an easy way to distinguish between end-user readable msg & debug/technical msg
	 * 
	 * @example :
	 * 'line 221 error fetching header ## Le service n\'est pas disponible'
	 * => 'Le service n\'est pas disponible'
	 * 'Contexte::getEnvt : no env defined ## '
	 * => $defaultMsg
	 * 'Database not available'
	 * => 'Database not available'
	 */
	public function getPublicMsg()
	{
		$origin = __CLASS__.'::'.__FUNCTION__; $db = false;
		$msg = $this->getMessage();
		$p = explode(self::$d['msgSep'], $msg);
		$nb = count($p);
		if (self::$d['strict'] AND 2 < $nb) {
			throw new owExc($origin.' : msgSep found ' . $nb . ' times / 1 allowed ## ');
		}
		if ($nb) {
			$publicMsg = '';
			if (1 < $nb) { $publicMsg = trim($p[1]); }
			if (empty($publicMsg) ) {
				$publicMsg = self::$d['defaultPublicMsg'];
			}
			return $publicMsg;
		}
		return $msg;
	} // /getPublicMsg
	
	public function getPublicMessage()
	{
		return $this->getPublicMsg();
	}
}