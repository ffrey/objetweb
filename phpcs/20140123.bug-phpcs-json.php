<?php
/**
 * 2014 01 24 : ??? click upon link "Report new bug" returns blank page ???
 * phpcs version : PHP_CodeSniffer version 1.5.1 (stable)
 
 * using json_decode on report format "json" returns NULL with error of type JSON_ERROR_SYNTAX
 
 */
$cmd = 'phpcs -n --standard=PubSf1 --report=json ./fichiers_tests/erreurs.php'; 
exec($cmd, $outputJson, $ret);
$aJson = json_decode($outputJson[0], true);
var_dump(
	'raw output', $outputJson[0], $ret
	, 'after json_decode', $aJson
	, (JSON_ERROR_SYNTAX === json_last_error() ) ? 'error of type JSON_ERROR_SYNTAX' : 'error json undefined'
);