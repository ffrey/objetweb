<?php
$myArray = array("Three" => 3, "Two" => 2, "One" => 1);
sort($myArray);
var_dump($myArray);
$keys = array_keys($myArray);
var_dump($keys);
echo $keys[0];

echo "\n\r=============";
$array1 = $array2 = array("img12.png", "img10.png", "img2.png", "img1.png");

asort($array1);
echo "Standard sorting\n";
print_r($array1);

natsort($array2);
echo "\nNatural order sorting\n";
print_r($array2);