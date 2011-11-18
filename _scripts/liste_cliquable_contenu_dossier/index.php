<?php
$list = array(); $dirs = array();
if ($handle = opendir('.')) {
    while (false !== ($file = readdir($handle))) {
		if (is_dir($file) ) { 
			$dirs[] = $file;
			continue;
		}
    	if (!is_file($file) ) { continue; }
        if ($file != "." && $file != "..") {
        	$list[] = $file;
        }
    }
    closedir($handle);
}
sort($list);
?>
<html>
<head>
<title>Contenu</title>
</head>
<h1>Fichiers :</h1>
<?php
foreach ($list AS $file) {
	 echo sprintf('<a href="%s">%s</a><br/>'."\n", $file, $file);
}
print '<hr>';
?>
<h1>Dossiers : </h1>
<?php
foreach ($dirs AS $dir) {
	echo sprintf('<a href="%s">%s</a><br/>'."\n", $dir, $dir);
}
print '<hr>';
?>
