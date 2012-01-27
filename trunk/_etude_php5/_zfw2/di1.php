<?php
/**
 * http://akrabat.com/wp-content/uploads/PHPNW11-ZF2-Tutorial.pdf
 * http://www.slideshare.net/jpauli/zendframework2-prsentation
 * http://alcides-notes.ijintek.fr/index.php?post/2011/09/22/ZF2-dev4-Petit-point-sur-l-autoloading
 */
// php53 C:\wamp\lib\ow\_etude_php5\_zfw2\di1.php
require_once 'C:\PROJETS_WEB\lib\zend_fw_2_b1\zf2\library\Zend\Loader\StandardAutoloader.php';
/**
$paths = array(
	'namespaces' => array(
		'Zend' => 'C:\PROJETS_WEB\lib\zend_fw_2_b1\zf2\library\Zend',
	),
);
$loader = new Zend\Loader\StandardAutoloader($paths);
*/
$loader = new Zend\Loader\StandardAutoloader();
$loader->registerNamespace('Zend', 'C:\PROJETS_WEB\lib\zend_fw_2_b1\zf2\library\Zend');
$loader->register();
$loader->setFallbackAutoloader(true);
// use \Zend;
$di = new Zend\Di();

var_dump(get_class_name($di) );