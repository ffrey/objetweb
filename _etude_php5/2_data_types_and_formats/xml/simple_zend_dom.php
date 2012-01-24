<?php
/**
 * 2012 01 24 : comment afficher avec le bon encoding ?
 *
 * use : http://simplehtmldom.sourceforge.net/ <= http://davidwalsh.name/php-notifications
 *       http://framework.zend.com/manual/en/zend.dom.query.html
 */
// C:\wamp\lib\ow\_etude_php5\2_data_types_and_formats\xml\php53 simple_xpath.php
# recup html d'une page web
$url='www.msf.fr';
$client = new Zend_Http_Client($url);
$response = $client->request(); 
$html = $response->getBody();
$dom = new Zend_Dom_Query($html);

# recup par xpath de simple xml une partie de la page
$results = $dom->query('.foo .bar a');
 
$count = count($results); // get number of matches: 4
foreach ($results as $result) {
    // $result is a DOMElement
	
}