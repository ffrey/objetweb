<?php
echo "===========\n";
// passing by ref : for array_WALK !
function wucfirst(&$v, $k = null)
{
    $v = ucfirst($v);
    $v = '_'.$v;
    return $v; // for array_MAP !
}
$a = $b = array('gaga', 'paulo', 'aldo', 'angela');
array_walk($a, 'wucfirst');
var_dump($a);

echo "===========\n";
$b = array_map('ucfirst', $b);
var_dump($b);

echo "===========\n";
$b = array_map('wucfirst', $b);
var_dump($b);