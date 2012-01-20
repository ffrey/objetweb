<?php
$file = 'sample1.xml';
$xml = simplexml_load_file($file); 

// $xml = new SimpleXMLElement($string);

/* On cherche <a><b><c> */
$xpath = '/node/leaf';
$result = $xml->xpath($xpath);

while(list( , $node) = each($result)) {
    echo $xpath.' : ',$node,"\n";
}