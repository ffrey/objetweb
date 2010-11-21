<?php
require_once 'PHPUnit/Framework.php';
require_once 'helper/owConsoleHelper.php';

/*** extra checks ***/
/**
 * Propel : test if a propel obj has been deleted 
 * @todo : rename into hasPropelObjBeenDeleted()
 */
function hasBeenDeleted($o) {
  $ret = false;
  do {
    if (!is_object($o)) { $ret = true; break; }
    if ($o->isDeleted()) { $ret = true; break; }
    $class = get_class($o);
    $peerClass  = ucfirst($class) . 'Peer';
    $O_peer = new $peerClass;
    $exist = $O_peer->retrieveByPk($o->getId());
    if (!$exist) { $ret = true; break; }
  } while (false);
  return $ret;
}

/**
 * Doctrine
 */
 function hasDoctrineObjBeenDeleted(Doctrine_Record $o, $id_col = 'id')
 {
      $ret = false;
  do {
    if (!is_object($o)) { $ret = true; break; }
    if (!$o->exists()) { $ret = true; break; }
    $class = get_class($o);
    $O_peer = Doctrine::getTable($class);
    if (property_exists(get_class($o), $id_col) ) {
        throw new Exception ( sprintf ('class %s has no %s prop !', get_class($o), $id_col) );
    }
    $exist = $O_peer->find($o[$id_col]);
    if (!$exist) { $ret = true; break; }
  } while (false);
  return $ret;
 }

function assertContentExistsInOneFile(array $files)
{
  $ret = false;
  do {
    foreach ($files AS $file) {
      $fileContent = file_get_contents($file);
      if (strchr($fileContent, $content) ) { $ret = true; break; }
    }
  } while (false);
  return $ret;
}

/*** data storage for Propel testing ***/
/**
* usefull to keep Propel objects over several test methods ! 
* 
* @uses function hasBeenDeleted(), Propel ORM
*/
class OW {
    static public $doReset = true; // must we tearDown + setup before next test ?

	static $data = array('last'=>array() ); // Propel objects to be deleted on tear down (those inside 'last' are deleted after the others)
	static $perm = array();                 // already existed before test : do not delete
	
	static public function deleteData()
	{
		$ret = "*** start tear-down*** \r\n";
		foreach (self::$data AS $k => $objet) {
			if ('last' === $k) continue;
			if ( !hasBeenDeleted($objet) ) {
				$ret .= "  " . get_class($objet) . " (" . $k . ") is deleted on tearDown\n\r"; 
                // print 'DB ' . $k . "\n\r";
                $objet->delete(); 
                unset(self::$data[$k]);
			}
		}
		foreach (self::$data['last'] AS $k => $objet) {
			if ( !hasBeenDeleted($objet) ) {
				$ret .= "  " . get_class($objet) .  " (" . $k . ") is deleted on tearDown\n\r"; 
                $objet->delete(); 
                unset(self::$data['last'][$k]);
			}
		}
		$ret .= "*** tear-down done ***\n\r";
		return $ret;
	}
}

/*** formatting outside console : deprecated ? ***/
function br()
{
  print '<br /><hr />';
}
function h4($s)
{
  print '<h4>' . $s . '</h4>';
}
function h($s)
{
  print '<strong>' . $s . '</strong><br />';
}
if (!function_exists('t'))
{
  function t($v)
  {
    if (is_array($v)) { print '<pre>';print_r($v);print '</pre>'; return true; }
    print $v;
  };
}