<?PHP
/**
 * class static to perform custom tasks within any class
 *
 * @throws Exception <= pb : compels one to wrap call within a try/catch...
 * ? too heavy for static helper methods => return bool ?
 * @enhance : set 2 modes : dev => with checks (@see getTransEnums() ) / prod => no checks (for greater speed) ?
 */
class myUtil {
	private static $log = array ('anonymous' => '' );
	private function __construct() {
		// static class
	}
	
	/*** custom ***/
	/**
	 * implement full checking :
	 * @see QuickForm, sf 1.1
	 * @param
	 * $dataToCheck (usually raw $_POST !)
	 * $checks = array(
	 * 'fiche_id' => 'filled, int, range(2)'
	 * 'titre'    => 'filled, range(1, 50)'
	 * 'body'     => 'set'
	 * etc.
	 * )
	 * @return
	 * $errors       = '' if no error / array(
	 * 'fiche_id' => '<msg_error>',
	 * etc.
	 * )
	 */
	/**
	 * checks all vars specified in $A_filled are properly filled in $A_data
	 * @todo add type checking for int/string/bool (array & Class already built-in)
	 *
	 */
	public static function _checkNeededFilled(array $A_filled, array $A_data) {
		$missingKey = array ();
		$emptyKey = array ();
		$msg_error = '';
		foreach ( $A_filled as $key ) {
			if (! key_exists ( $key, $A_data )) {
				$missingKey [] = $key;
				continue;
			}
			if (empty ( $A_data [$key] )) {
				$emptyKey [] = $key;
			}
		}
		if (sizeof ( $missingKey )) {
			$msg_error .= 'Valeurs manquantes : ' . implode ( ', ', $missingKey );
		}
		if (sizeof ( $emptyKey )) {
			$msg_error .= 'Valeurs vides : ' . implode ( ', ', $emptyKey );
		}
		if ($msg_error) {
			throw new Exception ( __CLASS__ . '::' . $msg_error );
		}
	}
	/**
	 * idem : except vars can be empty
	 *
	 */
	public static function _checkNeededSet(array $A_needed, array $A_data) {
		$msg = __CLASS__ . '::' . __FUNCTION__ . ':';
		$missingKey = array ();
		$msg_error = '';
		foreach ( $A_needed as $keyNeeded ) {
			if (! key_exists ( $keyNeeded, $A_data )) {
				$missingKey [] = $keyNeeded;
				continue;
			}
		}
		if (sizeof ( $missingKey )) {
			$missingKey = array_flip ( $missingKey );
			// throw new Exception ( $msg . 'missing fields : ' . self::_extractKeys ( $missingKey ) );
			throw new Exception ( $msg . 'missing fields : ' . implode(', ', array_keys( $missingKey ) ) );
		}
	}
    public static function _checkAreIn(array $vals, array $acceptedVals) {
        foreach ($vals AS $val) {
            self::_checkIsIn($val, $acceptedVals);
        }
    }
	public static function _checkIsIn($val, array $acceptedVals) {
        if (is_array($val) ) {
            foreach ($val AS $v) {
                self::_checkOneValIsIn($v, $acceptedVals);
            }
            return;
        }
		self::_checkOneValIsIn($val, $acceptedVals);
	}
    protected static function _checkOneValIsIn($val, array $acceptedVals)
    {
        $msg = __CLASS__ . '::' . __FUNCTION__ . ': ';
		if (! in_array ( $val, $acceptedVals )) {
			throw new Exception ( $msg . $val . ' is not in accepted values : ' . implode ( ', ', $acceptedVals ) );
		}
    }
    /**
    * @param $acceptedOptions array : keys are accepted options / values are default values
    * @param $options                       ! no value === null ! => default used if any
    */
    public static function getOpt($options, $acceptedOptions)
    {
        self::_checkAreIn(array_keys($options), array_keys($acceptedOptions) );
        $ret = array();
        foreach ($acceptedOptions AS $opt => $defaultVal) {
            $newVal = null;
            if (isset($options[$opt]) AND !is_null($options[$opt]) ) {
                $newVal = $options[$opt];
            } else {
                $newVal = $defaultVal;
            }
            $ret[$opt] = $newVal;
        }
        return $ret;
    }
	
	/*** LOG ***/
	/**
	 * temporary storage => to be retrieved with getLog()
	 *
	 * @param mixed $msg
	 */
	public static function log($msg, $ns = '') {
		if (is_array ( $msg )) {
			$msg = print_r ( $msg, true );
		}
		if (! empty ( $ns )) {
			if (! isset ( self::$log [$ns] ))
				self::$log [$ns] = '';
			self::$log [$ns] .= $msg;
		} else {
			self::$log ['anonymous'] .= $msg;
		}
	}
	public static function getLog($ns = '') {
		if (! empty ( $ns )) {
			if (! isset ( self::$log [$ns] ))
				self::$log [$ns] = '[no log for ' . $ns . ']';
			$ret = self::$log [$ns];
			self::$log [$ns] = '';
		} else {
			$ret = self::$log ['anonymous'];
			self::$log ['anonymous'] = '';
		}
		return $ret;
	}
	
	/*** *** DEBUG ***/
	/**
	* @todo : add support for multi args <= less calls to myUtil::db inside of one function !
	* @example : myUtil::db(__function__, $this); instead of myUtil::db(__function__); myUtil::db($this) !
	*/
	public static function db($data) {
		$dump = '';
		if (is_string ( $data ) or is_numeric ( $data )) {
			print $dump = '<h4>' . $data . '</h4>' . "\n\r";
		} elseif (is_bool ( $data )) {
			$val = (true === $data) ? 'True' : 'False';
			print $dump = '<h5>Boolean ' . $val . '</h5>' . "\n\r";
		} else { // : array, class,...
			print '<h5><pre>';
			var_dump($data);
			print '</h5></pre>' . "\n\r";
			// $dump = 'DUMP : <pre>' . print_r ( $data, true ) . '</pre>'; <= too memory intensive for objects !!!
		}
		return;
	}
	
	
    /*
	public static function validEnum($class, $prop, $value) {
		$trueVals = myPropel::getEnumValues ( $class, $prop );
		if (! in_array ( $value, $trueVals )) {
			throw new Exception ( 'Invalid enum value : ' . $value . ' [valid : ' . implode ( ',', $trueVals ) );
		}
		return $value;
	}
    */
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
	 
	public static function getQuery(sfWebRequest $req, $without = array()) {
		$ret = array ();
		$P_action = (! $req->hasParameter ( 'P_action' )) ? $req->getParameter ( 'action' ) : $req->getParameter ( 'P_action' );
		$P_module = (! $req->hasParameter ( 'P_module' )) ? $req->getParameter ( 'module' ) : $req->getParameter ( 'P_module' );
		$P_params = '';
		$P_params = self::_getParams ( $req, $without ); // BUG !
		$msg = 'P_params : ' . $P_params;
		// self::sf_log(__FUNCTION__ . ' : ' . $msg);
		

		return $ret = array ($P_module, $P_action, $P_params );
	}*/
	/**
	 * gets login-needing uri (before forwarding to auth form)
	 * - if POST => returns 'POST'
	 * - ! if auth form submission fails => requested Uri remains the same !!!
	 *
	 * @param sfWebRequest $req
	 * @param string $requestedUriField : name of the field where redirection uri is stored
	 * @return string : <uri> / 'POST'
	 
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
	*/
	/*** ARRAY HANDLING ***/
	/**
	 * split $A into $slices sub arrays
	 * @todo manage uneven lengths !!! / see if enhance poss with array_chunk() !
	 
	public static function _split_into($A, $slices) {
		$A_ret = array ();
		
		$size = count ( $A );
		$sizeOfSlice = $size / $slices;
		$slice = floor ( $sizeOfSlice );
		$offset = 0;
		$surplus = 0;
		if ($sizeOfSlice != $slice)
			$surplus = ($sizeOfSlice - $slice) * $slice;
		for($i = 1; $i <= $slices; $i ++) {
			$s = $slice;
			if (0 < $surplus) {
				$surplus --;
				$s = $slice + 1; // if uneven => odd values added to first array(s)
			}
			$A_ret [] = array_slice ( $A, $offset, $s );
			$offset += $s;
		}
		return $A_ret;
	}
    */
	/**
	 * callback function to sort ojects in a array on their __toString() function !
	 * @example usort($a, array("myUtil", "cmp_obj")
	 *
	 * @return int
	 * @deprecated : better to use ASC on sql query (or addAscendingOrder... for Criteria object)
	 */
	public static function cmp_obj($a, $b) {
		if (! method_exists ( $a, '__toString' )) {
			throw new Exception ( 'myUtil::cmp_obj : objects need a defined __toString() method to be sorted !' );
		}
		$al = strtolower ( $a );
		$bl = strtolower ( $b );
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? + 1 : - 1;
	}
	/*** PROTECTED **
	protected static function _extractKeys(array $A) {
		$string = '';
		$A_tmp = array_keys ( $A );
		$string = implode ( ', ', $A_tmp );
		return $string;
	}*/
	
	/**
	 * @see getQuery()
	 * @param array $without : param not to include !
	 * @return string : <?param1=val1&param2=val2...> OR ''
	 * @todo : BUG : gets all vars : POST also !
	 
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
    */
}