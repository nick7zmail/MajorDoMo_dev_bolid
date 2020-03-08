<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
set_time_limit(0);
// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();
include_once(DIR_MODULES . 'dev_bolid/dev_bolid.class.php');
$dev_bolid_module = new dev_bolid();
$dev_bolid_module->getConfig();
$tmp = SQLSelectOne("SELECT ID FROM dev_bolid_devices LIMIT 1");
if (!$tmp['ID'])
   exit; // no devices added -- no need to run this cycle
echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$checkEvery=10;

while (1)
{
   if ((time()-$latest_check)>$checkEvery) {
     setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
     $latest_check=time();
     echo date('Y-m-d H:i:s').' Polling devices...';
     $devices=SQLSelect("SELECT * FROM dev_bolid_devices");
     $total=count($devices);
     if ($total) {
       for($i=0;$i<$total;$i++) {
         $com[$i]=$dev_bolid_module->createCom($devices[$i]);
         //опрос зон
         $dev_bolid_module->processCycle($com[$i], 'check', 'zones');
         //опрос разделов
         $dev_bolid_module->processCycle($com[$i], 'check', 'sections');
         fclose($com[$i]);
       }
     }
   }
   $opqueue=checkOperationsQueue('m_bolid');
   if($opqueue) {
     $dev_bolid_module->processCycle($com[$i], $opqueue[0]['DATANAME'], 'zonessections', $opqueue[0]['DATAVALUE']);
   }


   if (file_exists('./reboot') || IsSet($_GET['onetime']))
   {
      $db->Disconnect();
      exit;
   }
}
DebMes("Unexpected close of cycle: " . basename(__FILE__));
