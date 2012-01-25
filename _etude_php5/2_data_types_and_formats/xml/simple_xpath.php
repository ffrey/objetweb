<?php
/**
 * 2012 01 24 : comment afficher avec le bon encoding ?
 *
 * @see simple_zend_dom.php pour ex d'utilisation de Zend_Dom_Query : http://framework.zend.com/manual/en/zend.dom.query.html
 */
// php53 C:\wamp\lib\ow\_etude_php5\2_data_types_and_formats\xml\simple_xpath.php
# recup html d'une page web
// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, "http://www.msf.fr/rss.xml");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// grab URL and pass it to the browser
$page = curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

// var_dump($page);
# recup par xpath de simple xml une partie de la page
$xml = simplexml_load_string($page);
$path = "channel/item/title";
// div/div[@id='wrapper']/div[2]/div[1]/div/div/div/div/div/div[1]/ul[@id='slider1']/li[3]/h2/a 
$result = $xml->xpath($path);
// var_dump($result);
while(list( , $node) = each($result)) {
    echo 'content : ',htmlentities($node),"\n";
}
