<?php
$db = false; // ! desactiver le renommage pour debug !
$opts  = '';
$opts .= 'p:';
$opts .= 'f::';
$args = getopt($opts);
if (!@isset($args['p']) ) { exit('p manquant'); }
$from = 0;
if (@isset($args['f']) )  { $from = $args['f']; }
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
$i = $from;
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
    $err = array();
    foreach ($aR AS $l) {
            $msg = 'rename ' . $l['old'] . ' into ' . $l['new'] . "\n\r";
            if ($db) { 
                echo $msg;
            } else {
                $ok = rename($l['old'], $l['new']);
                if (!$ok) {
                    $err[] = 'ECHEC : '. $msg;
                }   
            }
    }
    if (count($err) ) {
        echo 'ERREURS :'."\n\r";
        foreach ($err AS $m) {
            echo $m."\n\r";
        }
    }
} else {
    exit('Annulation par user');
}
