<?php
// port : php C:\EasyPHP_3_0\php\ow\_etude_php5\2_data_types_and_formats\xml\Dom.php
$doc = new DOMDocument();
$doc->loadXML('<root />');
$doc->documentElement->setAttribute('attr', 'some value');

echo $doc->saveXML();