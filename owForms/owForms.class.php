<?php
/**
 * class to translate default error messages in sf forms framework
 * + util methods : 
 */

class owForms {

	public static function makeChoiceArray($a)
	{
		$ret = array(); 
		foreach ($a AS $v) {
			$val = $v;
		if (is_string($v) ) $val = ucfirst($v);
			$ret[$v] = $val;
		}
		return $ret;
	}
	/**
	 * display field value without input within a form
	 * * ! do not forget to modify the view accordingly !!!
	 *       * eaysy way : <?php echo $form ?> <= you are certain all fields will be printed
	 *       * more arduous : add manually the added display fields... !
	 * * ! setPositions should be called prior ! (because display fields
	 * ... are added here, and setPositions needs all fields)
	 */
	public static function display($fields, sfForm $f) {
		$w = $f->getWidgetSchema ();
		//		myUtil::db($w); exit;
		$v = $f->getValidatorSchema ();
		$oldPos = $w->getPositions();
		$newFields = array();
		foreach ( $fields as $field ) {
			if (strstr ( $field, ' ' )) {
				throw new Exception ( 'space is not allowed in field names : ' . $field );
			}
			if (! @isset ( $w [$field] )) {
				throw new Exception ( 'unknown field : ' . $field );
			}
			$display_field = 'display_' . $field;
			// ! if field is required => add hidden input !
			if ($v [$field]->getOption ( 'required' )) {
				$w [$field] = new sfWidgetFormInputHidden ( );
			} else {
				// 				print 'we unset ' . $field . '<br/>';
				unset($w[$field]);
				unset($v [$field]); // ! we must not forget to unset associated validator, lest value is set to Null !
			}
			// myUtil::db('label : ' . $w [$field]->getLabel() . ' / ' );
			$get = 'get' . sfInflector::camelize ( $field );
			$value = $f->getObject ()->$get ();
			// print 'New disply field : ' . $display_field . ' [method ' . $get . ' with value of ' . $value . '<br/>';
			$w [$display_field] = new owWidgetFormDisplay ( array ('display_value' => $value, 'label' => ucfirst ( $field ) ) );
			$v [$display_field] = new sfValidatorPass ( );
			$newFields[$field] = $display_field;
		}
		self::restorePositions($newFields, $oldPos, $w);	
	}
	/**
	 * unsets non-required fields / hides required fields
	 * ! only for 'edit' ! (for create : required fields must be 
	 * present anyhow !)
	 */
	public static function hide(array $fields, sfForm $f) {
		$w = $f->getWidgetSchema ();
		$v = $f->getValidatorSchema ();
		foreach ( $fields as $field ) {
			if (! @isset ( $w [$field] )) {
				throw new Exception ( 'unknown field : ' . $field );
			}
			// ! if field is required => add hidden input !
			if ($v [$field]->getOption ( 'required' )) {
				$w [$field] = new sfWidgetFormInputHidden ( );
			} else {
				unset ( $f [$field] );
			}
		}
	}
	public static function _unset(array $fields, sfForm $F) {
	$W = $F->getWidgetSchema ();
	$V = $F->getValidatorSchema ();
		foreach($fields AS $field) {
		if (! @isset ( $W[$field] )) {
				throw new Exception ( 'unknown field : ' . $field );
			}
			unset($W[$field]); unset($V[$field]);
		}
	}
	
	public static function getRequiredFields(sfForm $F)
{
$ret = array();
			$w = $F->getWidgetSchema ();
		$v = $F->getValidatorSchema ();
		foreach ( $v->getFields() as $field => $V) {
			if (! @isset ( $w [$field] )) {
				throw new Exception ( 'unknown field : ' . $field );
			}
			// ! if field is required => add hidden input !
			if ($v [$field]->getOption ( 'required' )) {
				$ret [$field] = $field;
			} 
		}
		return $ret;
}	
	/*** TRANSLATION methods ***/
	/**
	 * some error msgs in french...
	 * @todo : retrieve all default messages from form sf fw !
	 */
	protected static $langs = array ('fr' => array (
	'required' => 'obligatoire.', 
	'invalid' => '"%value%" est non valide', 
	'max_length' => '"%value%" est trop long (%max_length% caractères max).', 
	'min_length' => '"%value%" est trop court (%min_length% caractèress min).',
	) );
	
	public static function translateTo($lang, sfForm $F) {
		$l = explode ( '_', $lang );
		if (! key_exists ( $l [0], self::$langs )) {
			throw new Exception ( 'lang unavailable : ' . $l [0] );
		}
		foreach ( $F->getValidatorSchema ()->getFields () as $field => $V ) {
			// myUtil::db ( $field );
			$V->setMessages ( self::$langs [$l [0]] );
		}
	}
	
		/*** *** protected ***/
/**
	 * @param array $newFields : array('<field>' => 'display_<field>'[,...])
	 */
	protected static function restorePositions(array $newFields, array $oldPositions, sfWidgetFormSchema $w)
	{
		$newPositions = array();
		// at this stage, $w->getPositions();  unset fields are not there anymore / new display fields are there (pushed at end of array) !
		//... whereas unset fields are still in $oldPositions (got at start of self::display() : we need them to set corresponding new display fields !
		foreach ($oldPositions as $field) {
			if (in_array($field, array_keys($w->getFields() ) ) ) {
				$newPositions[] = $field; // we retrieve only set fields (	not those unset over self::display() !)		
			}
			if (key_exists($field, $newFields) ) {
				// print 'new : ' . $field . '<br/>';
				$newPositions[] = $newFields[$field]; //... plus new ones !
				// print $newFields[$field] . '<br/>';
			}
		}
		$w->setPositions($newPositions); // check on diff $newPositions, array_keys($w->getFields()) is performed !
	}
    
    /**
          * render label WITH required symbol if needed
          */
    public static function renderLabel($field, sfForm $F)
    {
        $WS = $F->getWidgetSchema ();
        $VS = $F->getValidatorSchema ();

        $V = $VS[$field]; $W = $WS[$field];
        $ret = $W->renderLabel();
        if ($V->hasOption('required') AND true === $V->getOption('required') ) {
           print $ret .= '<h4> REQ !<h4>';
        }
        return $ret;
    }
    /**
 *
   * Add $symbol to required field labels (inside <label>)
   * @enhance :
   * * allow adding a class to <label> (@see dev owWidgetFormSchemaFormatterListReq.class.php)
   *
   *
   * @author Gordon Franke 
   * @param sfForm $form
   * @param string $symbol
   * @param string $title
   *
   * @return void
   */
  public static function addRequiredToLabel(sfForm $form, $symbol = '*') // , $title = 'This field is mandatory.')
  {
    $widgetSchema = $form->getWidgetSchema();
    $validatorSchema = $form->getValidatorSchema();
 
    foreach($form->getFormFieldSchema()->getWidget()->getFields() as $key => $object){
      $label = $form->getFormFieldSchema()->offsetGet($key)->renderLabelName();
      if(isset($validatorSchema[$key]) and true === $validatorSchema[$key]->getOption('required') ) {
        //         $label .= '<sup title="' . $widgetSchema->getFormFormatter()->translate($title) . '">' . $symbol . '</sup>';
        $label .= $symbol;
      }
      $widgetSchema->setLabel($key, $label);
    }
  }


}