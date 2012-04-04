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

echo "======".PHP_EOL;
$first =  'Qu\'en penses-tu ?'; // 5
$second = "qu'en pensez-vous ?"; // 13
var_dump('length', strlen($second) );
var_dump('levenshtein', levenshtein($first, $second) );
var_dump('similar_text', similar_text($first, $second) );

echo "======".PHP_EOL;
var_dump('explode', explode('.', '...') );