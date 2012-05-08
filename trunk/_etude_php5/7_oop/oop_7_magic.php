<?php
class Magic 
{
    public function __call($name, $arg)
    {
        echo "calling " . __METHOD__ . print_r(func_get_args(), true);
    }
    static public function __callStatic($name, $args)
    {
        echo "calling " . __METHOD__ . print_r(func_get_args(), true);
    }
    
    public function __invoke()
    {
        echo "calling " . __METHOD__ . print_r(func_get_args(), true);
    }
    
    public function __set_state($v)
    {
        return $v;
    }
}

$t = new Magic();
$t->hello();
$t();
Magic::bonjour();

print "===================\n\r";
echo var_export($t);