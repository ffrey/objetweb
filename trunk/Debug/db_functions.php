<?php
if (!class_exists('myUtil') ) { require_once('C:\EasyPHP_3_0\php\perso_php\myUtil.class.php'); }
/**
 * easily prints debug info (any type !) : replaces prints AND var_dumps ! => easier to clean once debugged !
 *
 * @param mixed $data
 */
function db($data)
{
  // under dev => _dev/
  return myUtil::db($data);
  /*
  if ( is_string($data) OR is_numeric($data) ) {
  $dump = '<h4>' . $data . '</h4>';
  } elseif ( is_bool($data) ) {
  $val = (true === $data)? 'True' : 'False';
  $dump = '<h5>Boolean ' . $val . '</h5>';
  } elseif ( is_array($data) ) {
  $dump = '<h5><pre>' . print_r($data, true) . '</pre></h5>';
  } else {
  print 'DUMP : ';
  print '<pre>';
  var_dump($data);
  print '</pre>';
  return;
  }
  print $dump;
  */
}