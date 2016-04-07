<?php

/**
 * 
 * 
 * @package    b2c
 * @subpackage utils
 * @author FFreyssenge
 *
 */
class owContexte {

	static protected
			$storage = array(),
			$session = null;

	/**
	 * @throws Exception SSI $default is null
	 */
	static public function get($key, $default = null)
	{
		if (array_key_exists($key, self::$storage)) {
			return self::$storage [$key];
		} else if (!is_null($default)) {
			return $default;
		} else {
			throw new Exception('Variable inconnue : ' . $key);
		}
	}

	static public function set($key, $value)
	{
		self::$storage [$key] = $value;
	}

	/** GESTION CONTEXTE DE PAGE * */
	static public function setPage($page)
	{
		self::set('page', $page);
	}

	static public function setGroupe($groupe)
	{
		if (is_object(self::$session) AND method_exists(self::$session, 'set')) {
			self::$session->set('groupe', $groupe);
			return true;
		}
		self::set('groupe', $groupe);
	}

	static public function getPage()
	{
		return self::get('page', '');
	}

	static public function getMeta()
	{
		return self::get('metas', array());
	}

	static public function getGroupe()
	{
		return self::get('groupe', '');
	}

	static public function isGroupe($groupe, $classe = '')
	{
		if ($classe == '') {
			if (self::get('groupe', '') == $groupe || (is_array($groupe) AND in_array(self::get('groupe', ''), $groupe) )) {
				return true;
			} else {
				return false;
			}
		} else {
			if (self::get('groupe', '') == $groupe || (is_array($groupe) && in_array(self::get('groupe', ''), $groupe))) {
				return $classe;
			}
		}
	}

	/**
	 *
	 * @param mixed string|array : $page 
	 *    if string => accepts glob format ! (not in a array !)
	 * @param string $classe        	
	 * @return mixed : $classe|boolean
	 */
	static public function isPage($page, $return = null)
	{
		$db = false;
		$origin = __CLASS__ . '::' . __METHOD__;
		$ret = false;
		do {
			if (is_array($page)) {
				$ret = in_array(self::get('page', ''), $page);
				break;
			}
			$ret = self::_checkPage($page);
		} while (false);

		if ($ret AND is_string($return) ) {
			return $return;
		}
		return $ret;
	}
	
	static protected function _checkPage($page)
	{
		return fnmatch($page, self::get('page', '') );
	}
}
