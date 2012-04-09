<?php
/*
$str = "monkeys are smarter than vultures";
$new = preg_replace('/(monkeys)(.*)(vultures)/',
                    '$3$2$1',
                    $str);
var_dump($str, $new);
*/
echo "==========";
$str = "hello you, you say hello!";
$nb = preg_match_all('/(hello)!/', $str, $matches);
var_dump('matches', $matches);
function cb($match) {
    var_dump(__FUNCTION__, $match);
    return strtoupper($match[0]);
}
$new = preg_replace_callback('/(hello)!/', 'cb', $str);
var_dump('with callback', $new);
