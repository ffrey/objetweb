#!/usr/bin/env php
<?php
/**
 * ce script permet de supprimer tous les fichiers correspondant a un pattern de type glob
 * la recherche part du dossier courant inclus + les sous-dossiers
 * une confirmation est demandee apres listing des fichiers
 *
 * @uses sfFinder (part of standard symfony1.4 lib)
 */
$db = false;
$finder = 'C:\wamp\lib\sf14\lib\util\sfFinder.class.php';
if (!is_file($finder) ) {
	print 'ce script a besoin de la classe sfFinder pour fonctionner !'."\n\r";
	return 0;
}
require_once $finder;
do {
	print 'please enter a glob pattern for files to delete'."\n\r";
	$pattern = fgets(STDIN);
	$pattern = trim($pattern);
	$pattern = (string) $pattern;
	$empty = empty($pattern);
	if ($db) {
		print 'empty ?'."\n\r";
		var_dump($empty);
	}
} while ($empty);
print 'glob pattern : ' . $pattern ."\n\r";

$files = sfFinder::type('file')->name($pattern)->in('.');
$found = (is_array($files) AND count($files) );
if ($db) {
	var_dump('files', $files, 'found', $found);
}
if(!$found) {
	print 'NO FILES FOUND';
	return 0;
}
foreach ($files AS $f) {
	print 'found '.$f."\n\r";
}
print 'ready to delete ? (yes or no)'."\n\r";
$confirm = fgets(STDIN);
if ('yes' != trim($confirm) ) {
	print 'THE END'."\n";
	return 0;
}
print 'DELETING...'."\n\r";
$erreurs = array(); $nbDeleted = 0;
foreach ($files AS $file) {
	$File = new SplFileInfo($file);
	if (!$File->isFile() ) {
		$erreurs[] = 'File not found : ' . $File->getFilename();
		continue;
	}
	if ($db) {
		print 'deleting ' . $File->getFilename() . "\n\r";
	}
	// $ok = unlink($file);
	$ok = false;
	if (!$ok) {
		$erreurs[] = 'echec suppression ' . $file;
	}
	$nbDeleted++;
}
if (count($erreurs) ) {
	print 'ERREURS ! '."\n\r";
	foreach ($erreurs AS $err) {
		print $err . "\n\r";
	}
} else {
	printf('%s fichiers supprimes'."\n\r", $nbDeleted);
}
return 0;