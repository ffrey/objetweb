<?php
function renommer(&$obj)
{
    $obj->name = 'changed from within function = passed by ref !';
}
class padre 
{
    public $name = 'Raoul';
}
$p = new padre();
$p->name = 'nouveau nom';
$pp = $p;
// $pp = clone($p);
var_dump('avt', $p->name);
renommer($pp);
var_dump('apres', $p->name);

echo "===============\n\r";
$a = 'hello';
function renommerVar(&$v)
{
    $v = 'changed from within function = passed by ref !';
}
var_dump('avt', $a);
renommerVar($a);
var_dump('apres', $a);
