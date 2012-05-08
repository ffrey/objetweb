<?php
class myClass {
    public $member = "ABC";
    public function show() {
        var_dump($this->member);
    }
}
class child extends myClass {
    public $member = "DEF",
    $h = 'hello';
    public function show()
    {
        echo $this->member;
    }
}
$c = new child();
$c->show();

echo serialize($c);
