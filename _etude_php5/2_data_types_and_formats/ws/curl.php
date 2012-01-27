<?php
/**
 * tester des req brutes !
 */
$url = 'asso95.fr';
// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $url);
// 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);

// grab URL and pass it to the browser
$res = curl_exec($ch);
// 
var_dump($res);

// close cURL resource, and free up system resources
curl_close($ch);
?>