<?php
$name = 'tAtIaNa';

switch (strtolower($name) ) {
    case 'angela':
        echo 'ang';
    case 'tat' . 'ia' . 'na' :
        echo 'tat';
    
    case array(1) :
        echo 'Arr';
    case 'bard':
        echo 'bar';
        break;
    default:
        echo 'default !';
}

