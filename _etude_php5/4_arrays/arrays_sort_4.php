<?php
echo "REGULAR is ok on numeric or numeric-compatible values";
echo "===========\n";
$prior = $a = array(5, "43", 2, "10");
sort($a, SORT_REGULAR);
var_dump($prior, $a);

echo "===========\n";
$prior = $a = array("5", "43", "2", "10");
sort($a);
var_dump($prior, $a);

echo "===========\n";
echo "... but not with strings !\n";
$prior = $a = array("a5", "a43", "a2", "a10");
sort($a);
var_dump($prior, $a);