<?php
/**
 * generates a sql query, suitable for consumption by Pdo::query(), from an array (ex. : $_POST)
 * @version 2.2 / 2009 04 24
 * - add getCrits()
 *
 * if needed, natural (only type allowed) joins are generated
 * @usage : @see mySqlBuilderTest.php
 *
 * @requisites :
 * * all fields start with #[-]<main_table>...
 * * foreign keys are after pattern <parent>_<id_field>
 * * all primary keys are after pattern <id_field>
 *
 * @enhance :
 * * make more params optional
 *   => default op, id_field
 *
 * * allow for choosing type of join !!! Now : only natural join allowed !
 *
 * @throws Exception
 */
class mySqlBuilder {
	const NO_SEARCH_VALUE = '###';

	protected $allowedOptions = array('no_empty_value');
	protected $acceptedOps    = array(
		'gt' => '>', 'lt' => '<',
		'ge' => '>=', 'le' => '<=',
		'like' => 'LIKE', 'ilike' => 'ILIKE', 
		'in' => 'IN', 'eq' => '='
		);
		protected $defaultOp       = 'eq';

		protected $select = 'SELECT';
		protected $mainTable = ''  ; // used if all crits are empty
		protected $froms  = array();
		protected $joins  = array(); //
        /**
        * valid entries of $rawData passed to constructor
        * @see $this->crits
        */
        protected $rawCrits = array();
        /** @see getShowableCrits() */
        protected $showableCrits = array();
        /**
        * crits with operator & value (!) in sql format (as used in sql select query) : 
        * @example : array("#fiche#annonce##type_annonce" => "ench") becomes array("annonce.type_annonce='ench'")
        */
		protected    $crits  = array(); 

		public function __construct(array $rawData, $wantedFields = '*')
		{
			$this->select .= ' ' . $wantedFields . ' ';
			$this->filter($rawData);

		}
		public function getSql()
		{
			$ret = '';
			$this->checkFilteredData();
			$ret = $this->addSelect($ret);

			$ret = $this->addFrom($ret);

			$ret = $this->addJoins($ret);

			$ret = $this->addCrits($ret);

			return $ret;
		}
        /**
        * ! conveniency method for debugging purpose only ! 
        * @return array : query format
        * @example : [0] => annonce.type_annonce='ench'
                           [1] => fiche.description_fiche LIKE '%hello%'
                           ...
         */
        protected function getCrits()
        {
            return $this->crits;
        }
        /**
        * allows to store the relevant search data (eg. between requests, etc.)
        * @return array : same as $rawData (@see __construct() ) , most often data direct from $_POST, BUT filtered : only valid are kept
        * @example : [#fiche#annonce##type_annonce##eq] => ench
                          [#fiche##description_fiche##like]       => hello
        */
        public function getRawCrits()
        {
        return $this->rawCrits;
        }
                /**
                * convenient array format for screen output (easily translatable ?)
        * @return array : 
        * @example : [0] => array('fieldName' => annonce.type_annonce', 'op' => 'eq', 'value' => 'ench')
                           [1]  => ...
        */
        public function getShowableCrits()
        {
            return $this->showableCrits;
        }
		public function countCrits()
		{
			return count($this->crits);
		}
		/*** PROTECTED ***/
		/*** *** filter the values ***/
		protected function filter(array $fAndVals)
		{
			$select = ''; $froms = array(); $joins = array(); $crits = array();
			foreach ($fAndVals AS $fieldAndOp => $val) {
				list($fieldAndOp, $val) = $this->manageOptionNoEmptyValue($fieldAndOp, $val);
				if (!$this->isValidField($fieldAndOp) ) { continue; }
				list($classes, $classAndField, $op) = $this->extract($fieldAndOp);
				$ascendingClasses = $this->formatClasses($classes); // + set main table !

				if (!$this->isSearchValue($val) ) { continue; } // we cannot discard a value until a main table is set...

				$this->makeFromsAndJoins($ascendingClasses);
                $this->rawCrits[$fieldAndOp] = $val;
                $this->showableCrits[] = array(
                'op' => $op, 'value' => $val, 
                'fieldName' => strtr($classAndField, array('##' => '.') )
                );
				$this->makeCrits($classAndField, $op, $val);
			}
			return array($select, $froms, $joins, $crits);
		}/* */
		/**
		 * breaks a field name into its components : class hierarchy, field on which to select, value to use to select
		 * @param string $field
		 * @return array : for list
		 */
		protected function extract($field)
		{
			$classes = ''; $classAndField = ''; $op = '';

			$classes = substr($field, 0, strpos($field, '##') );

			$fieldAndOp = substr($field, strpos($field, '##')+2, strlen($field)+1);
			$parts = explode('##', $fieldAndOp);
			if (2 < count($parts) ) { throw new Exception('invalid field and operator : ' . $fieldAndOp . '[1]'); }
			if (1 == count($parts) AND false !== strpos($parts[0], '#') ) {
				throw new Exception('invalid field and operator : ' . $fieldAndOp . '[2]');
			}
			$class  = substr(strrchr($classes, '#'), 1); // get last class
			if (0 === strpos($class, '-') ) { $class= substr($class, 1); }
			$classAndField = $class . '##' . $parts[0];

			$op = $this->validOp($parts);

			return array($classes, $classAndField, $op);
		}
		protected function formatClasses($classesStr)
		{
			$cs = explode('#', substr($classesStr, 1) ); // trim starting '#'
			if (0 === strpos($cs[0], '-') ) { // we are dealing with children of main table
				$cs[0] = substr($cs[0], 1); // trim starting '-'
				$this->setMainTable($cs[0]);
			} else {
				$this->setMainTable($cs[0]);
				$cs = array_reverse($cs); // $cs : always from child up to parent
			}
			return $cs;
		}
		protected function setMainTAble($mainClass)
		{
			// print __FUNCTION__ . ' : ' . $mainClass . "\r\n";
			if (0 === strpos($mainClass, '#') ) {
				throw new Exception(__FUNCTION__ . ' : invalid param : ' . $classesStr);
			}
			if (!empty($this->mainTable) ) {
				if ($this->mainTable !== $mainClass) {
					throw new Exception('anomaly : all fields must start with same table (' . $mainClass . ' found instead of ' . $this->mainTable);
				}
				return;
			}
			$this->mainTable = $mainClass;
		}
		/**
		 * @param $cs array : list of linked classes from child to parent
		 * @return void
		 */
		protected function makeFromsAndJoins(array $cs)
		{
			// var_dump($cs); exit;
			$parent = ''; // <= used within foreach !
			foreach ($cs AS $class) {
				$parent_id = (empty($parent) )? 'id' : $parent . '_id';
				if (!isset($this->joins[$class]) ) { // $class already set
					$this->joins[$class] = array();
				}
				if ('id' === $parent_id) {
					if (!count($this->joins[$class]) ) { // only add 'id' if no parent id set yet...
						$this->joins[$class][] = $parent_id;
					}
				} else {
					if (isset($this->joins[$class][0]) AND 'id' === $this->joins[$class][0]) {
						$this->joins[$class] = array(); // discard 'id' (if exist !) if FK is being added
					}
					if (!in_array($parent_id, $this->joins[$class]) ) { // no need to add same FK several times...
						$this->joins[$class][] = $parent_id;
					}
				}
				$this->froms[$class] = $class;
				$parent = $class;
			}
		}
		protected function makeCrits($classAndField, $op, $val)
		{
			$classAndField = preg_replace('/##/', '.', $classAndField);
			$this->crits[] = $classAndField . $this->formatOp($op, $val);
		}
		protected function validOp(array $a)
		{
			$op = (@isset($a[1]) )? $a[1] : $this->defaultOp;
			// print 'TRANSLATING op : ' . $op;
			if (!key_exists($op, $this->acceptedOps) ) { throw new Exception('invalid operator : ' . $op); }
			return $this->acceptedOps[$op];
		}
		/**
		 * if $f does not accept selects on empty value
		 * AND value is empty
		 * => replace '' with self::NO_EMPTY_VALUE
		 */
		protected function manageOptionNoEmptyValue ($f, $val)
		{
			$parts = explode('###', $f); // separate the option from the rest of field
			do {
				if (!empty($val) )     { break; } // no need to check !
				if (1 === count($parts) ) { break; } // no option set
				if (2 < count($parts) )   { throw new Exception('only one option is allowed'); }
				if (!$this->isValidOption($parts[1]) ) {
					throw new Exception('invalide option ' . $parts[1]);
				}
				if ($parts[1] === 'no_empty_value') {
					$val = self::NO_SEARCH_VALUE;
				}
			} while (false);
			return array($parts[0], $val);
		}
		protected function isValidOption($opt)
		{
			return (in_array($opt, $this->allowedOptions) );
		}
		/**
		 * ! all simple values get quoted (both numbers and strings) ;: seems not to hamper selects on numbers !
		 * (@see below)
		 */
		protected function formatOp($op, $val)
		{
			do {
				if ( 'IN' === $op ) {
					if (is_array($val) ) {
						$val = implode(', ', $val);
					}
					$ret = ' ' . $op . ' (' . $val . ') ';
					break;
				}
				$val = trim($val);
				if (in_array($op, array('LIKE', 'ILIKE') ) ) {
					$ret = ' ' . $op . ' \'%' . $val . '%\'';
					break;
				}
				// if (false !== strpos($val, ' ') ) { // ! spaces !
				// $isString = $val + 1;
				// if ($isNumber === 1 ) {
				$val = "'" . $val . "'";
				// }
				$ret = $op . $val;
			} while (false);
			return $ret;
		}
		/**
		 * @param $field
		 * * minimum pattern : #<table>##<field>
		 * * maxi     "      : [#<parent>...]#<table>##<field>[##<operator of comparison>]
		 */
		protected function isValidField($field)
		{
			$ret = false;
			do {
				if (false !== strpos($field, '####')){ break; } // maximum string of '#' : 3
				if (0 !== strpos($field, '#') )      { break; } // no leading '#'
				if ('#' == $field[1])                { break; } // leading '#' followed by another !
				if (false === strpos($field, '##') ) { break; } // no '##'
				if (2 < preg_match('/[^#]{1}##[^#]{1}/', $field) ) {
					print 'invalid form : ' . $field . "\n\r"; exit;
					break; } // more than 2 '##'
					$ret = true;
			} while (false);
			return $ret;
		}
		protected function isSearchValue($val)
		{
			return (self::NO_SEARCH_VALUE !== $val);
		}

		/*** *** build the query from filtered values ***/
		protected function addSelect($sqlStr)
		{
			$sqlStr .= $this->select;
			return $sqlStr . "\n\r";
		}
		protected function addFrom($sqlStr)
		{
			$sqlStr .= ' FROM ';
			if (count($this->froms) ) {
				$sqlStr .= implode(', ', $this->froms);
			} else {
				$sqlStr .= $this->mainTable;
			}
			return $sqlStr . "\n\r";
		}
		protected function addJoins($sqlStr)
		{
			if (!$this->hasJoins() ) {
				return $sqlStr;
			}
			// var_dump($this->joins);
			$sqlStr .= ' WHERE ';
			$tmp_inner = ''; $tmp_outer = array();
			foreach ($this->joins AS $class => $parent_fields) {
				foreach ($parent_fields AS $parent_field) {
					if ('id' === $parent_field) { continue; }
					$tmp_inner .= $class . '.' . $parent_field . '=';
					$tmp_inner .= $this->extractParent($parent_field, $class) . '.id';
					if (next($parent_fields) ) { $tmp_inner .= ' AND '; }
				}
				if (empty($tmp_inner) ) { continue; } // @see above : case 'id' == $parent_field...
				$tmp_outer[] = $tmp_inner;
				$tmp_inner   = '';
			}
			// print "we get "; print_r($tmp_outer);
			$tmp = implode(' AND ', $tmp_outer);
			// print "we add $tmp \r\n";
			$sqlStr .= $tmp;
			return $sqlStr . "\n\r";
		}
		/**
		 * @param $class              : <table>
		 * @param $parent_field strin : <field>
		 * @return string :
		 * * if field == FK
		 *      return name of parent
		 *   if field == 'id'
		 *      return name of table
		 */
		protected function extractParent($parent_field, $class)
		{
			if ('id' === $parent_field) {
				if (!in_array($parent_field, $this->joins) ) {
					throw new Exception('no top parent defined !');
				}
				/*$tmp = array_flip($this->joins);
				 return $tmp[$parent_field];*/
				return $class;
			}
			if (false === strpos($parent_field, '_') ) {
				throw new Exception('invalid parent field name : ' . $parent_field);
			}
			return $parent = substr($parent_field, 0, strrpos($parent_field, '_') );
		}
		protected function hasJoins()
		{
			return (1 < count($this->joins) ); // you need more than 1 table to make a join !-)
		}
		protected function addCrits($str)
		{
			if (!count($this->crits) ) { return $str; }
			$str .= (!$this->hasJoins() )? ' WHERE ' : ' AND ';
			$str .= implode(' AND ', $this->crits);
			return $str;
		}

		/*** *** checks ***/
		protected function checkFilteredData()
		{
			// at least one from table !
			if (!count($this->froms) AND empty($this->mainTable) ) {
				throw new Exception('no table found !');
			}
			if (1 < $nb = $this->countTopTables() ) {
				throw new Exception('only one root table is allowed : got ' . $nb);
			}
		}
		protected function countTopTables () {
			$nb_of_top_tables = 0;
			foreach ($this->joins AS $table => $foreign_field) {
				if ('id' !== $foreign_field) continue;
				$nb_of_top_tables++;
			}
			return $nb_of_top_tables;
		}
}