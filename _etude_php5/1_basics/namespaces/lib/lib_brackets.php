<?php
namespace my\brackets {
    class B {
        public function hello($name) 
        {
            printf('Salut %s'.PHP_EOL, $name);
        }
    }
    
    function lastLetter($a) {
        $last = substr($a, -1);
        printf('last letter of %s is : %s'.PHP_EOL, $a, $last);
    }
}