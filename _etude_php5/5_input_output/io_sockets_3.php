<?php
var_dump(stream_get_transports() );

echo "====================\n";
$fp = fsockopen("www.zend.com", 80, $errno, $errstr, 30);
var_dump('meta data', stream_get_meta_data($fp) );
if (!$fp) {
    echo "$errstr ($errno)<br/>\n";
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: www.zend.com\r\n";
    $out .= "Connection: close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp) ) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}