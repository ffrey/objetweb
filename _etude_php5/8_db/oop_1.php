<?php

class A { 
    protected $a = 1;
    function a() { echo "\n\r".__FUNCTION__."\n\r"; echo $this->a++; }
}

class B extends A {
    protected $a = 10;
    function b() { echo "\n\r".__FUNCTION__."\n\r"; echo $this->a++; $this->a(); }
    public function destruct()
    {
        $gb = new GB();
    }
}

$b = new B();
$b->b();

echo "================\n";
class GB {
        public function __destruct()
        {
            echo "good bye";
        }
}
$b->destruct();
echo "end of script";
// $gb = new GB();


