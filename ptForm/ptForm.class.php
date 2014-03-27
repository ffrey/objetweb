<?php

/**
 * Base project form.
 *
 * @package    b2c
 * @subpackage form
 * @author     FFREY
 */
class ptForm {

    protected
            $errors = array(), // messages d'erreur classes par chp
            $msg_ok = '',
            $rules = array(), // regles de validation classees par chp avec messages d'erreur
            $rawData = array(), // données fournies par l'internaute => utilisées pour le réaffichage si besoin
            $cleanedData = array(), // données filtrées et reformatées pour traitement par self::process()
            $errors_msg = array(),
            $sRequiredTag		= '<strong>*</strong>'
    ;
    protected $msgs = array(
        'fr' => array(
            'all' => 'Veuillez remplir les donn&eacute;es du formulaire.',
            'notvide' => 'Le champ ne peut &ecirc;tre vide.',
            'email' => 'Format d\'email invalide',
            'regex' => 'Format invalide',
            'number' => 'Seuls les nombres sont accept&eacute;s',
            'required' => 'Champ obligatoire !',
            'telephone' => 'Le format du num&eacute;ro de t&eacute;l&eacute;phone n\'est pas valide.',
        )
    );
    static public
    $regex = array(
        'email' 	=> '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i',
        'telephone' => '/^([0-9]{2}[-_\.\s\\/]?){4}[0-9]{2}$/',
    );

    /**
     * @author : sfValidatorEmail.class.php in sf project
     */
    public function checkEmail($chp, $msg = null) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        // $regex = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
        $regex = self::$regex['email'];
        $msg = (null !== $msg) ? $msg : $this->getDefaultErrorMsg('email');
        if ($db) {
            var_dump($origin, $msg);
        }
        $this->checkRegEx($chp, $msg, $regex);
    }

    public function checkTelephone($chp, $msg = null) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        // $regex = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
        $regex = self::$regex['telephone'];
        $msg = (null !== $msg) ? $msg : $this->getDefaultErrorMsg('telephone');
        if ($db) {
            var_dump($origin, $msg);
        }
        $this->checkRegEx($chp, $msg, $regex);
    }

    public function getRules() {
        return $this->rules;
    }

    /**
     * this 'check' function is defined because it has one mandatory extra parameter (instead of standard 2 : @see __call() )
     * @param string $chp
     * @param string $msg
     * @param string $regex
     */
    public function checkRegEx($chp, $msg, $regex) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        $rule = 'regex';
        $this->rules[$chp][$rule] = array('regex' => $regex);
        $this->errors_msg[$chp][$rule] = (null !== $msg) ? $msg : $this->getDefaultErrorMsg($rule);
        if ($db) {
            var_dump($this->errors_msg, $msg, $this->getDefaultErrorMsg($rule));
        }
    }
    
    public function checkRequired($chp, $msg = null, $notempty = true) {
    	$db = false;
    	$origin = __CLASS__ . '::' . __FUNCTION__;
    	$rule = 'required';
    	$this->rules[$chp][$rule] = array('notempty' => $notempty, 'chp' => $chp);
    	$this->errors_msg[$chp][$rule] = (null !== $msg) ? $msg : $this->getDefaultErrorMsg($rule);
    	if ($db) {
    		var_dump($this->errors_msg, $msg, $this->getDefaultErrorMsg($rule));
    	}
    }
    
    public function checkEnum($chp, $msg, $allowedValues)
    {
    	$db = false;
    	$origin = __CLASS__ . '::' . __FUNCTION__;
    	$rule = 'enum';
    	$this->rules[$chp][$rule] = array('allowedValues' => $allowedValues);
    	$this->errors_msg[$chp][$rule] = (null !== $msg) ? $msg : $this->getDefaultErrorMsg($rule);
    	if ($db) {
    		var_dump($this->errors_msg, $msg, $this->getDefaultErrorMsg($rule));
    	}
    }
    
    public function unsetRulesOn($chp)
    {
    	if (array_key_exists($chp, $this->rules) ) {
    		unset($this->rules[$chp]);
    	}
    }

    final public function getError($chp) {
        $msg = $this->errors;
        if (array_key_exists($chp, $msg)) {
            return $msg[$chp];
        }
        return '';
    }

    final public function getRawValue($chp, $default = null) {
        if (array_key_exists($chp, $this->rawData)) {
            return $this->rawData[$chp];
        }
        return $default;
    }

    final public function val($chp, $default = null) {
        return $this->getRawValue($chp, $default);
    }
    
    final public function showIfRequired($chp)
    {
    	$ret = '';
    	if ($this->isRequiredField($chp) ) {
    		$ret = $this->sRequiredTag;
    	}
    	return $ret;
    }

    final public function valIfNotEmpty($chp, $default = null)
    {
    	$ret = $this->val($chp, $default);
    	if (empty($ret) AND !is_null($default_val) ) {
    		$ret = $default_val;
    	}
    	return $ret;
    }
    
    final public function getRawValues() {
        return $this->rawData;
    }

    final public function setDefault($champ, $value) {
        $this->rawData[$champ] = $value;
    }

    final public function getCleanedValue($chp) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        if ($db) {
            var_dump($origin, $this->cleanedData);
        }
        if (array_key_exists($chp, $this->cleanedData)) {
            return $this->cleanedData[$chp];
        }
        return $this->getRawValue($chp);
    }

    final protected function setClean($chp, $val) {
        if (array_key_exists($chp, $this->rawData)) {
            $this->cleanedData[$chp] = $val;
        }
    }

    final public function setError($type, $val) {
        $this->errors[$type] = $val;
    }

    final public function setErrors(array $d) {
        foreach ($d AS $type => $val) {
            $this->setError($type, $val);
        }
    }

    final public function setMsg($msg) {
        $this->msg_ok = $msg;
    }

    final public function setRequiredTag($sTag)
    {
    	$this->sRequiredTag = $sTag;
    }
    
    final public function hasMsg() {
        return ('' != $this->msg_ok);
    }

    final public function getMsg() {
        return $this->msg_ok;
    }

    public function isRequiredField($chp) {
        if (!array_key_exists($chp, $this->rules)) {
            return false;
        }
        if (!array_key_exists('required', $this->rules[$chp])) {
            return false;
        }
        return true;
    }

    public function isNotVideField($chp) {
        if (!array_key_exists($chp, $this->rules)) {
            return false;
        }
        if (!array_key_exists('notvide', $this->rules[$chp])) {
            return false;
        }
        return true;
    }

    /**
     * ATTENTION : cette methode utilise bind(), qui lui-meme reset les errors !!!
     * => si override : appeler cette methode au DBT !
     * 
     * @param array $chps
     */
    public function isValid(array $chps = null) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        if ($db) {
            print '***DEBUT ' . $origin . '***' . "\n\r";
        }
        if (is_array($chps)) {
            $this->bind($chps);
        }

        do {
            if (!count($this->rawData)) {
                $msg = $this->msgs['fr']['all'];
                $this->errors['all'] = $msg;
                break;
            }
            $this->clean();
            if ($db) {
                print '***' . $origin . '***' . __LINE__ . "\n\r";
                var_dump($this->rules);
            }
            if (!count($this->rules)) {
                break;
            }
            if ($db) {
                print '***' . $origin . '***' . __LINE__ . "\n\r";
                var_dump('got', $this->getCleanedValues() );
            }
            // if ($db) { var_dump($origin, $this->rules);
            // exit;
            // }
            foreach ($this->rules AS $chp => $r) {
                foreach ($r AS $rule => $args) {
                    $method = 'is' . $rule;
                    $value = $this->getCleanedValue($chp);
                    if (empty($value) AND !$this->isRequiredField($chp) AND !$this->isNotVideField($chp)) {
                        break; // si un chp est vide et n'est pas obligatoire,
                        // alors, on ne fait pas de verification sur ce champ !
                    }
                    $args['value'] = $value;
                    if ($db) {
                        print $origin . ' : checking ' . $chp . ' ' . $method . "\n\r<br/>";
                    }
                    $ok = $this->$method($args);
                    if (!$ok) {
                        $this->errors[$chp] = (@isset($this->errors_msg[$chp][$rule]) ) ? $this->errors_msg[$chp][$rule] : $this->getDefaultErrorMsg($rule);
                        if ($db) {
                            var_dump('adding error', $this->errors[$chp]);
                        }
                        break; // on passe au chp suivant
                    }
                }
            }
        } while (false);
        if ($db) { exit('fin : ' . $origin); }
        return !$this->hasErrors();
    }

    public function hasErrors() {
        return count($this->errors) ? true : false;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function unbind() {
        $this->rawData = array();
        $this->errors = array();
    }

    public function bind(array $values) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        $this->errors = array();
        // delete all rules which have no values !!!
        // except if REQUIRED !!!
        foreach ($this->rules AS $chp => $val) {
            if ($db) {
                var_dump($origin, 'chp', $chp, 'val', $val);
            }
            if (array_key_exists('required', $val)) {
                continue;
            }
            if (!array_key_exists($chp, $values)) {
                unset($this->rules[$chp]);
            }
        }
        //  @TODO : temporaire : cleanedData devrait etre processed ici au lieu de isValid() ???
        $this->rawData = $values;
        $this->clean();
    }

    /**
     * renomme les cles de $toTranslate suivant le tableau $correspondances
     *
     * @param array $correspondances : 'champ de formulaire' => 'champ de base de données'
     * @param array $toTranslate
     */
    public function translateKeys(array $correspondances, array $toTranslate) {
        foreach ($correspondances AS $chp_form => $chp_bdd) {
            if (array_key_exists($chp_form, $toTranslate)) {
                $toTranslate[$chp_bdd] = $toTranslate[$chp_form];
                unset($toTranslate[$chp_form]);
            }
        }
        return $toTranslate;
    }

    /**
     * ! this function should be overwritten if needed !
     * => place here all the processing needed on values prior to validation
     * 
     * @see self::bind()
     */
    protected function clean() {
        $this->cleanedData = $this->rawData;
        // 		$val = $this->getRawValue('chp1');
        // 		$c   = trim($val);
        // 		$this->setClean('chp1', $c);
    }

    public function getCleanedValues() {
        return $this->cleanedData;
    }

    protected function getDefaultErrorMsg($type) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        if ($db) {
            var_dump($origin, $type, $this->msgs);
        }
        $ret = (array_key_exists($type, $this->msgs['fr']) ) ? $this->msgs['fr'][$type] : '';
        return $ret;
    }

    protected function isrequired($args, $notempty = true) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        $notempty = isset($args['notempty'])? $args['notempty'] : $notempty;
        if ($db) {
        	var_dump($origin, 'got', $args, 'not empty', $notempty);
        }
        
        if ($notempty) {
            return $this->isnotvide($args);
        }
        if ($db) {
            var_dump($origin, $args
            , 'existe ?', $this->rawData
            );
        }
        return array_key_exists($args['chp'], $this->rawData);
    }
    
    protected function isnotvide($args) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        $value = $args['value'];
        $ret = !in_array($value, array(null, '', array()), true);
        if ($db) {
            var_dump($origin, 'value', $value, 'return', $ret);
        }
        return $ret;
    }

    protected function isNumber($args) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        $value = $args['value'];
        $ret = is_numeric($value);
        if ($db) {
            var_dump($origin, 'value', $value, 'return', $ret);
        }
        return $ret;
    }

    protected function isregex($args) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        $value = $args['value'];
        $regex = $args['regex'];
        $ret = preg_match($regex, $value);
        if ($db) {
            var_dump($origin, 'value', $value, 'return', $ret);
        }
        return $ret;
    }

    protected function isenum($args) {
    	$db = false;
    	$origin = __CLASS__ . '::' . __FUNCTION__;
    	$value = $args['value'];
    	$aVals = $args['allowedValues'];
    	$ret = in_array($value, $aVals);
    	if ($db) {
    		var_dump($origin, 'value', 'vals', $aVals, $value, 'return', $ret);
    		exit;
    	}
    	return $ret;
    }
    
    /**
     * this function allows to not define check<Rule>()
     * => you only have to implement is<Rule>($field_name, $error_msg = null)
     * (ie : checkRequired(), checkNotVide(),...)
     * @param : la methode check doit ***obligatoirement*** avoir :
     * - param 1 : nom du chp
     * - param 2 : message d'erreur
     * @throws Exception
     */
    public function __call($name, $arguments) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
        if (0 === strpos($name, 'check')) {
            $Cond = substr($name, 5);
            $rule = strtolower($Cond);
            $method = 'is' . $rule;
            if (!method_exists($this, $method)) {
                throw new Exception('Regle de validation inconnue : ' . $rule);
            }
            if ($db) {
                var_dump($origin, $rule, $arguments);
            }
            $chp = $arguments[0];
            $msg = (@isset($arguments[1]) ) ? $arguments[1] : null;
            unset($arguments[0], $arguments[1]);
            $args = array();
            if (count($arguments)) {
                $args = $arguments;
            }
            $args['chp'] = $chp;
            $this->rules[$chp][$rule] = $args;
            $this->errors_msg[$chp][$rule] = (null !== $msg) ? $msg : $this->getDefaultErrorMsg($rule);
            if ($db) {
                var_dump('errors_msg', $this->errors_msg);
            }
            return true;
        }
        throw new Exception('Methode inconnue : ' . $name);
    }

}
