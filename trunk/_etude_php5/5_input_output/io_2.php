<?php
$Dir = new RecursiveDirectoryIterator('glob://C:\Documents and Settings\Main\Mes documents\downloads\*.dll');
foreach($Dir as $f) {
    printf("%s: %.1FK\n", $f->getFilename(), $f->getSize()/1024);
}
echo "==========\n";
$globs = glob('C:\Documents and Settings\Main\Mes documents\downloads\*.dll');
foreach ($globs AS $g) {
    printf("%s \n\r", $g);
}

echo "==============\n";
$Directory = new RecursiveDirectoryIterator('C:\Documents and Settings\Main\Mes documents\downloads');
$Iterator = new RecursiveIteratorIterator($Directory);
foreach ($Iterator AS $v) {
    echo $v."\n";
}
$Regex = new RegexIterator($Iterator, '/^.+\.7z$/i', RecursiveRegexIterator::GET_MATCH);
foreach ($Regex AS $v) {
    var_dump($v);
}