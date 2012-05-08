<?php
interface int1 {
    public function hello();
}

interface int2 {
    public function hello();
}

class Essai implements int1, int2 
{
    public function hello() 
    {
        echo "hello\n\r";
    }
    public function bonjour()
    {
        echo "bonjour\n\r";
    }
}

$t = new Essai();