<?php
/**
 * easily prints debug info (any type !) : replaces prints AND var_dumps ! => easier to clean once debugged !
 *
 * @param mixed $data
 */
function db($data)
{
  // 26 11 08 : under development (_dev)

  $dump = '';
  if ( is_string($data) OR is_numeric($data) ) {
    $dump = '<h4>' . $data . '</h4>';
  } elseif ( is_bool($data) ) {
    $val = (true === $data)? 'True' : 'False';
    $dump = '<h5>Boolean ' . $val . '</h5>';
  } elseif ( is_array($data) ) {
    $dump = '<h5>' . print_r($data, true) . '</h5>';
  } else {
    print 'DUMP : ';
    print '<pre>';
    var_dump($data);
    print '</pre>';
    return;
  }
  print $dump;
}