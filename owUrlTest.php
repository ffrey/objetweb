<?php
// phpunit C:\wamp\lib\ow\owUrlTest.php
require_once 'owUrl.php';

class owUrlTest extends PHPUnit_Framework_TestCase
{
  public function testAddQuery()
  {
	$aTests = array(
		array('url' => '/hello/', 'query' => 'yes=no',
			'expect' => '/hello/?yes=no',
		),
		array('url' => 'http://name.com/bonjour/', 'query' => 'yes=no',
			'expect' => 'http://name.com/bonjour/?yes=no',
		),
		// ! si trailing ? present !
		array('url' => 'http://name.com/bonjour/?', 'query' => 'yes=no',
			'expect' => 'http://name.com/bonjour/?yes=no',
		),
		array('url' => 'http://name.com/bonjour/?hello=bonjour', 'query' => 'yes=no',
			'expect' => 'http://name.com/bonjour/?hello=bonjour&yes=no',
		),
		array('url' => 'http://name.com/bonjour/#', 'query' => 'yes=no',
			'expect' => 'http://name.com/bonjour/?yes=no#',
		),
		array('url' => 'http://name.com/bonjour/#quoi', 'query' => 'yes=no',
			'expect' => 'http://name.com/bonjour/?yes=no#quoi',
		),
		array('url' => 'http://name.com/bonjour/#quoi', 'query' => 'yes=no&lkjk=lkjkl',
			'expect' => 'http://name.com/bonjour/?yes=no&lkjk=lkjkl#quoi',
		),
		// fonctionne aussi avec un array en argument de query
		array('url' => 'http://name.com/bonjour/#quoi', 'query' => array('yes' => 'no', 'lkjk' => 'lkjkl'),
			'expect' => 'http://name.com/bonjour/?yes=no&lkjk=lkjkl#quoi',
		),
		array('url' => 'http://name.com/bonjour/#quoi&', 'query' => array('yes' => 'no', 'lkjk' => 'lkjkl'),
			'expect' => 'http://name.com/bonjour/?yes=no&lkjk=lkjkl#quoi', 'comment' => 'corrige les & terminaux',
		),
		/*'*/
	);
	foreach ($aTests AS $a) {
		$got = owUrl::addQuery($a['url'], $a['query']);
		$comment = array_key_exists('comment', $a)? $a['comment'] : '';
		$this->assertEquals($a['expect'], $got, $comment);
	}
  }
}