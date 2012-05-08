<?php
class myClass {
    public $member = "ABC";
    static function showMember() {
        var_dump(self::$member);
    }
}
class child {
    public show()
    {
        echo $this->member;
    }
}
$c = new child();
$c->show();