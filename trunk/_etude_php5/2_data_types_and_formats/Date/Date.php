<?php
// php53 C:\wamp\lib\ow\_etude_php5\2_data_types_and_formats\Date\Date.php
date_default_timezone_set('Europe/Paris');
printf('Time Zone is %s'."\n\r", date_default_timezone_get() );
$Date = new DateTime();

printf('Today is : %s'."\n\r", $Date->format('d/m/Y') );


$Future = new DateTime('2012-03-27');
printf('Future is : %s'."\n\r", $Future->format('d/m/Y') );

printf('cookie format : %s'."\n\r", $Future->format(DateTime::COOKIE) );

$Int = $Date->diff($Future);
printf('Attention : option "a" is buggy ! @see https://bugs.php.net/bug.php?id=51184&thanks=7'."\n\r");
printf('Future is in : . ' . $Int->format('%R%a days')."\n\r");
printf('Future is in %s months'."\n\r", $Int->format('%m') );

################
$period = new DatePeriod($Date,$Int,10);
printf('Toutes les dates jusqu\'a futur :'."\n\r");
foreach($period AS $d) {
	printf('date : %s'."\n\r", $d->format('d/m/Y') );
}