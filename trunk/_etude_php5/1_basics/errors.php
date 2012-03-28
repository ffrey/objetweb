<?php
$test = array(E_CORE_WARNING | E_PARSE, 36);
        // 32 | 4 => 100000 | 000100 => 100100 = 36
        var_dump($test);
$test = array(~E_CORE_WARNING, 011111, 31);
// 1024 512 256 128 64 32 16 8 4 2 1
// 11111111111