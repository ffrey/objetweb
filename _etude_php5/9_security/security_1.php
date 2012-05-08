<?php
function e($str)
{
    return str_pad(
        decbin($str), 
        15, 
        ' ', 
        STR_PAD_LEFT
    );
}
$dep = E_DEPRECATED;
$all = E_ALL;
var_dump('dep', e($dep), 'all', e($all) );

$new = $all | $dep;
var_dump('new', e($new) );