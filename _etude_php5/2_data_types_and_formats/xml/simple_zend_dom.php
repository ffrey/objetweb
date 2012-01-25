<?php
/**
 * 
 *       http://framework.zend.com/manual/en/zend.dom.query.html
 */
// php53 C:\wamp\lib\ow\_etude_php5\2_data_types_and_formats\xml\simple_zend_dom.php
/* */
$paths = array(
    'C:\wamp\lib\zend\library',
    '.',
);
set_include_path(implode(PATH_SEPARATOR, $paths) );
require_once 'C:\wamp\lib\zend\library\Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
# recup html d'une page web
$url='http://www.msf.fr';
$client = new Zend_Http_Client($url);
$response = $client->request(); 
$msg = $url . ' is unavailable !-(';
if ($response->isSuccessful() ) {
	$msg = 'reponse ok !'."\n\r";
}
print $msg;
$html = $response->getBody();
// var_dump($response->getBody() ); exit;
$dom = new Zend_Dom_Query($html);

# recup par xpath de simple xml une partie de la page
$css = 'h2.item-name';
$results = $dom->query($css);
 
$count = count($results); // get number of matches: 4
print $css . ' : ' . $count . "\n\r";
foreach ($results as $result) {
    // $result is a DOMElement
	print htmlentities($result->textContent)."\n\r";
}