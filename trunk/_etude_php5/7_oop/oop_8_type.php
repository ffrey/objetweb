<?php
class padre {

}

class nino extends padre {

}

function test (padre $n)
{
    echo var_dump($n);
}
$n = new nino();
test($n);

echo "===============\n\r";
function essai(MyClass $foo = null )
{
    echo 'hello';
}
essai();