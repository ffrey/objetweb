<?php
echo 'Publicis<br/>';
if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '10.196.172.17',
))) {
    header('HTTP/1.0 404 File Not Found');
    exit('File Not Found.');
}
var_dump($_SERVER);
var_dump($_ENV);
echo 'host : ' . $_ENV['HOSTNAME '] . '<br/>';
echo 'path : ' . realpath('.');
echo phpinfo();