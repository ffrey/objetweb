<?php
$path = 'C:\phpunit36\phpunit';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once dirname(__FILE__).'/../_bootstrap.php';
$a = 'hello';
xdebug_debug_zval('a');
$b = &$a;
cmd('apres assignation par ref');
xdebug_debug_zval('a');

$b = 'apres modif via b !';
cmd('new a : ' . $a);