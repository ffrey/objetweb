<?php
$var = substr("123456", 2, 3);

var_dump("345", $var);

$var2 = substr("123456", -4, -2);

var_dump("34", $var2);

echo "======".PHP_EOL;
$x = 'ababa';
$pos = 0;
$nr  = 0;

$nb = preg_match_all('/a/', $x, $all);
var_dump($nb);

echo "======".PHP_EOL;
$ret = False * -3;
var_dump($ret);

$cast = (int) True;
var_dump($cast, True);