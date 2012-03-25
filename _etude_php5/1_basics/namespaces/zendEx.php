<?php
// php53 C:\wamp\lib\ow\_etude_php5\_basics\namespaces\zendEx.php
require_once dirname(__FILE__).'/lib/zendExLib.php';
/** 
 * SOLUTION 1

use \My;

$B = new \My\B(new \My\A() );
$B->hello();
 */

/**
 * SOLUTION 2

use \My\A, \My\B;

$B = new B(new A() );
$B->hello();
 */