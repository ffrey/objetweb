<?php
$myArray = array("hello", "Three" => 3, 3 => 2, "One" => 1, "what");
var_dump($myArray);

$a = array('john', 4 => 'peter', 'angela');
$r = array_reverse($a, false);
var_dump($a, $r, $r[0]);

echo "\n\r============";
$r = range(5.0, 3.0, 0.17);
var_dump($r, count($r) );

echo "\n================";
$str = "this is a cool feature ;-)";
$smiley = substr($str, -3, 3);
var_dump($str, 'substr', $smiley, $str);
echo "\n================";
$a = explode(" ", $str);
$smiley_slice = array_slice($a, 5, 1);
var_dump($a, 'splice to keep smiley slice ;-)', $smiley_slice);

echo "\n=============";
$a = array(7 => 1, 2, 3);
$a[] = 4;
// array(7 => 1, 8 => 2, 9 => 3, 10 => 4)
$a[2] = 5;
// array(2 => 5, ...) !
var_dump('step 2', $a);
array_unshift($a, 6);
// array(0 => 6, 2 => 5, ...)
// $a[7] == 1 !!!
var_dump($a, $a[7]);