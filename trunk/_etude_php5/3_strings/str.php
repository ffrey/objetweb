<?php
// php53 C:\wamp\lib\ow\_etude_php5\3_strings\str.php
$nowDoc = <<<'id'
Quelle importance tout cela ?
id;

$other = <<<id
Quelle importance tout ceci ?
id;

var_dump($nowDoc, $other);
$s = similar_text($nowDoc, $other, $percent);
printf('! escape char in printf is %% !!!'."\n\r");
printf('similar_text : number of common chars is %s / percentage of similarity is %s%%'."\n\r",  $s, $percent);


$l = levenshtein($nowDoc, $other);
printf('levenshtein : nb of chars to modify/add/delete is %s'."\n\r", $l);

print '####################'.PHP_EOL;
$tests = array(
array('makao', 'macao'),
array('drink', 'trink'),
array('drink', 'mrink'),
);
foreach ($tests AS $t) {
	var_dump($t);
	printf('soundex : %s / %s'."\n\r"  , soundex($t[0])  , soundex($t[1]) );
	printf('metaphone : %s / %s'."\n\r", metaphone($t[0]), metaphone($t[1]) );
}
