<?php
namespace ow\lib;
require 'lib/lib.php';
require 'lib/global.php';

$a = new Text();
print $a->show('go');

$b = new Iam();
echo $b;

echo echoGlob('John');