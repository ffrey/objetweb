<?php
/**
 * @source http://framework.zend.com/manual/1.11/en/zend.log.factory.html
 * ATTENTION : quelques erreurs !!!
 * => option timestampFormat imposs !
 * => %info% => %priority%
 */
// php C:\wamp\www\publicis_projets\projet_ptLib\LIB\ptLog\_test\raw_example.php
require_once 'PHPUnit/Framework.php';

// bootstrap
$zend_lib = "C:\wamp\lib\zend\library\\";
set_include_path(get_include_path() . PATH_SEPARATOR . $zend_lib);
require_once $zend_lib.'Zend\Loader\Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

require_once dirname(__FILE__).'/../ptLog.class.php';

$logger = Zend_Log::factory(array(
    // 'timestampFormat' => 'Y-m-d',
    array(
        'writerName'   => 'Stream',
        'writerParams' => array(
            'stream'   => 'C:\wamp\www\publicis_projets\projet_ptLib\LIB\ptLog\_test\logs\zend.log',
        ),
        'formatterName' => 'Simple',
        'formatterParams' => array(
            'format'   => '%timestamp%: %message% -- %priorityName% (%priority%)'."\r",
        ),
    ),
));
$logger->setTimestampFormat('d/m/Y');
$logger->info('hello');
$logger->info('autre !');

print 'end writing to log !';