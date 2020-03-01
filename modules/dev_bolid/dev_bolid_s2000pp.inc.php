<?php
$com=$device['PORT'];
exec("mode $com: BAUD=9600 DATA=8 STOP=1 to=on PARITY=none xon=off odsr=off octs=off dtr=off rts=off idsr=off");
$f = fopen($com, 'r+');
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
$this->write_com($f,$cmd,8,1);
sleep (1);
fclose($f);

?>
