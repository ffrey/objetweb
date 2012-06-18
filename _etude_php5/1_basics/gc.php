<?php
$phpunit = false;
if ($phpunit) {
    $path = 'C:\phpunit36\phpunit';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    require_once dirname(__FILE__).'/../_bootstrap.php';
}
$a = 'hello';
if ($phpunit) xdebug_debug_zval('a');
$b = &$a;
echo('apres assignation par ref'.PHP_EOL);
if ($phpunit) xdebug_debug_zval('a');

$b = 'apres modif via b !';
echo('new a : ' . $a.PHP_EOL);