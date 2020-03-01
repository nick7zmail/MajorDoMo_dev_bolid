<?php
$com=$device['PORT'];
exec("mode $com: BAUD=9600 DATA=8 STOP=1 to=on PARITY=none xon=off odsr=off octs=off dtr=off rts=off idsr=off");
$f = fopen($com, 'r+');
$pbyte = array("ON" => array(0x4B,0xC3,0x4C,0xCA), "OFF" => array(0xA9,0x21,0xAE,0x28));
$ch=substr($properties[$i]['TITLE'], -1);
if($value) {
write_com($f,array(0x7f,0x08,0x00,0x41,dechex($ch),0x00,0x00,0x01,$pbyte["ON"][$i]),6,1); //Включение порта
write_com($f,array(0x7f,0x06,0x00,0x17,0x00,0x00,0x10),6,0); // Enter Update
} else {
write_com($f,array(0x7f,0x08,0x00,0x41,dechex($ch),0x00,0x00,0x02,$pbyte["OFF"][$i]),6,1); //Выключение порта
write_com($f,array(0x7f,0x06,0x00,0x17,0x00,0x00,0x10),6,0); // Enter Update
}
sleep (1);
fclose($f);

/*function write_com($f_file,$cmd_bit,$size_read,$ok)
{
//С2000-СП1 response command  = array(0x7F,0x05,0x42,0x00,0x00,0xFD)
//С2000-СП1 response enter    = array(0x7F,0x05,0x18,0x00,0x00,0xEC)
	for($i=0; $i < count($cmd_bit); $i++) {$c .= chr($cmd_bit[$i]);}
	fwrite($f_file,$c);
	$fresult = fread($f_file,$size_read);
	switch ($ok)
	{
		case 0:
			if ( ( ord($fresult[2]) == 0x18) && ( ord($fresult[5]) == 0xEC) ){echo "\n\nEnter OK\n\n";}
		case 1:
			if ( ( ord($fresult[2]) == 0x42) && ( ord($fresult[5]) == 0xFD) ) {echo "\n\nCommand OK\n\n";}
	}
}*/
?>
