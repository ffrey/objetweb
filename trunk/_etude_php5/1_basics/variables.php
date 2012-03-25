<?php
$var = "variable\n\r";

echo $var;
echo ${'var'};

$str = "var is ${'var'}ok";
echo $str;

$str = "var is {$var}ok";
echo $str;