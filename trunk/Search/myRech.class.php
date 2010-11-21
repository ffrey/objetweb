<?php
/**
 * automates search with Propel
 *
 * @uses Propel !
 *
 * ! 25 06 08 : refactor => 3 search modes !
 * - main
 * - TODO : by method (relies on tP::getByGetterMethod()) ! <= TODO : see below !
 * - by parentField (relies on tP::getByParentField())
 * @todo document & comment 2 last modes !
 *
 * *** main ***
 * Required in search form :
 *  - field names : @@<underscored tableFieldName>[@@<tableNameAlias : default = t1]
 *  - at least one '@@t1' => Class (!ucfirst !) to perform search upon
 * @todo : minimum table should be 't1' OR 'tP'
 *  - if search performed on two related tables, '@@tP' => parent Class (!ucfirst !) !
 *  - if a field belongs to parent table => append '@@tP' !
 * Particulars :
 *  - multiple search on same column : fields must be named @@<fieldName>#<name1>, and inserted as usual into array $signes => search will be performed upon <fieldName>
 * Defaults :
 *  - fields without '@@tP' are supposed belonging to t1
 *  - search operator is '=' (see constructor args to change it) : '>' '<' '>=' '<=' 'IN' 'LIKE' 'ILIKE''='
 *
 * @todo BUG ret_format <= return of $this->search uselessly 'heavy'
 * now : array(0 => array('<tP>' => tP object, '<t1>' => t1 object), etc.
 * =>  : array(0 => tP object, etc. ! (child objects retrievable through Propel getters !)
 *
 * @todo
 *  - handle 'OR' !
 *  - + handle 'OR' with 'AND' !!!
 * ... example : (t1 LIKE '%hello' OR t2 LIKE '%hello') AND t3 = 120
 *  - ? '###' == '' ? useful ? => check how these special values are handled
 * example : 'indifferent' / 'all'...
 *  - ? define operator within field name inside of html form
 * example : [<operatorAlias : default = '=']@@<tableFieldName>...
 *
 * *** by method ***
 * Required in search form :
 *  - field names : 'MM_'<object class>&<method> ! CAUTION : & instead of '.' <= due to '.' transformed into '_' by http !?
 *
 * *** by parent field ***
 * @todo : finish ? => a getByGetter() method has to be built on each orm class used ???
 * Required in search form :
 *  - field names : '##<object class>&<object class>&...&<underscored tableFieldName> ! CAUTION : idem above !
 *
 * @todo extend this class to make mysql specific class : fulltext search ! => see recherche.php de old asso95 & bdd.php
 *
 * @package myRech
 * @uses myUtil (for logging)
 */
class myRech {
  const SEP    = '@@'; // marks searchable fields : compulsory at beginning of field name !
  const MS_SEP = '#';  // marks fields with multiple conditions applied

  const PARENTSEARCH_SEP = '##'; // fields searchable with self::searchByParentFieds()
  const METHODSEARCH_SEP = 'MM_'; //  "         "         "       searchByMethods()

  const NULL_VALUE = '###';
  // internal vars for main search
  protected $tables    = array();
  protected $tP        = '';      // table parent
  protected $tChildren = array(); // tables children
  protected $defaultOp = 'AND';

  // internal vars for search by parent
  protected $byParentSearchTables = array();

  // internal vars for search by method
  protected $byMethodSearchTables = array(); // byMethodSearchTables

  // gettable vars
  protected $nbCrit    = 0;
  protected $critsArray = array();
  protected $critsLitt = '';
  protected $results   = array();

  protected $log = '<hr />LOG : <br />'; // msgs + errors in outlined

  /*** API ***/
  /**
	 * constructor
	 *
	 * sifts data to keep only valid search fields
	 *
	 * @param array $signes to specify operator used for given field array('Class' => array('<field>' => '<op>', ...), ...)
	 * @example $allSignes = array(
			'Ficheauto' => array('kms' => '<', 'description_fiche' => 'LIKE'), // MAIN search type
			...
			'Fiche.getPhotos' => '>', // ? by method => TODO
			'Fiche.Annonce.type_annonce' => '<', // by parent field
			...
			);
	 * @see addToCriteria() for list of allowed ops
	 * @return myRech
	 */
  public function __construct( array $data, array $signes = array() ){
    $db = 0;
    $this->log('<strong>' . __FUNCTION__ . '</strong>');
    if (!is_array($data)) throw new Exception('erreur $data');
    if (!key_exists(self::SEP . 't1', $data) AND !key_exists(self::SEP . 'tP', $data) )	{
      throw new Exception('at least table ' . self::SEP . 't1 needed !');
    }
    list($mainSearchTables, $byParentSearchTables, $byMethodSearchTables) = $this->makeCrits($data, $signes);
    $this->tables = $mainSearchTables;
    $this->byParentSearchTables = $byParentSearchTables;
    $this->byMethodSearchTables = $byMethodSearchTables;
  }// /constructor

  /**
	 * @return array
	 * [0]
	 *   [Class t1] : TODO : nom de class (Class CamelCased) ou nom de table (table underscored) ?
	 *   [Class t2]
	 *   ...
	 * [1]
	 *   [Class t1]
	 *   ...
	 */
  public function search() {
    // BUG : 28 01 08 : search on 'annee' => always returns all records !!! ???
    $db = 0;		$this->log('<strong>' . __FUNCTION__ . '</strong>');
    $ret = array();
    do {
      $tmpResults = array();
      if ( 0 === sizeof($this->tables) ) { break; }
      $tmpResults = $this->getSearchOnEachTable($this->tables);
      if ( 'AND' === $this->defaultOp AND $this->isOneSearchEmpty($tmpResults, $this->tables) )
      { break; }// 'AND' search => one search on a table empty => no result at all !
      if ( $this->hasParent() ) {
        $ret = $this->mergeTablesWithParent($tmpResults); break;
      }
      $ret = $this->mergePeerTables($tmpResults);
    }while (false);
    $this->results = $this->_reformat($ret); // REFACTORED : for bug 2/27/06
    return $ret;
  } // /search()
  public function searchAndFormat() {
    $new = array();
    $ret = $this->search();
    foreach ($ret AS $value) {
      if (!is_array($value)) { continue; }
      foreach ($value AS $o) { if (is_object($o)) $new[] = $o; }
    }
    return $new;
  }
  public function searchByParentFields()
  {
    $ret = $this->results; $msg_log = __FUNCTION__ . ' : ';
    do {
      if (!sizeof($ret)) { $msg_log .= 'no result from main search => STOP'; break; }
      else { $msg_log .= 'we get ' . sizeof($ret) . ' results to sift.'; }
      $class = $this->tP; $msg_log .= 'we are looking for ' . $class . ' objects / ';
      $A_objects = $ret; // REFACTORED : for bug 2/27/06
      /*
      $A_objects = array();
      foreach ($ret AS $ligne) { // REFACTOR : unconvenient ret format
      if (!@isset($ligne[$class])) { continue; }
      $A_objects[] = $ligne[$class];
      }
      */
      if (!sizeof($A_objects)) { $msg_log .= 'no objects of type' . $class . ' ! => STOP'; break; }
      $fields = array_keys($this->byParentSearchTables['crits']);
      // $fields  = $this->byParentSearchTables['crits'];
      $msg_log .= 'fields : ' . implode(', ', $fields) . ' / ';
      // $field = array('Annonce.type_annonce', 'Annonce.Pack_Vendeur.Vendeur.type_vendeur');
      $vals    = array_values($this->byParentSearchTables['crits']);
      // $vals = $this->byParentSearchTables['signes'];
      $msg_log .= 'fields : ' . implode(', ', $vals) . ' / ';
      // $val   = array('ench', 'pro');
      $peerObject = $class . 'Peer';
      $o = new $peerObject;
      $ret = $o->getByParentField($fields, $vals, $A_objects);
      // $ret = $peerObject($fields, $vals, $A_objects);
      // $ret = $this->formatResults($tmp_ret); // REFACTORED : for bug 2/27/06
      $msg_log .= ' > we get ' . sizeof($ret) . ' results !';
    } while (false);
    owSf::sf_log($msg_log);
    $this->results = $ret;
    return $ret;
  }

  public function searchByMethods()
  {
    $receivedResults = $this->results;
    $msg_log = __FUNCTION__ . ' : '; $ret = array();
    try {
      $tmp_ret = array();
      $class = $this->tP; $msg_log .= 'we are looking for ' . $class . ' objects / ';
      $A_objects = $receivedResults; // REFACTORED : for bug 2/27/06
      // list($class, $A_objects) = $this->_reformat($receivedResults);
      if (!sizeof($A_objects)) { throw new Exception($msg_log .= 'no result from main search => STOP'); }
      $methods = $this->byMethodSearchTables;
      if (!sizeof($methods)) { throw new Exception($msg_log .= 'no method search to perform => STOP'); }
      /* $method ='countPhotos';  $val   = 1;  $signe = '>'; */
      $peerObject = $class . 'Peer';
      $o = new $peerObject;
      $crits = $this->byMethodSearchTables; // TODO !
      $methods = array_keys($crits['crits']);
      $vals    = array_values($crits['crits']);
      $signes  = array_values($crits['signes']);
      //  => check format from $this->extractForSearchByMethod($data); + makeCrits()
      // ? should be array[0]=> array('method'=>..., 'signe'=>..., 'value'=>...)) ?
      $tmp_ret = $A_objects;
      foreach ($methods AS $method => $val) {
        // ? $tmp_ret changed on each loop ?
        $tmp_ret = $o->getByGetterMethod($method, $signes[$method], $val, $tmp_ret);
      }
      // $ret = $this->formatResults($tmp_ret); // REFACTORED : for bug 2/27/06
      $ret = $tmp_ret;
    } catch (Exception $e) {
      $msg_log .= $e->getMessage();
    }
    owSf::sf_log($msg_log);
    $this->results = $ret;
    return $ret;
    // TODO !
    /* use Fiche::getByGetterMethod($method, $signe, $val, $A_fiches)
    see module essais : action annonces/propel4
    */
  }
  // getters
  public function getNbCrit() {
    return $this->nbCrit;
  }

  public function getCritsArray()
  {
    return $this->critsArray;
  }
  // askers
  public function hasResultOverTwoTables()
  {
    $ret = false;
    if ($this->tP) $ret = true;
    return $ret;
  }

  public function isSearchWithAParent()
  {
    return $this->hasParent();
  }

  /*** PROTECTED ***/
  /**
   * reformat results for searchByMethods() & searchByParentFields
   *
   * @return array : [0] => class being searched for
   *                 [1] => array of objects of type class found
   */
  protected function _reformat(array $results)
  {
    $class = $this->tP;    $A_objects = array();
    do {
      if ( !sizeof($results) ) { throw new Exception(__FUNCTION__ . ' : no results received'); break; }
      foreach ($results AS $ligne) { // REFACTOR : unconvenient ret format
        if (!@isset($ligne[$class])) { continue; }
        $A_objects[] = $ligne[$class];
      }
    }while (false);
    return array($class, $A_objects);
  }
  /*
  protected function formatResults(array $rawTab) // BUG ret_format
  {
  $formatted = array();
  $parentObject = ucfirst($this->tP);
  for ($i = 0; $i < sizeof($rawTab); $i++) {
  $formatted[$i][$parentObject] = $rawTab[$i];
  }
  return $formatted;
  }
  */

  /**
	 * validates data
	 *
	 * @return array
	 * [0]
	 *  ['tP']
	 *  --- ['<fieldName>']       => mixed <value>
	 *  --- ['<fieldName>_signe'] => <op>
	 *  --- ...
	 *  ['t1']
	 *  ...
	 * [1] // for search by parent fields
	 *   [<cheminToField>]   => string <value>
	 *   [<cheminToField>_signe'] => <op>
	 *   ...
	 */
  protected function makeCrits(array $data, array $signes = array()) {
    // TODO : unknown fields should be discarded here => see $this->search()
    /*** main search ***/
    $this->log('<strong>' . __FUNCTION__ . ' </strong>: ');
    $A_critsWithOps = array(); // return
    db($data);
    list($tables, $postData) = $this->extractRelevantData($data);
    $this->critsArray = $postData;
    $this->log('--- tables dispo : ' . print_r($tables, true) . ' / data : ' . print_r($postData, true));

    $t = $this->assignFieldsToTables($postData, $tables);
    $A_critsWithOps = $this->setOpsUponCrits($t, $tables, $signes);

    /*** search by parent fields ***/
    $t = $this->extractForSearchByParent($data);
    $A_critsForSearchByParent = $this->setOps($t, $signes);

    /*** search by methods ***/
    $t = $this->extractForSearchByMethod($data);
    $A_critsForSearchByMethod = $this->setOps($t, $signes);

    return array($A_critsWithOps, $A_critsForSearchByParent, $A_critsForSearchByMethod);
  } // /makeCrits()

  /*** called from $this->search() ***/
  protected function getSearchOnEachTable(array $crits)
  {
    $this->log_function(__FUNCTION__);
    $tmpResults = array();
    foreach ($crits as $table => $values) {
      if ( !sizeof($values) ) { $this->results[$table] = null; continue; } // no search if no crit
      $c = new Criteria();
      $className = ucfirst($table) . 'Peer'; // TODO : use objects defined in $this->makeCrits() !
      // currently : same Peer objects created twice...
      $objet = new $className;
      $this->log('--- rech sur ' . $table . ' avec objet de classe ' . $className);
      foreach ($values as $field => $value) {
        if ( strstr($field, '_signe') ) continue; // search operator on $field : we skip
        $op = '';
        if ( isset($values[$field . '_signe']) ) {
          $op = $values[$field . '_signe'];
        } else {
          $this->log_error('--- --- no operator on ' . $field); continue;
        }
        $field = $this->extractFieldForMultipleSearch($field);
        $this->addToCriteria($c, $table, $field, $value, $op);
      }
      $this->log('--- --- Query ' . $c->toString());
      $tmp_res = $objet->doSelect($c);
      if (0 !== sizeof($tmp_res) ) $tmpResults[$table] = $tmp_res; // no cell created for
      //... table without results !
    } // /foreach
    return $tmpResults;
  } // /getSearchOnEachTable()
  /*** *** from getSearchOnEachTable ***/
  protected function addToCriteria(Criteria $c, $table, $field, $value, $op = '=') {
    // $this->log_function(__FUNCTION__, 'protected');
    $completeFieldName = strtolower($table) . '.' . $field;
    switch (trim($op)) {
      case '>':
      $propelOp =  Criteria::GREATER_THAN;
      break;
      case '<':
      $propelOp =  Criteria::LESS_THAN;
      break;
      case '>=':
      $propelOp =  Criteria::GREATER_EQUAL;
      break;
      case '<=':
      $propelOp =  Criteria::LESS_EQUAL;
      break;
      case 'IN':
      $propelOp =  Criteria::IN;
      break;
      case 'LIKE':
      $value = '%'.$value.'%'; $propelOp = Criteria::LIKE;
      break;
      case 'ILIKE':
      $value = '%'.$value.'%'; $propelOp =  Criteria::ILIKE;
      break;
      case '=':
      $propelOp = Criteria::EQUAL;
      break;
      default:
      $this->log_error('--- no op for value ' . $value);
      $propelOp = Criteria::EQUAL;
    }
    $this->log('--- --- new criteria (' . $completeFieldName . ' ' . $op . '[' . $propelOp . '] ' . $value . ')');
    $c->addAnd($completeFieldName, $value, $propelOp);
    // $this->log('---' . $c->toString());
  } // /addToCriteria()
  /*** ***/
  protected function isOneSearchEmpty(array $r, array $crits) {
    $ret = false;
    do {
      if (!sizeof($r)) { $ret = true; $this->log_error('Aucun résultat !'); break; }
      foreach ($crits as $table => $v) {
        if (!@isset($r[$table])) {
          $this->log_error('search on ' . $table . ' is empty !');
          $ret = true; // there is one table with crits without results => true
        }
      }
    } while (false);
    return $ret;
  } // /isOneSearchEmpty()
  protected function mergePeerTables(array $res) {
    $db = 0;$this->log_function(__FUNCTION__ . ' : ');
    $mergedR = array();
    $ligne = 0;
    foreach ($res as $table => $objets) {
      foreach ($objets as $O) {
        $mergedR[$ligne][$table] = $O;
        $ligne++;
      }
    }
    return $mergedR;
  }
  protected function mergeTablesWithParent(array $res) {
    $this->log_function(__FUNCTION__, 'protected');

    $defaultOp = $this->defaultOp; // AND, OR
    $mergedR = array(); // @return

    $nbOfChildren = sizeof($this->tChildren);
    $nbOfResTables = sizeof($res);
    $this->log('Resultats non merged : ' . $nbOfResTables . ' tables');
    if (0 === $nbOfResTables ) {
      $this->log('--- no results at all !');
      $mergedR = $res;
    } elseif (1 === $nbOfResTables) {
      $this->log('--- 1 seule table de résultats => rechercher données complémentaires !');
      // <= op 'AND' => if a search retrieves nothing from one table => no results at all !
      // BUG : ? anyway : info only on one table === anomalie !!! => ne pas prendre en cpte !
      $ligne = 0;
      foreach ($res as $table => $objets) {
        foreach ($objets as $O) {
          $mergedR[$ligne][$table] = $O;

          if ($table === $this->tP) {// if parent => get child
            $this->log('--- is a parent (' . $this->tP .') sans enfant !');
            foreach ($this->tChildren as $childName) {
              $c = new Criteria();
              $parentFk = strtolower($childName).'.'.$this->tP . '_id';
              $this->log('--- chercher enfants avec ' . $parentFk . ' = ' . $O->getId());
              $c->add($parentFk, $O->getId());
              $childNamePeer = $childName.'Peer';
              $O_childPeer = new $childNamePeer ;
              $this->log('--- --- on cherche avec  ' . $childNamePeer);
              $this->log('Objet Peer : ' . print_r($O_childPeer, true));
              $children = $O_childPeer->doSelect($c);
              $this->log('--- --- => ' . sizeof($children) . ' résultats');
              foreach ($children as $child) {
                $mergedR[$ligne][$childName] = $child; // TODO : here, only one child possible
              }
              /**/
            }
          } elseif (in_array($table, $this->tChildren)) {// if child => get parent
            $this->log('--- is an enfant (' . $table . ') sans parent !');
            // var_dump($O); exit;
            // print Reflection::export(new ReflectionObject($O));exit;
            $method = 'get' . $this->tP . 'Id';
            $parentId = $O->$method();
            // $parentId = $O->getFicheId();
            $parent = FichePeer::retrieveByPK($parentId); // TODO : use getter getFiche()
            $mergedR[$ligne][$this->tP] = $parent;
          } else {
            $this->log_error($table . ' is neither parent nor child !');
          }
          $ligne++;
        }
      }
    } else {
      $this->log('--- parent : ' . $this->tP);
      switch ($defaultOp) {
        case 'AND':
        default: // TODO : OR
        $ligne = 0;
        $this->log('--- enfants ' . print_r($this->tChildren, true));
        foreach($res[$this->tP] as $O_parent){
          for ($i = 0; $i < $nbOfChildren; $i++) {
            $childName = $this->tChildren[$i];
            $this->log('--- merge ' . $this->tP . ' and ' . $childName);
            foreach($res[$childName] as $O_enfant){ // BUG ?
              if($O_enfant->getFicheId() == $O_parent->getId()){
                $mergedR[$ligne][$this->tP] = $O_parent;
                $mergedR[$ligne][$childName] = $O_enfant;
                $ligne++;
              }
            } // foreach 3
          } // /for
        } // /foreach 2
      } // /foreach 1
    } // /if
    return $mergedR;
  } // /mergeAccordingToDefaultOp()
  /***/

  /*** called from $this->makeCrits() ***/
  protected function extractRelevantData(array $data)
  {
    $this->log_function(__FUNCTION__);
    $tables = array(); // t1[, tP] with real bdd names
    $postData = array(); // relevant search data
    foreach ($data as $key => $val) { // take away unrelevant data
      if ($this->isNotRelevant($key)) {
        $this->log_error('---' . $key . ' is not in form');
        continue; // not a form value
      }
      $i = explode(self::SEP , $key);
      if (1 < sizeof($i)) { // only main search concerned !
        if ( preg_match('#^t[0-9P]{1,2}$#', $i[1]) ) { // extract table names from @@t1@@ & @@tP@@
          $this->log('--- new table : ' . $val);
          $tables[$i[1]] = $val; // $tables['t1'] => <nom table BDD>
          continue;
        };
      }
      $postData[$key] = $val; // $postData['@@rubrique_id@@tP'] = <value>
    }
    $this->log('--- tables : ' . print_r($tables, true));
    if ( 1 == sizeof($tables) AND key_exists('tP', $tables) ) {
      $this->log('--- only tP => child sans parent (tP devient t1)');
      $tables['t1'] = $tables['tP'];
      foreach ($postData AS $key => $val) {
        $newKey = str_replace(self::SEP . 'tP', self::SEP . 't1', $key, $hasReplaced);
        if (!$hasReplaced) continue;
        $this->log('--- on remplace tP par t1 dans ' . $key . ' => ' . $newKey);
        $postData[$newKey] = $val;
        unset($postData[$key]);
      }
      unset($tables['tP']); // TODO : CAUTION with vals without tables => have been assigned to 't1' by default !
    }
    $this->setParentsAndChildren($tables);

    return array($tables, $postData);
  } // /extractRelevantData()
  protected function isNotRelevant($field)
  {
    $ret = true;
    // TODO : better up the sifting :
    //... ex. : regex '/^' . self::SEP . '[^@ ]' . self::SEP...
    do {
      if ( self::SEP === substr($field, 0, 2 ) ) { $ret = false; break; }
      if ( self::METHODSEARCH_SEP === substr($field, 0, 3)) { $ret = false; break; }
      if ( self::PARENTSEARCH_SEP === substr($field, 0, 2)) { $ret = false; break; }
    } while (false);
    return $ret;
  }
  protected function extractForSearchByParent(array $data)
  {
    $tables = array();
    foreach ($data as $key => $val) { // take away unrelevant data
      if ( self::PARENTSEARCH_SEP !== substr($key, 0, 2 ) ) { continue; }
      $path = str_replace('&', '.', substr($key, 2));
      $tables[$path] = $val;
    }
    return $tables;
  }
  protected function extractForSearchByMethod(array $data)
  {
    $this->log_function(__function__); $this->log(print_r($data, true) );
    $tables = array();
    foreach ($data as $key => $val) { // take away unrelevant data
      $this->log(substr($key, 0, 3 ) . '<br />');
      if ( self::METHODSEARCH_SEP !== substr($key, 0, 3 ) ) { continue; }
      $path = str_replace('&', '.', substr($key, 3));
      $tables[$path] = $val;
    }
    $this->log(print_r($tables, true) );
    return $tables;
  }
  /**
   * @param array [0] => array('<tP>' => Object tP, '<t1>' => Object t1)
   *              ...
   * @return array [0] => Object tP
   *                ...
   *
   */
  protected function extractParentObjects(array $res)
  {
    $ret = array();
    $parentObject = ucfirst($this->tP);
    foreach ($res AS $ligne) {
      if (!@isset($ligne[$parentObject])) { continue; }
      $ret[] = $ligne[$this->tP];
    }
    return $ret;
  }
  /*** *** from extractRelevantData() ***/
  protected function setParentsAndChildren(array $tables) // void
  {
    $this->log_function(__FUNCTION__);
    $this->log('--- --- tables to set : ' . print_r($tables, true));
    foreach ($tables AS $alias => $tableName) {
      if ( 'tP' === $alias) {
        $this->tP = $tableName; // table parent
      } else {
        $this->tChildren[] = $tableName; // table children
      }
    }
  } // /setParentsAndChildren()
  protected function assignFieldsToTables(array $postData, array $tables)
  {
    $t = array();
    foreach ($postData as $key => $val) { // distributes fields data to relevant
      //... tables + takes away empty fields
      $i = explode(self::SEP , $key);
      if (1 >= sizeof($i)) { continue; } // keep only crits for main search
      if ($this->isNull($val)) { $this->log('--- ' . $i[1] . ' is null !'); continue; }
      $tableIndice = sizeof($i) - 1;
      if ( @isset($i[2]) ) {
        if ( !key_exists($i[2], $tables) ) {
          $this->log_error('--- : table ' . $i[2] . ' for field ' . $i[1] . ' does not exist ! ');
          continue;
        }
        $t[$i[2]][$i[1]] = $val;
        $this->log('--- : crit ' . $i[1] . '(' . $val .') belongs to ' . $i[2] . '(' . $tables[$i[2]] . ') ');
        continue;
      }
      $this->log('--- : crit ' . $i[1] . ' belongs to t1 by default');
      $t['tableless'][$i[1]] = $val;
    }
    if ( @isset($t['tableless']) ) { // crits without table => t1 by default
      if (!@isset($t['t1']) ) { $t['t1'] = array(); } //
      $t['t1'] = array_merge($t['t1'], $t['tableless']);
    }
    unset($t['tableless']);
    return $t;
  } // /assignFieldsToTables()
  protected function setOpsUponCrits(array $t, array $tables, array $signes)
  {
    $A_critsWithOps = array();
    foreach ($t as $tableIndice => $val) {
      $A_critsWithOps[$tables[$tableIndice]] = array();
      $A_signesOfCurrentTable = (@isset($signes[$tables[$tableIndice]]))? $signes[$tables[$tableIndice]] : array();
      $this->log('--- : existe signes pour ' . $tables[$tableIndice] . ' ? ');
      $log_reponse = (sizeof($A_signesOfCurrentTable))? ' YES ' : ' NO ';
      $this->log($log_reponse);
      foreach ($val as $k => $v) {
        if (!$this->isValidField($k, $tables[$tableIndice])) { continue; }
        // TODO 1: keep object Peer for re-use in $this->search() !
        $A_critsWithOps[$tables[$tableIndice]][$k] = $v;
        $this->nbCrit++;
        // search ops for each field
        if ( key_exists($k, $A_signesOfCurrentTable) ) {
          $this->log(' signe de ' . $k . ' : ' . $A_signesOfCurrentTable[$k]);
          $A_critsWithOps[$tables[$tableIndice]][$k . '_signe'] = $A_signesOfCurrentTable[$k];
          continue;
        }
        $A_critsWithOps[$tables[$tableIndice]][$k . '_signe'] = '='; // op by default
        $this->log(' signe de ' . $k . ' : =');
      }
    }
    return $A_critsWithOps;
  } // /setOpsUponCrits()
  /**
	 * idem setOpsUponCrits() for searches by method/parentField
	 * @todo unify ways of managing ops !
	 *
	 * @return array : ['crits']  => array [<field>] => [<value>]
	 *                 ['signes'] => array [<field>] => [<op>] (by default : '=')
	 */
  protected function setOps(array $t, array $signes)
  {
    $newSignes = array();
    $ret = array('crits' => $t, 'signes' => $newSignes);
    foreach ($t AS $field => $value) {
      if ($this->isNull($value)) { unset($t[$field]); continue; } // skip NULL_VALUEs ! (not 'O', '', etc. !)
      if (key_exists($field, $signes)) {
        $newSignes[$field] = $signes[$field];
      } else {
        $newSignes[$field] = '=';
      }
    }
    $ret = array('crits' => $t, 'signes' => $newSignes);
    return $ret;
  }
  /**
	 * does $field exist for given $table ?
	 *
	 * @param string $field
	 * @param string $table
	 * @return bool
	 */
  protected function isValidField($field, $table) {
    $db = 0;

    $className = ucfirst($table) . 'Peer';
    $objet = new $className;
    if ($db) print $objet . '<br />';
    $tableFields = $objet->getFieldNames(); // to check field exists !
    if ($db) {print 'chps de ' . $table . ' : '; print_r($tableFields); }
    $this->log(__FUNCTION__ . ' : rech sur ' . $table);
    $field = $this->extractFieldForMultipleSearch($field);
    $phpNameField = sfInflector::camelize($field);// phpName : Camellized
    if ( !in_array($phpNameField, $tableFields) ) {
      $this->log_error(__FUNCTION__ . ' : champ ' . $field . ' inconu dans ' . $table); return false;
    }

    return true;
  } // /isValidField()

  protected function extractFieldForMultipleSearch($fieldName)
  {
    if (strstr($fieldName, self::MS_SEP )) {
      $tmp = explode(self::MS_SEP , $fieldName);
      $this->log('multiple search field : ' . $fieldName . ' => ' . $tmp[0]);
      $fieldName = $tmp[0];
    }
    return $fieldName;
  }

  protected function isNull($val) {
    if ( '' === $val ) return true;
    if ( self::NULL_VALUE === $val) return true; // value for indifférent : nom pris en cpte ! = all !
    return false;
  }

  protected function hasParent() {
    $db = 0;$this->log_function(__FUNCTION__ . ' : ');
    if ('' !== trim($this->tP)) { $this->log('--- HAS A PARENT : ' . $this->tP);return true; }
    $this->log('--- HAS NO PARENT');
    return false;
  }

  /*** log functions ***/
  public function getLog() {
    $log = '<hr />LOG : <br />\n\r' . $this->log;
    $this->log = '';
    return $log;
  }

  protected function log($msg) {
    $this->log .= $msg . '<br />\n\r';
    /*$logger = sfContext::getInstance()->getLogger();
    $logger->debug(__CLASS__ . '::' . $msg);
    */
  }
  protected  function log_function ($msg, $access = 'public')
  {
    if ('protected' == $access) $msg = '<i>' . $msg . '</i>';
    $this->log .= '<strong>' . $msg . '</strong><br />\n\r';

  }
  protected function log_error($msg) {
    $this->log .= '<font color="red">' . $msg . '</font>' . '<br />\n\r';
    /* $logger = sfContext::getInstance()->getLogger();
    $logger->info(__CLASS__ . '::' . $msg); */
  }
  /***/
} // /class resultatsRech()