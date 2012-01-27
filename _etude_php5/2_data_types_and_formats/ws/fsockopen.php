<?php
/**
 * tester des req brutes !
 */
$url = 'asso95.fr';
$fp = fsockopen($url, 80, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out  = "GET / HTTP/1.1\r\n";
	$out .= "Host: ".$url."\r\n";
    $out .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1\r\n";
    $out .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
    $out .= "Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3\r\n";
    // $out .= "Accept-Encoding: gzip, deflate\r\n";
    $out .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
    $out .= "Connection: keep-alive\r\n\r\n";
    // $out .= "Referer: http://association.mon-guide.info/\r\n";
    // $out .= "Cookie: PHPSESSID=d77da84fe378541c05027dfed5357413; __utma=185110457.574782887.1327658355.1327658355.1327658355.1; __utmb=185110457.3.10.1327658355; __utmc=185110457; __utmz=185110457.1327658355.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=association%2095\r\n";
	// $out .= "\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}
/**
Host: association.mon-guide.info
User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,/*;q=0.8
Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3
Accept-Encoding: gzip, deflate
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
Connection: keep-alive
Referer: http://association.mon-guide.info/
Cookie: PHPSESSID=d77da84fe378541c05027dfed5357413; __utma=185110457.574782887.1327658355.1327658355.1327658355.1; __utmb=185110457.3.10.1327658355; __utmc=185110457; __utmz=185110457.1327658355.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=association%2095

*/