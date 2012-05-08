<?php
class myClass {
    public static $member = "ABC";
    static function showMember() {
        var_dump(self::$member);
    }
}
myClass::showMember();
echo myClass::$member;

echo "============\n";
class Me {
    const NAME = "Dr. Evil";
}
class MiniMe extends Me { }
echo MiniMe::NAME;