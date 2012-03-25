<?php
echo "! OCTAL : int starting with 0 until next impossible octal
 value (>7 !)\n\r";
$var = 12;

$var .= 3;
echo $var;

echo "\n=====\n";
$t = array(
1234e-3,
1.234,
1.5,
# parse error : 1.5.5,
0024,
"018",
" 25",
"A3",
"3A",
);
foreach ($t AS $v) {
var_dump($v);
$var = (int) $v;
echo $var."\n\r";
}   

echo "\n=====\n";
$t = array(
018,
'018',
' 25',
'A3',
'3A',
);
foreach ($t AS $v) {
$var = (int) $v;
echo $var."\n\r";
}   