<?php
$str = "<p>First Paragraph<ul><li>First</li><li>Second</li></ul></p><p>Second Paragraph</p>";
$pattern = "/<.*>/U";
$matches = array();
$positive = preg_match_all($pattern, $str, $matches);
echo $positive ? "MATCH" : "NO MATCH";
echo "\n";
var_dump($matches);