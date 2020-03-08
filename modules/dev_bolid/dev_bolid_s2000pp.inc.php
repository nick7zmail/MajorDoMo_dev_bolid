<?php
//addToOperationsQueue('m_bolid', 'set', '{"TYPE":"'.$properties[$i]['TYPE'].'", "TYPE_NUM":'.$properties[$i]['TYPE_NUM'].', "VALUE":'.$value.'}', false);
$f=$this->createCom($device);
if($properties[$i]['TYPE']=='zones' || $properties[$i]['TYPE']=='sections') {
  $cmd="\x01";
  $cmd.="\x06";
  if ($properties[$i]['TYPE']=='zones') {
    $cmd.="\x9C";
  } elseif($properties[$i]['TYPE']=='sections') {
    $cmd.="\xAC";
  }
  $cmd.=chr(0x40+$properties[$i]['TYPE_NUM']-1);

  if($value==1) {
    $cmd.="\x00";
    $cmd.="\x18";
  } else {
    $cmd.="\x00";
    $cmd.="\x6D";
  }
} elseif($properties[$i]['TYPE']=='relays') {
  $cmd="\x01";
  $cmd.="\x05";
  $cmd.="\x27";
  $cmd.=chr(0x10+$properties[$i]['TYPE_NUM']-1);
  if($value==1) {
    $cmd.="\xFF";
    $cmd.="\xFF";
  } else {
    $cmd.="\x00";
    $cmd.="\x00";
  }
}
$this->write_com($f,$cmd,8,1);
sleep (1);
fclose($f);

?>
