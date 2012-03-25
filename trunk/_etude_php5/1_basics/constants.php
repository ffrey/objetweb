<?php
echo '? contrary to what seems to be said in 1st lesson : all CONSTANTS 
have global scope wherever they are initialized (be it within class methods, etc.)'."\n\r";

define ('HELLO', 'my name is ok', true);

echo 'constant : ' . hello . "\n\r";

function def ($name) 
{
    define('FUNC_CST', $name);
}
def('alberto');
echo 'function constant : ' . FUNC_CST . "\n\r";

class test
{
    public function hello($name)
    {
        define ('CLASS_CONSTANT', $name);
    }
}
$t = new test();
$t->hello('sandra');
echo CLASS_CONSTANT;