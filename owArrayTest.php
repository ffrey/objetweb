<?php
// php phpunit-4.8.phar owArrayTest.php

//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'owArray.php';

/**
 * @see http://www.php.net/manual/fr/function.is-numeric.php
 */
class owArrayTest extends PHPUnit_Framework_TestCase
{
	protected $data;
 
    protected function setUp()
    {
        // Create the Array fixture.
		$expect_date = '10/11/2010';
		$this->data['expect_date'] = $expect_date;
        $this->data['array'] = array(
		'don' => array('transac_date' => array('to' => $expect_date) ),
		'type' => 'pso',
		'valeur_vide' => '',
		);
    }

  public function testG_if()
  {
		$expect_date = $this->data['expect_date'];
		$array = $this->data['array'];
		
		$got = owArray::g_if('type', $array);
		$this->assertEquals($got, $array['type']);
		
		$default_value = 'default';
		$got = owArray::g_if('unknown', $array, $default_value);
		$this->assertEquals($got, $default_value);
		
		$got = owArray::g_if('valeur_vide', $array, $default_value);
		$this->assertEquals($got, $default_value,
		'si la cle existe mais la valeur est string vide, alors on renvoie $default_value');
		
		$got = owArray::g_if('valeur_vide', $array);
		$this->assertEquals($got, '');

	}
	
	public function testFlatten_sep()
	{
		$this->markTestIncomplete(
          'flatten_sep disappeared ? => see util::flatten() for implementation'
        );
	
		$expect_date = '10/11/2010';
		$mail = 'noreply@domain.com';
		$a = array('one', 'two');
		$multi = array(
			'transac_date' => array(
				'to' => array('hello' => $expect_date),
				'from' => $mail,
				'simple',
				'array' => $a,
				),
			'simple' => 'easy',
		);
		
		$got = owArray::flatten_sep('_', $multi);
		$expect = array(
			'transac_date_to_hello' => $expect_date,
			'transac_date_from' => $mail,
			'transac_date_array_0' => 'one',
			'transac_date_array_1' => 'two',
			'simple' => 'easy',
			'transac_date_0' => 'simple',
		);
		$this->assertEquals($expect, $got);
		
		return $expect;		
	}
	
	public function test_Split_into()
	{
	$this->markTestIncomplete(
          '_split_into disappeared ? => see util::flatten() for implementation'
        );
	
		$test = $this->data['array'];
		$got  = owArray::_split_into($test, 2);
		// var_dump($got);
		$this->assertEquals(count($test), count($got[0])+count($got[1]) );
	
		$nb_test = count($test);
		$got_max = owArray::_split_into($test, $nb_test);
		$over_count = $nb_test + 3;
		$got  = owArray::_split_into($test, $over_count);
		$this->assertEquals($got, $got_max);
		$this->assertEquals(count($got), $nb_test,
		'on ne peut pas couper en plus de morceaux que le nombre de morceaux initial !');
	}
	
	/**
	 * @depends testFlatten_sep
	 */
	public function testMultiUnset($a)
	{
		$init_count = count($a);
		$unset = array(
		'transac_date_from', 'simple');
		$got = owArray::multiUnset($a, $unset);
		// var_dump($got);
		$this->assertEquals($init_count-count($unset), count($got) );
		$all_keys_unset = true;
		foreach ($unset AS $k) {
			if (array_key_exists($k, $got) ) {
				$all_keys_unset = false; break;
			}
		}
		$this->assertTrue($all_keys_unset);	
	}
	
	public function testArray_merge_with_numeric_keys()
	{
	$this->markTestIncomplete(
          'array_merge_with_numeric_keys disappeared ? => see util::flatten() for implementation'
        );
		$trois = array('one', 2);
		$test = array(
			'simple' => 'key',
			3        => $trois,
			'4'      => 'string',
		);
		$test2 = array(
			5        => 'cinq',
			'4'      => 'quatre',
		);
		$expect = array(
			'simple' => 'key',
			3        => $trois,
			5        => 'cinq',
			'4'      => 'quatre',
		);
		$got = owArray::array_merge_with_numeric_keys($test, $test2);
		$this->assertEquals($expect, $got);
		
		return $expect;
	}
	
	/**
	 * @depends testArray_merge_with_numeric_keys
	 *
	 */
	public function testTranslateKeys($a)
	{
		$old = 'simple'; $new = 'easy_key';
		$t = array($old => $new);
		$got = owArray::translateKeys($a, $t);
		$new_key_replaced = array_key_exists($new, $got);
		$this->assertTrue($new_key_replaced);
		$this->assertEquals($a[$old], $got[$new]);
	}
}