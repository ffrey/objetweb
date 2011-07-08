<?php
/**
 *
 */

class owCodes 
{
	protected $codes = array();
	
	public function __construct(array $codes)
	{
		$this->codes = array_change_key_case($codes, CASE_LOWER);
	}	
	
	public function getCode($type, $ns = null, $default = null) 
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$type = strtolower($type);
		if (!is_null($ns) ) {
			if (!array_key_exists($ns, $this->codes) ) {
				return $default;
			}
			$codes = $this->codes[$ns];
		} else {
			$codes = $this->codes;
		}
		if (!array_key_exists($type, $codes) ){
			if ($db) {
				var_dump($origin, 'type inconnu', $type, $codes, $default);
			}
			return $default;
		}
		return $codes[$type];
	}
}