<?php
namespace myapp\utils\hello;

function world() {
	return 'this is ' . __FUNCTION__;
}


class Voice {
	public function sayHello($name)
	{
		return 'Hello ' . $name;
	}
}