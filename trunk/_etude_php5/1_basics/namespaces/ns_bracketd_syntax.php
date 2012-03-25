<?php
echo "strangely enough, the 'use' statement seems to be able to
 include namespaces as such as well as a class !?\n\r";
include 'lib/lib_brackets.php';

use my\brackets AS go, my\brackets\B as cl;

$b = new cl;
$b->hello('alfred');

go\lastLetter('alfred');