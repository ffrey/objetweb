<?PHP
/**
 * class static to perform custom tasks within any class
 *
 * @throws Exception <= pb : compels one to wrap call within a try/catch...
 * ? too heavy for static helper methods => return bool ?
 * @enhance ? rename class into mySfUtil() <= methods using sf bits (sfRequest as parameter, sfConfig used within method, etc.)
 *  / other methods (totally framework independent : _checkNeededFilled(), ...) => myUtil ?
 * @enhance : set 2 modes : dev => with checks (@see getTransEnums() ) / prod => no checks (for greater speed) ?
 * @uses sfContext
 * @uses sfLogger
 * @uses myPropel
 */
class owSf {
		private static $logger; // sfLogger
	private function __construct() {
		// static class
	}
	/*** *** TRANSLATIONS ***/
    /**
    * @ uses sfConfig
    */
	public static function getTranslations($dataType) {
		$A = array ();
		$A = sfConfig::get ( 'app_' . $dataType . '_list' );
		if (! $A or ! is_array ( $A )) {
			self::sf_log ( __FUNCTION__ . ' : no such type : ' . $dataType );
			$A = array ();
		}
		return $A;
	}
	public static function getTranslation($dataType, $value) {
		$ret = '';
		do {
			$A = self::getTranslations ( $dataType );
			if (! key_exists ( $value, $A )) {
				self::sf_log ( __FUNCTION__ . ' : no such value : ' . $value . ' in ' . print_r ( $A, true ) );
				break;
			}
			$ret = $A [$value];
		} while ( false );
		return $ret;
	}
	/**
	 * returns enum vals (CHECKED !) with translation => build selects
	 *
	 * @uses myPropel, sfInflector
	 * @return array : array(<enum val> => <human readable val>)
	 */
	public static function getTransEnums($class, $prop) {
		$ret = array ();
		$prop = sfInflector::underscore ( $prop );
		$appYmlVals = self::getTranslations ( $prop );
		if (! sizeof ( $appYmlVals )) {
			throw new Exception ( 'no such type (' . $prop . ') in app.yml' );
		}
		$trueVals = myPropel::getEnumValues ( $class, $prop );
		foreach ( $trueVals as $val ) {
			if (! key_exists ( $val, $appYmlVals )) {
				throw new Exception ( 'you shoul add key ' . $val . ' in app.yml in list ' . $prop );
			}
			$ret [$val] = $appYmlVals [$val];
			unset ( $appYmlVals [$val] );
		}
		if (sizeof ( $appYmlVals )) {
			throw new Exception ( 'unneeded keys in app.yml in list ' . $prop );
		}
		
		return $ret;
	}

	/*** *** SYMFONY WEB REQUEST HANDLING ***/
	/**
	 * extract info for redirection or link : module, action, params
	 *
	 * - ! if form inside page : extract params from request if first show of form (no referrer fields : 'P_module' & 'P_action')
	 * / from referrer fields if re-show after invalid data
	 *
	 * @param sfWebRequest $req
	 * @param array $without : @see _getParams()
	 * @return array : <module>, <action>, <?param1=val1&param2=val2...> OR ''
	 * @todo : replace by getRedirection ?
	 */
	public static function getQuery(sfWebRequest $req, $without = array()) {
		$ret = array ();
		$P_action = (! $req->hasParameter ( 'P_action' )) ? $req->getParameter ( 'action' ) : $req->getParameter ( 'P_action' );
		$P_module = (! $req->hasParameter ( 'P_module' )) ? $req->getParameter ( 'module' ) : $req->getParameter ( 'P_module' );
		$P_params = '';
		$P_params = self::_getParams ( $req, $without ); // BUG !
		$msg = 'P_params : ' . $P_params;
		// self::sf_log(__FUNCTION__ . ' : ' . $msg);
		

		return $ret = array ($P_module, $P_action, $P_params );
	}
	/**
	 * gets login-needing uri (before forwarding to auth form)
	 * - if POST => returns 'POST'
	 * - ! if auth form submission fails => requested Uri remains the same !!!
	 *
	 * @param sfWebRequest $req
	 * @param string $requestedUriField : name of the field where redirection uri is stored
	 * @return string : <uri> / 'POST'
	 */
	public static function getRequestedUri(sfWebRequest $req, $requestedUriField = 'P_requestedUri') {
		$uri = '';
		do {
			if ($req->hasParameter ( $requestedUriField )) { // auth form submission has failed
				$uri = $req->getParameter ( $requestedUriField );
				self::sf_log ( 'auth form being re-submitted => no new redirection' );
				break;
			}
			if ('POST' === $req->getMethod ()) {
				$uri = 'POST';
				self::sf_log ( 'no redirection due to Post' );
				break; // we won't redirect onto a form submission !
			}
			$uri = $req->getUri (); // ? should not work <= login failure triggers a REDIRECT ! => use getReferrer instead ?
			self::sf_log ( 'redirection : ' . $uri . ' [methodName : ' . $req->getMethod () . ']' );
		} while ( false );
		return $uri;
	}
	/**
	 * forwards to 'P_requestedUri' if param exists / returns false if param does not exist !
	 * @param $requestedUriField string : name of param containing the url to forward to
     * @use owSfHelper ! => @todo : make makeInternal() into a static method for greater coherency ?
	 */
	public static function forwardToRequestedUri($requestedUriField = 'P_requestedUri', sfAction $Act)
	{
		if (!$Act->getRequest()->hasParameter($requestedUriField) 
		OR '' === $Act->getRequest()->getParameter($requestedUriField) ) {
			return false;
		}
		$R = sfContext::getInstance()->getRouting();
		sfContext::getInstance()->getConfiguration()->loadHelpers('owSf');
		$wantedUri = makeInternal($Act->getRequest()->getParameter($requestedUriField) );
		$params = $R->parse($wantedUri );
		foreach ($params AS $name => $val) {
			if (!is_string($val) ) { continue; }
			if (in_array($name, array('module', 'action') ) ) { continue; }
			$Act->getRequest()->setParameter($name, $val);
		}
		$Act->forward($params['module'], $params['action']);
	}
    
    /*** *** LOG ***/
	/**
	 * passes msg (string OR array !) to sfLogger
	 *
	 * @param mixed $msg (string OR array !)
	 * @param string [*'info', 'alert']
     * @uses sfLogger
	 */
	public static function sf_log($msg, $type = 'info') {
		if (is_array ( $msg ) and count ( $msg )) {
			$msg = print_r ( $msg, true );
		}
		if (empty ( $msg ))
			return;
		if (sfConfig::get ( 'sf_logging_enabled' )) {
			if (null === self::$logger) {
				self::$logger = sfContext::getInstance ()->getLogger ();
			}
			$from = '[' . __CLASS__ . ']';
			switch ($type) {
				case 'err' :
					self::$logger->err ( $from . $msg );
					break;
				case 'alert' :
					self::$logger->alert ( $from . $msg );
					break;
				case 'info' :
				default :
					self::$logger->info ( $from . $msg );
			}
		}
	}
    
	
	/*** PROTECTED **
	protected static function _extractKeys(array $A) {
		$string = '';
		$A_tmp = array_keys ( $A );
		$string = implode ( ', ', $A_tmp );
		return $string;
	}
	*/
	/**
	 * @see getQuery()
	 * @param array $without : param not to include !
	 * @return string : <?param1=val1&param2=val2...> OR ''
	 * @todo : BUG : gets all vars : POST also !
	 */
	protected static function _getParams(sfWebRequest $req, $without = array()) {
		$P_params = '';
		$without = array_merge ( $without, array ('module', 'action' ) );
		$params = $req->getParameterHolder ()->getAll ();
		do {
			if (! (sizeof ( $params )) > 2)
				return true; // minimum = module + action
			$P_params = '?';
			foreach ( $params as $paramName => $value ) {
				if (in_array ( $paramName, $without ))
					continue;
				if (in_array ( $paramName, $_POST ))
					continue; // ? solves the BUG ? => CHECK
				$P_params .= $paramName . '=' . $value . '&';
			}
			$P_params = substr ( $P_params, 0, strlen ( $P_params ) - 1 );
		} while ( false );
		return $P_params;
	}
}