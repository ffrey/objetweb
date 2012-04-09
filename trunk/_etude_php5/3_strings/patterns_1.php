<?php
$str = <<<HER
What do you think ?
I thinked is's ok ;-)
think
ok !
HER;
echo $str;
/**
$count = preg_match_all('/think/', $str, $res);
var_dump('with nothing : get 3', $count, $res);
$count = preg_match_all('/\bthink\b/', $str, $res);
var_dump('with \b & \b : get 2', $count, $res);
$count = preg_match_all('/think/m', $str, $res);
var_dump('with /m : get 3 ', $count, $res);
*/
$count = preg_match_all('/^think$/', $str, $res);
// got 0 alors ke j'attendais 1 : start of line + think + end of line
var_dump('with ^ & $', $count, $res);
$count = preg_match_all('/^think$/m', $str, $res);
// got 0 alors ke j'attendais 1 : start of line + think + end of line
var_dump('with ^ & $ + multiline !', $count, $res);
/*
$count = preg_match_all('/\Athink\Z/m', $str, $res);
// got 0
var_dump('with \A & \Z', $count, $res);
*/