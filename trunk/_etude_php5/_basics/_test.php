<?php
define('CONSTANT', 1);
define('_CONSTANT', 0);

define('EMPTY', '');
$var = 'ee';
if (!EMPTY($var) ) {
	print 'hello';
}