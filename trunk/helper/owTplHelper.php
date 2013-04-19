<?PHP
/*** common view helpers ***/
/**
 * @deprecated : too specific <= depends on Propel class !
 
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
*/

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
    switch (count($vars) ) {
        case 1:
        echo r($vars[0] );    break;
        case 2: 
        echo r($vars[0], $vars[1]); break;
        case 3:
        echo r($vars[0], $vars[1], $vars[2]);    break;
        case 4:
        echo r($vars[0], $vars[1], $vars[2], $vars[3]); break;
    }
 }
function r()
{
	$db = 0;
	$before = ''; $var; $after = ''; $alt = '';
	$args = func_get_args();
	if (!sizeof($args)) { throw new Exception(__FUNCTION__ . ' : arguments insuffisants'); }
	if (sizeof($args) == 1) {
		$var = $args[0];
	} else {
		$before = $args[0]; $var = $args[1];
		if (isset($args[2])) $after = $args[2];
		if (isset($args[3])) $alt = $args[3];
	}
	if ($db) print 'vars : ' . $before . ' / ' . $var . ' / ' . $after . ' / ' . $alt . '<br />';
	$ret = '';
	do {
		if ('' === $var OR null === $var OR false === $var) { // NOT 0 !
			if ('' === $alt) break;
			$ret = $alt;
			break;
		} else {
			$var = is_bool($var)?'':$var; // ne pas afficher 1 si $var = true !
			$ret = $before . $var . $after;
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
/**
 * @return mixed : false if is php false ('', null, false BUT NOT 0 !) + empty array /  $var else
 */
function TP($var)
{
	$ret = false;
	do {
		if (is_array($var) AND !count($var) ) { break; } // empty array
		if (''    === $var OR
		    false === $var OR 
		    null  === $var) 
		{ break; }
		$ret = $var;
	} while (false);
	return $ret;
}