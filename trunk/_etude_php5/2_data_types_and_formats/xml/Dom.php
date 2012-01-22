<?php
// port : php C:\EasyPHP_3_0\php\ow\_etude_php5\2_data_types_and_formats\xml\Dom.php
$doc = new DOMDocument();
$doc->loadXML('<root />');
$root = $doc->getElementsByTagName('root')->item(0);
$el   = $doc->createElement('test', 'some value');
$doc->documentElement->appendChild($el);
// $root->appendChild($el);
echo $doc->saveXML();