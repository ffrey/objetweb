<?php
// php53 C:\wamp\lib\ow\_etude_php5\_basics\namespaces\ns_exo.php
/**
 *
 * Ni les fonctions, ni les constantes ne peuvent être importées avec la commande use
 * @see http://www.php.net/manual/fr/language.namespaces.faq.php
 */
require_once 'lib/lib_exo.php';
use myapp\utils\hello as foo;

echo foo\world();
