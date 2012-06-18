<?php

// $fp = fopen("php://stdin", "r");
$in = '';
while($in != "quit") {
    echo "php> ";
    $in=trim(fgets(STDIN));
    if ($in == "quit") { break; }
    eval ($in);
    echo "\n";
}