<?php
$a = array(047, 057, 067, 047, 057, 067);
$b = array(47, 57, 67);


// 047 : 0 + (8x4) + 7 = 39
// 057 => 47 <=
// 067 => 55

var_dump(array_diff($a, $b) ); // 4 diffs ?

var_dump($a[0], 39 === 047);

echo "=============\n";
$a = array(0, 2, 4);
$b = array(1, 2, 3);
$c = array_merge($a, $b);
// 0, 2, 4, 1, 2, 3 ???
var_dump($a);
