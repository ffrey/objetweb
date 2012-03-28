<?php
// phpunit C:\wamp\lib\ow\_etude_php5\_basics\operatorsTest.php
require_once dirname(__FILE__).'/../_bootstrap.php';

class operatorsTest extends PHPUnit_Framework_TestCase
{
	public function testEither()
	{
		step('Bitwise Operators : & | ^');
		cmd('^ : Xor');
		$tests = array(
		array(1 ^ 0, 1),
		array(1 ^ 1, 0),
        array(E_CORE_WARNING | E_PARSE, 36),
        // 32 | 4 => 100000 | 000100 => 100100 = 36
		);
        
		foreach ($tests AS $t) {
			
			$this->assertEquals($t[0], $t[1]);
		}
	}
	
	public function testArrayOperators()
	{
		step('array ops');
		cmd('+ union');
		$a1 = array('a' => 'tab1', 'b' => 'tab1');
		$a2 = array('a' => 'tab2', 'c' => 'tab2');
		$expect_union = array(
		'a' => 'tab1',
		'b' => 'tab1',
		'c' => 'tab2',
		);
		$expect_merge = $expect_union;
		$got = $a1 + $a2;
		$this->assertEquals($got, $expect_union,
		'union : left-hand array takes precedence');
		$expect_merge['a'] = 'tab2';
		$got = array_merge($a1, $a2);
		$this->assertEquals($got, $expect_merge,
		'merge : right-hand array takes precedence');
		
	}
	
	public function testLogicalOps()
	{
		step('Logical Operators');
		cmd('return boolean !');
		$tests = array(
			array(1 XOR 1, false),
		);
		foreach ($tests AS $t) {
			$this->assertEquals($t[0], $t[1]);
		}
	}
	
	public function testExecutionOperators()
	{
		step('Execution Operators');
		cmd('Use backticks `` to execute the content as shell command');
		$expect = shell_exec('ls -ls');
		$got    = `ls -ls`;
		$this->assertEquals($got, $expect);
	}
	
	public function testConstructs()
	{
		step('Constructs');
		cmd('die & exit, eval are functions, hence use parentheses if needed !');
		cmd('return is a control structure, hence no need to use parentheses... !');
	}
}
