<?php
/*** console ***/
/**
 * cmd(<msg> [, true]) : prints msg
 * cmd(<msg>, false)   : does not print
 * wait(<msg)            : prints AND waits for user to press enter
 * STEP(<msg>)          : prints msg in stressed format
 */
/**
 * @param string $msg
 * @param bool $stop : true => pause the script until Enter is pressed
 */
function cmd($msg, $show = true, $stop = false)
{
  // BUG : printing out of messages only occurs after ALL fgets(STDIN) have been made !
  
  do {
    if (!$show) break;
    print("\n\r");
    if (is_string($msg) )  { print($msg); }
	elseif (is_object($msg) )  { var_dump($msg); }
    else { print $s = print_r($msg, true); }
    print("\n\r");
  } while (false);
  if ($stop) { fgets(STDIN); }
  
}
function wait($msg)
{
	if (false === strpos($msg, '?') ) {
		$msg .= ' ?';
	}
  cmd($msg, true, true);
}
function STEP($msg)
{
    cmd('*** ' . ucfirst(strtolower($msg) ) . ' ***');
}