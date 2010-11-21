<?PHP
/**
 * @uses all Propel classes
 */
class myPropel {
  /**
	 * extract relevant fields from $_POST
	 *
	 * @param unknown_type $objet
	 * @param array $data
	 * @return array
	 * @todo
	 * - check handling of array values !
	 * => ex. : for Enums... <= coming from radio buttons
	 
  public static function getFields(BaseObject $objet, array $data)
  {
    $logger = sfContext::getInstance()->getLogger();
    $msg = __CLASS__ . '::' . __FUNCTION__ . ' : ';

    $d = array();
    $className = get_class($objet) . 'Peer';
    $msg .= ' [pour classe ' . $className . '] ';
    $classPeer = new $className;
    $tableFields = $classPeer->getFieldNames(BasePeer::TYPE_PHPNAME); // to check field exists !
    $msg .= ' [data received : ' . print_r($data, true) . ']';
    $msg .= ' [fields accepted : ' . print_r($tableFields, true) . ']';
    foreach ($data as $field => $value) {
      $phpNameField = sfInflector::camelize($field);
      $msg .= ' > ' . $phpNameField . '(' . $value . ') ? ';
      if ( !in_array($phpNameField, $tableFields) ) { continue; }
      if ( is_array($value) ) { // handling of array values => {@todo}
        $msg .= ' is array !';
        $v = '';
        foreach ($value as $val) {
          $v .= $val;
          if (next($value)) { $v .= ', '; }
        }
        $value = $v;
      }
      if ( empty($value) OR null === $value ) { continue; }
      $msg .= ' YES !-D';
      $d[$phpNameField] = $value;
    }

    $logger->debug($msg);
    return $d;
  }*/
  /**
	 * @todo useful ? <= fromArray() not the same ???
	 
  public static function setFields(BaseObject $objet, array $data)
  {
    owSf::sf_log(__FUNCTION__ . ' DEPRECATED');
	$objet->fromArray($data, BasePeer::TYPE_FIELDNAME);
	
    foreach ($data as $field => $value)
    {
      if ('' == $value) {
        owSf::sf_log(__FUNCTION__ . ' : ' . $field . ' vide => not set');
        continue; // BUG : if '' => 0 for int !
      }
      owSf::sf_log(' set field : ' . $field . ' => value ' . $value);
      $setField = 'set' . $field;
      $objet->$setField($value);
    }

	}
*/
  /**
	 * @todo useful ? <= fromArray() not the same ???
	 
  public static function getAndSetFields(BaseObject $objet, array $data)
  {
    $msg = __CLASS__ . '::' . __FUNCTION__ . ' : ';

    $d = self::getFields($objet, $data);
    if (!sizeof($d)) { owSf::sf_log($msg . ' VIDE => pas de chps gardés !!!'); }
    self::setFields($objet, $d);
  }
  */
/**
* => @see Propel toArray() method instead !

public static function intoArray(BaseObject $Obj)
{
$ret = array();
	$className = get_class($Obj) . 'Peer';
    $msg .= ' [pour classe ' . $className . '] ';
    $classPeer = new $className;
    $methods = $classPeer->getFieldNames(BasePeer::TYPE_PHPNAME); // to check field exists !
	foreach ($methods AS $m)
	{
	$method = 'get' . $m;
		$ret[$m] = $Obj->$method();
	}
	return $ret;
}
*/
  /**
 * ! only for mysql db : other dbs do not implement enum type !
 * @param string $class
 * @param string $prop : mySql type (TYPE_FIELDNAME)
 */
  public static function getEnumValues($class, $prop, PropelPDO $con = null)
  {
    $tmp = false;
    if (null === $con) { $tmp = true; $con = Propel::getConnection(); }
    $enumValues = array();
    
    if (false === strpos($class, 'Peer') ) {
        $peerClass = $class . 'Peer';
    } else {
        $peerClass = $class;
    }
    $obj = new $peerClass;
    $table = $obj->getTableMap()->getName();
    // $field = $obj->translateFieldName($prop, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
    $field = $prop;
    // print 'translated into : ' . $table . ' / ' . $field;
    $stmt = $con->prepare("SHOW COLUMNS FROM `" . $table . "` WHERE Field = '" . $field . "'");
    $stmt->execute();
    $r = $stmt->fetchAll();
    if (!sizeof($r) ) { throw new Exception($table . '.' . $field . ' not found'); }
    preg_match_all('/\'(.*?)\'/', $r[0]['Type'], $found);
    if(!sizeof($found[1]) ) { throw new Exception('no enum values found for field ' . $field); }
    $enumValues = $found[1];

    if ($tmp) { $con = null; }
    return $enumValues;
  }
  	public static function validEnum($class, $prop, $value) {
		$trueVals = self::getEnumValues ( $class, $prop );
		if (! in_array ( $value, $trueVals )) {
			throw new Exception ( 'Invalid enum value : ' . $value . ' [valid : ' . implode ( ',', $trueVals ) );
		}
		return $value;
	}
}