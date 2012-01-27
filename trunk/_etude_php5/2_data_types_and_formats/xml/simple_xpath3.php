<?php
// php53 C:\wamp\lib\ow\_etude_php5\2_data_types_and_formats\xml\simple_xpath3.php
$xml = <<<TEXT
<?xml version="1.0" encoding="ISO-8859-1"?>

<bookstore>

<book>
  <title lang="eng">Harry Potter</title>
  <price>29.99</price>
</book>

<book>
  <title lang="eng">Learning XML</title>
  <price>39.95</price>
</book>

</bookstore>
TEXT;
$X = simplexml_load_string($xml);

$res = $X->xpath('*/title');
$i   = 0;
foreach($res AS $r) { 
	$i++;
	// $r = (string) $r;
	printf('result %d : %s'."\n\r", $i, $r->getName() );
}