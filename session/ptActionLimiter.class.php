<?php
/**
 * limits the nb of requests per time-span within one session for a given action
 * 
 * @author FFREY
 * @todo add handling of ip ?
 *       example : ActionLimiter::isActionOverLimit($action, $ip)
 *       => the nb of requests would be calculated on a per-ip basis
 *       => asset : the storage could be done in a non-session container (db, file, etc.)
 *
 */
class ptActionLimiter
{
	static protected 
	$defaults = array(
			'span'      => 10,
		    'max'       => 10,
			'namespace' => 'ActionLimiter',
	),
	$config = array();
	
	/**
	 * 
	 * @param array $init : [0] => array('action' => '', 'max' => '', 'span' => '')
	 * @param array $storage
	 * @return bool
	 */
	public static function initActionLimits(array $init, array $defaults = null)
	{
		if (!session_id() ) {
			return false;
		}
		if (null != $defaults) {
			$d = array_intersect_key($defaults, self::$defaults);
			self::$defaults = array_merge(self::$defaults, $d);
		}
		foreach ($init AS $l) {
			if (is_array($l) ) {
				self::checkInit($l);
				self::setActionLimit($l);
			}
		}
		self::initStorage();
		return true;
	} // /initActionLimits()

	/**
	 * 
	 * @param string $action
	 * @return bool
	 */
	public static function isActionOverLimit($action)
	{
		if (!session_id() ) {
			return false;
		}
		if (!array_key_exists($action, self::$config) ) {
			return false;
		}
		list($span, $max) = self::getLimits($action);
		if (0 == $max) {
			return true;
		}
		self::clearActionsBefore($span, $action);
		$nbActionsDepuisSpan = self::getActionNumber($action);
		if ($max < $nbActionsDepuisSpan+1) {
			return true;
		}
		self::addAction($action, time() );

		return false;
	} // /isActionOverLimit()
	
	/**
	 * @return array : 0 => span, 1 => max
	 */
	public static function getLimits($action)
	{
		if (!array_key_exists($action, self::$config) ) {
			return array(0, 0);
		}
		$c = self::$config[$action];
		return array($c['span'], $c['max']);
	}
	
	public static function getActionNumber($action)
	{
		$store = self::getStorage();
		if (!array_key_exists($action, $store) ) {
			return 0;
		}
		return count($store[$action]);
	}
	
 	/*** PROTECTED ***/
	
	protected static function clearActionsBefore($span, $action)
	{
		$now = time();
		$start = $now - $span;
		$store = self::getStorage();
		if (array_key_exists($action, $store) ) {
			foreach ($store[$action] AS $l) {
				if ($start > $l) {
					array_shift($store[$action] );
				}
			}
		}
		self::store($store);
	} // /clearActionsBefore()
	
	protected static function addAction($action, $time = null)
	{
		if (null === $time) {
			$time = time();
		}
		$store = self::getStorage();
		if (!array_key_exists($action, $store) ) {
			$store[$action] = array();
		}
		$times = $store[$action];
		$times[] = $time;
		sort($times);
		$store[$action] = $times;
		self::store($store);
	} // /addAction()
	
	protected static function setActionLimit(array $l)
	{
		$action = $l['action'];
		unset($l['action']);
		$d = self::$defaults;
		unset($d['namespace']);
		self::$config[$action] = array_merge($d, $l);
	}

	protected static function checkInit(array $l)
	{
		if(!array_key_exists('action', $l) ) {
			throw new Exception (sprintf(__CLASS__.'::'.__FUNCTION__.' : missing action key : %s', implode(', ', $l) ) );
		}
	}
	
	/*** *** STORAGE *** ***/
	
	protected static function getStorage()
	{
		$ns = self::$defaults['namespace'];
		if (!array_key_exists($ns, $_SESSION) ) {
			return array();
		}
		return $_SESSION[$ns];
	}
	
	protected static function store(array $store)
	{
		$ns = self::$defaults['namespace'];
		$_SESSION[$ns] = $store;
	}
	
	protected static function initStorage()
	{
		$ns = self::$defaults['namespace'];
		if (!array_key_exists($ns, $_SESSION) ) {
			$_SESSION[$ns] = array();
		}
	}
	/*** *** *** ***/
}