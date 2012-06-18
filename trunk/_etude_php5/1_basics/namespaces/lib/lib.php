<?php
namespace ow\lib; // see "Defining Namespaces" section

class Text 
{
	public function show($arg)
	{
		return 'argument is ' . $arg;
	}
}
function owPrint($var = null) { 
    return sprintf('this is from owPrint function within %s : %s', __NAMESPACE__, $var); 
}
const OWCONST = 'hello';