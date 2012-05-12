<?php
$db = false; // ! desactiver le renommage pour debug !
$opts  = '';
$opts .= 'p:';
$args = getopt($opts);
if (!@isset($args['p']) ) { exit('p manquant'); }
$pref = trim($args['p']);
if (empty($pref) ) { exit('prefix vide !'); }

/**
 * 
 */
// $pref = 'af';
$pattern = $pref."_%03d";
$list = array(); $dirs = array();
$self = basename(__FILE__);
$exclude = array($self, 'Thumbs.db');
if ($handle = opendir('.')) {
    while (false !== ($file = readdir($handle))) {
		if (is_dir($file) ) { 
			$dirs[] = $file;
			continue;
		}
    	if (!is_file($file) ) { continue; }
        foreach ($exclude AS $ex) {
            if (false !== strpos($file, $ex) ) { continue 2; }
        }
        if ($file != "." && $file != "..") {
        	$list[] = $file;
        }
    }
    closedir($handle);
}
$i = 0;
$aR = array();
sort($list);
foreach ($list AS $f) {
    $i++;
    // $n = $pref.'_'.$i;
    $aP = pathinfo($f);
    if ($db) {
        // var_dump('infos path', $aP);
    }   
    $ext = @isset($aP['extension'])?'.'.$aP['extension']:'';
    $n = sprintf($pattern.$ext, $i);
    $aR[$i]['new'] = $n;
    $aR[$i]['old'] = $f;
    echo 'rename ' . $f . ' into ' . $n . "\n\r"; 
}
echo 'RENAME ? (O/N) ';
$line = trim(fgets(STDIN));
if ($db) { var_dump($line); }
if ('O' == $line) {
    foreach ($aR AS $l) {
            if ($db) { 
                echo 'rename ' . $l['old'] . ' into ' . $l['new'] . "\n\r"; 
            } else {
                rename($l['old'], $l['new']);
            }
    }
} else {
    exit('Annulation par user');
}
