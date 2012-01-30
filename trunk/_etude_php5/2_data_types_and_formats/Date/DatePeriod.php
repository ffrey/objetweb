<?php
// php53 C:\wamp\lib\ow\_etude_php5\2_data_types_and_formats\Date\DatePeriod.php
date_default_timezone_set('Europe/Paris');
printf('Time Zone is %s'."\n\r", date_default_timezone_get() );
echo 'Repetition : tous les 3 jours du 01/03/2012 exclu au 01/06/2012'."\n\r"; 
$Start = new DateTime('2012-03-01');
$Int   = new DateInterval('P3D');
$End  = new DateTime('2012-06-01');

$period = new DatePeriod($Start, $Int, $End, 1);
foreach ($period AS $D) {
	printf('Date : %s'."\r\n", $D->format('\l\e d/m/Y') );
}