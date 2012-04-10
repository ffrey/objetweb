<?php
/*
function myFunction($p) {
    return $p;
}
$x = myFunction();
echo "=================f\n";
function get($a, $b, $c=4, $d) {
    print "[$d, $c, $b, $a]\n";
}
get(2,3,5);
echo "=================f\n";
function ref(&$a) {
    $a++;
}  
$b = 1;
echo "prior : $b\n";
ref($b);
echo "after : $b\n";
*/
/*
echo "========  Only variables can be passed by reference =========f\n";
function test($one, &$two, &$three="3") {
    echo "[$one, $two, $three]\n";
    $three+=3;
    $two++;
}

$one = 21;
$two = 22;
test($one, $two, 23);
*/
/*
echo "============\n";
function xwz() { echo "hello from __FUNCTION__ !"; }
$f = 'abc';
$abc = 'xwz';
$$f();
*/
echo "============\n";
$a = 2;
$b = 3;
function test() {
    global $b;
    static $a;
    printf("global b : %d, static a : %d\n",
            $b, $a);
    $a++;
    
    $b+= $a;
    
    global $a;
    printf("global a : %d\n", 
            $a);
    $a += 2;
}
       // global $b     static $abs     global $a
test();// 3 => 4         0 => 1              2 => 4
test();// 4 => 6         1 => 2              4 => 6
test();// 6 => 9         2 => 3             6 => 8
echo "$a, $b"; // 8, 9

echo "====================\n";
function globish()
{
    global $g;
    $g = 'speach globish !';
}
globish();
echo $g;