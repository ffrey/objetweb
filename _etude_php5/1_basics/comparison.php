<?php
var_dump(123, 1230e-1, 123 !== 1230e-1);

echo "======\n\r";
echo "comparison of 2 strings follows the ascii value order !\n\r
=> see http://www.asciitable.com/\n\r
!!! ONLY ON FIRST CHAR without heeding space or line feed !!!\r\n";
$t = (int) 'A';
echo "int for A is " . $t."\n\r";
echo "A is ".ord('A')."\n\r";

echo "a is ".ord('a')."\n\r";

var_dump("1 > 'a'", 1 > 'a'); // true !
var_dump("'1' > 'a'", '1' > 'a'); // false !
var_dump("'1000' > 'a'", '1000' > 'a');
var_dump('AA > a', 'AA' > 'a');
$egal = 'AA' == 'a';
var_dump('AA == a', $egal);
var_dump('AA > a1', 'AA' > 'a1');
var_dump('AA > 1a', 'AA' > '1a');
$var = "php";
$VAR = "PHP";
$var2 = "Aphp";
var_dump("$var > $VAR", $var > $VAR);

var_dump("$var2 > $VAR", $var2 > $VAR);

var_dump("X > $VAR", 'X' > $VAR);

$int = '39';
$int2 = '100';
var_dump($int, $int2, $int < $int2);
