<h3>Sans definition de namespace : prefixer les appels par le namespace !</h3>
<?php
require 'namespaces/lib.php';
//phpinfo();

$a = new ow\lib\Text();
print $a->show('go');