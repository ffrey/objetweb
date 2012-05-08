<?php
class myClass {
    const C = 'Cparent';
    protected static $s = 'sparent';
    static public function show() {
        echo 'parent : ' . self::C . ' / ' . self::$s;
    }
}
class child extends myClass {
    const C = 'Cchild';
    protected static $s = 'schild';
    static public function showChild()
    {
        echo 'child : ' . self::C . ' / ' . self::$s;
        // echo ' / parent : ' . parent::showMember();
    }
}
child::showChild();
print "\n\r";
child::show();
// var_dump(child::C, myClass::C);