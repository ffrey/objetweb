<?php
namespace ow\lib; // see "Defining Namespaces" section

class Text 
{
	public function show($arg)
	{
		return 'argument is ' . $arg;
	}
}
function owPrint() { return 'this is from owPrint function...'; }
const OWCONST = 'hello';