<?PHP


/*** common view helpers ***/
// TODO : generalize function ! => into myArray class ?
/*
function _array_to_path(array $A, $sep = ' > ')
{
  $text = '';
  for ($i = 0; $i < sizeof($A); $i++) {
    $text .= $A[$i]->getTitre();
    if (($i + 1) !== sizeof($A)) $text .= $sep;
  }
  return $text;
}
*/
/**
 * @deprecated : too specific <= depends on Propel class !
 */
function get_chemin(Rubrique $O_currentRub, $sep = ' > ')
{
  $text = '';
  $A = $O_currentRub->getChemin();
  for ($i = 0; $i < sizeof($A); $i++) {
    $text .= $A[$i]->getTitre();
    if (($i + 1) !== sizeof($A)) $text .= $sep;
  }
  return $text;
}



/*** templating functions ***/
/**
 * echo with decoration if not null or empty
 *
 * @param :
 * * [$avt string]
 * * $var string : nothing is echoed if null OR empty OR false (not 0 !)
 * * [$apres string : can exist only if $avt is defined (even if empty) ]
 * * [$sinon string : alt string if $var is not echoable <= all params must be set]
 * 
 * @return : echoes !
 */
 function e()
 {
    $vars = func_get_args();
    switch (count($vars) {
    case 1:
    echo r($var[0] );    break;
    case 2: 
    echo r($var[0], $var[1]); break;
    case 3:
    echo r($var[0], $var[1], $var[2]);    break;
    case 4:
    echo r($var[0], $var[1], $var[2], $var[3]); break;
    }
 }
function r()
{
	$db = 0;
	$avt = ''; $var; $apres = ''; $sinon = '';
	$args = func_get_args();
	if (!sizeof($args)) { throw new Exception(__FUNCTION__ . ' : arguments insuffisants'); }
	if (sizeof($args) == 1) {
		$var = $args[0];
	} else {
		$avt = $args[0]; $var = $args[1];
		if (isset($args[2])) $apres = $args[2];
		if (isset($args[3])) $sinon = $args[3];
	}
	if ($db) print 'vars : ' . $avt . ' / ' . $var . ' / ' . $apres . ' / ' . $sinon . '<br />';
	$ret = '';
	do {
		if ('' === $var OR null === $var OR false === $var) {
			if ('' === $sinon) break;
			$ret = $sinon;
			break;
		} else {
			$ret = $avt . $var . $apres;
		}
	} while (false);
	return $ret;
}
/**
 * manage printing according to nb of elements => plural forms taken into account !
 *
 * @param int $nb
 * @param array $strings : array(<print if $nb == 0>,
 *                                <print if $nb == 1>,
 *                                <print if $nb > 1
 *                              )
 * @example array('aucun cheval trouvé', ':nb cheval trouvé', ':nb chevaux trouvés');
 *
 * @return String
 */
function r_nb($nb, array $strings)
{
  if (!is_int($nb)) throw new Exception(__FUNCTION__ . ' : nb param must be integer !');
  $ret = '';
  switch ($nb) {
    case 0:
    $ret = str_replace(':nb', $nb, $strings[0]);
    break;
    case 1:
    $ret = str_replace(':nb', $nb, $strings[1]);
    break;
    default:
    $ret = str_replace(':nb', $nb, $strings[2]);
  }
  return $ret;
}
/**
 * ! echoes !
 */
function e_nb($nb, array $strings)
{
	echo r_nb($nb, $strings);
}
// loops
/**
 * @return mixed : false is is php false ('', null, false) + empty array / @param $var else
 * @enhance : call to TP_is() : still useful ? <= was meant to handle loops, but whole managment of loops is to be refactored...
 */
function TP($var)
{
	$ret = false;
	TP_is('reset !', true);
	do {
		if (is_array($var)) {
			if (!sizeof($var) ) { break; } // array vide
		}
		if (''    === $var OR
		    false === $var OR 
		    null  === $var) 
		{ break; }
		$ret = $var;
	} while (false);
	return $ret;
}
$ongoingLoop = false;
/**
 * Enter description here...
 *
 * @param unknown_type $tab
 * @return unknown
 */
function TP_End($tab)
{
	static $n = 0;
	$n++;
	$ret = (sizeof($tab) == $n);
	if ($ret) { $n = 0; TP_Start(array(), true); } // reset !
	return $ret; // bool
}
function TP_Start($tab, $reset = false)
{
	static $n = 0;
	if ( $n >= sizeof($tab) ) { $n = 0;  } // a preceding loop was not ended by TP_End
	if ($reset) { $n = 0; return true; } // resets $n when end of loop is reached (called by TP_End())
	$ret = (0 == $n);
	$n++;
	return $ret; // bool
}

/**
 * @todo RISK of BUG ! <= if does not return true within foreach
 * ... => $n remains at last level of foreach !!! => does not start at 0 for
 * ... next foreach !!!
 *
 * @param unknown_type $int
 * @return unknown
 */
function TP_is($int, $reset = false)
{
	static $n = 0;
	if ($reset) { $n = 0; return true; } // workaround BUG... <= called from TP() !
	$ret = ($n == (int) $int);
	$n++;
	if ($ret) $n = 0;
	return $ret;
}