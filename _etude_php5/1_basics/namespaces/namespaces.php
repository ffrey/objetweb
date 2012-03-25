<?php
// C:\wamp\lib\ow\_etude_php5\_basics\namespaces>php535 namespaces.php
// home : C:\EasyPHP_3_0\php\ow\_etude_php5\_basics\namespaces\php535 namespaces.php
namespace my\name; // see "Defining Namespaces" section

class MyClass {}
function myfunction() {}
const MYCONST = 'hello';
define('TRADI_CONST', 'tradi');
define('my\name\NS_DEFINED', 'definie dans ns avec define()');
$a = new MyClass;
$c = new \my\name\MyClass; // see "Global Space" section

$a = strlen('hi'); // see "Using namespaces: fallback to global
                   // function/constant" section

$d = namespace\MYCONST; // see "namespace operator and __NAMESPACE__
                        // constant" section
$d = __NAMESPACE__ . '\MYCONST';
printf('constante definie avec const dans ns %s : %s'.PHP_EOL, __NAMESPACE__, constant($d) ); // see "Namespaces and dynamic language features" section
if (!defined('\my\name\TRADI_CONST') ) {
	print 'define() ne definit des constantes QUE dans le scope global !!!'.PHP_EOL;
}
printf('constante definie avec define() : %s'.PHP_EOL        ,  TRADI_CONST); 
printf('\my\name\MYCONST existe ? => %s'.PHP_EOL, defined('\my\name\MYCONST')?'true':'false' );

if (defined('my\name\NS_DEFINED') ) {
	printf('on pt aussi utiliser define() a la place de const pour creer une constante dans un ns : %s', \my\name\NS_DEFINED);
}