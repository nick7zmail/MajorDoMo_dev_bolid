<?php
/**
* Bolid
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 17:02:54 [Feb 19, 2019])
*/
//
//
class dev_bolid extends module {
/**
* dev_bolid
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="dev_bolid";
  $this->title="Bolid";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_bolid_devices' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_bolid_devices') {
   $this->search_dev_bolid_devices($out);
  }
  if ($this->view_mode=='edit_dev_bolid_devices') {
   $this->edit_dev_bolid_devices($out, $this->id);
  }
  if ($this->view_mode=='delete_dev_bolid_devices') {
   $this->delete_dev_bolid_devices($this->id);
   $this->redirect("?data_source=dev_bolid_devices");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_bolid_data') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_bolid_data') {
   $this->search_dev_bolid_data($out);
  }
  if ($this->view_mode=='edit_dev_bolid_data') {
   $this->edit_dev_bolid_data($out, $this->id);
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* dev_bolid_devices search
*
* @access public
*/
 function search_dev_bolid_devices(&$out) {
  require(DIR_MODULES.$this->name.'/dev_bolid_devices_search.inc.php');
 }
/**
* dev_bolid_devices edit/add
*
* @access public
*/
 function edit_dev_bolid_devices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_bolid_devices_edit.inc.php');
 }
/**
* dev_bolid_devices delete record
*
* @access public
*/
 function delete_dev_bolid_devices($id) {
  $rec=SQLSelectOne("SELECT * FROM dev_bolid_devices WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM dev_bolid_devices WHERE ID='".$rec['ID']."'");
 }
/**
* dev_bolid_data search
*
* @access public
*/
 function search_dev_bolid_data(&$out) {
  require(DIR_MODULES.$this->name.'/dev_bolid_data_search.inc.php');
 }
/**
* dev_bolid_data edit/add
*
* @access public
*/
 function edit_dev_bolid_data(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_bolid_data_edit.inc.php');
 }
 function propertySetHandle($object, $property, $value) {
   $table='dev_bolid_data';
   $properties=SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
		$device=SQLSelectOne("SELECT * FROM dev_bolid_devices WHERE ID='".$properties[$i]['DEVICE_ID']."'");
		if($device['TYPE']=='s2000sp1') {
			require(DIR_MODULES.$this->name.'/dev_bolid_s2000sp1.inc.php');
		} elseif($device['TYPE']=='s2000pp') {
			require(DIR_MODULES.$this->name.'/dev_bolid_s2000pp.inc.php');
		}
    }
   }
 }
 function processCycle($com, $act, $type, $data = '') {
   if($act=='check') {
     if($type=='zones') {
       echo date('Y-m-d H:i:s').' Polling zones...'.PHP_EOL;
       $properties=SQLSelect("SELECT * FROM dev_bolid_data WHERE TYPE = 'zones'");
       $total=count($properties);
       if ($total) {
        for($i=0;$i<$total;$i++) {
          $cmd="\x01";
          $cmd.="\x03";
          $cmd.="\x9C";
          $cmd.=chr(0x40+$properties[$i]['TYPE_NUM']-1);
          $cmd.="\x00";
          $cmd.="\x01";
          $fresult=$this->write_com($com,$cmd,7,1);
          if ( ( ord($fresult[3]) == 109) ) {
              $state=0;
          } elseif ( ( ord($fresult[3]) == 24) ) {
              $state=1;
          } else {
              registerEvent('bolid/alarms/'.ord($fresult[3]), array('zone'=>$properties[$i]['TYPE_NUM']));
              $state=NULL;
          }
          if ( ( ord($fresult[4]) != 0) && ( ord($fresult[4]) != 47) ) {
              registerEvent('bolid/alarms/'.ord($fresult[4]), array('zone'=>$properties[$i]['TYPE_NUM']));
          }

          debmes('[get] '.$properties[$i]['TYPE_NUM'].' zone state:'.$state, 'bolid');
          if(isset($properties[$i]['LINKED_OBJECT']) && isset($properties[$i]['LINKED_PROPERTY']) && $state!==NULL) {
              sg($properties[$i]['LINKED_OBJECT'].'.'.$properties[$i]['LINKED_PROPERTY'], $state, array($this->name => '0'));
          }
        }
       }
     } elseif ($type=='sections') {
       $properties=SQLSelect("SELECT * FROM dev_bolid_data WHERE TYPE = 'sections'");
       $total=count($properties);
       if ($total) {
        for($i=0;$i<$total;$i++) {
          echo date('Y-m-d H:i:s').' Polling sections...'.PHP_EOL;
          $cmd="\x01";
          $cmd.="\x03";
          $cmd.="\xAC";
          $cmd.=chr(0x40+$properties[$i]['TYPE_NUM']-1);
          $cmd.="\x00";
          $cmd.="\x01";
          $fresult=$this->write_com($com,$cmd,7,1);
          if ( (ord($fresult[4]) == 0) && ( ord($fresult[3]) == 109) ) {
              $state=0;
          } elseif ( (ord($fresult[4]) == 0) && ( ord($fresult[3]) == 24) ) {
              $state=1;
          } else {$state=NULL;}
            debmes('[get] '.$properties[$i]['TYPE_NUM'].' sect state:'.$state, 'bolid');
          if(isset($properties[$i]['LINKED_OBJECT']) && isset($properties[$i]['LINKED_PROPERTY']) && $state!==NULL) {
              sg($properties[$i]['LINKED_OBJECT'].'.'.$properties[$i]['LINKED_PROPERTY'], $state, array($this->name => '0'));
          }
        }
       }
     } elseif ($type=='relays') {
       $properties=SQLSelect("SELECT * FROM dev_bolid_data WHERE TYPE = 'relays'");
       $total=count($properties);
       if ($total) {
        for($i=0;$i<$total;$i++) {
          echo date('Y-m-d H:i:s').' Polling relays...'.PHP_EOL;
          $cmd="\x01";
          $cmd.="\x01";
          $cmd.="\x27";
          $cmd.=chr(0x10+$properties[$i]['TYPE_NUM']-1);
          $cmd.="\x00";
          $cmd.="\x01";
          $fresult=$this->write_com($com,$cmd,6,1);
          if ( (ord($fresult[3]) == 0) ) {
              $state=0;
          } elseif ( ( ord($fresult[3]) == 1) ) {
              $state=1;
          } else {$state=NULL;}
            debmes('[get] '.$properties[$i]['TYPE_NUM'].' rel state:'.$state, 'bolid');
          if(isset($properties[$i]['LINKED_OBJECT']) && isset($properties[$i]['LINKED_PROPERTY']) && $state!==NULL) {
              sg($properties[$i]['LINKED_OBJECT'].'.'.$properties[$i]['LINKED_PROPERTY'], $state, array($this->name => '0'));
          }
        }
       }
     }
   } elseif ($act=='set') {
     debmes('[op] set '.$data, 'bolid');
     if ($type=='zonessections') {
       $params=json_decode($data, TRUE);
       debmes('[set] '.$params['TYPE_NUM'].' sect state:'.$params['VALUE'], 'bolid');
       $cmd="\x01";
       $cmd.="\x06";
       if ($params['TYPE']=='zones') {
         $cmd.="\x9C";
       } elseif($params['TYPE']=='sections') {
         $cmd.="\xAC";
       }
       $cmd.=chr(0x40+$params['TYPE_NUM']-1);
      $cmd.="\x00";
       if($params['VALUE']==1) {
         $cmd.="\x18";
       } else {
         $cmd.="\x6D";
       }
       $fresult=$this->write_com($com,$cmd,8,1);
     }
   }
 }

 function createCom($device) {
   $com=$device['PORT'];
   exec("mode $com: BAUD=9600 DATA=8 STOP=1 to=on PARITY=none xon=off odsr=off octs=off dtr=off rts=off idsr=off");
   $f = fopen($com, 'r+');
   return $f;
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }

function write_com($f_file,$cmd_bit,$size_read,$ok)
{
  //debmes('[---] '.string($cmd_bit), 'bolid');
	//for($i=0; $i < count($cmd_bit); $i++) {$c .= chr($cmd_bit[$i]);}
	fwrite($f_file,$cmd_bit.$this->crc16($cmd_bit));
	$fresult = fread($f_file,$size_read);
  //debmes('[+++] '. string($fresult), 'bolid');
  return $fresult;
}

function crc16($data)
{
  $crc = 0xFFFF;
  for ($i = 0; $i < strlen($data); $i++)
  {
    $crc ^=ord($data[$i]);
      for ($j = 8; $j !=0; $j--)
    {
      if (($crc & 0x0001) !=0)
      {
        $crc >>= 1;
        $crc ^= 0xA001;
      }
      else
      $crc >>= 1;
    }
  }
  $highCrc=floor($crc/256);
  $lowCrc=($crc-$highCrc*256);
  return chr($lowCrc).chr($highCrc);
}
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS dev_bolid_devices');
  SQLExec('DROP TABLE IF EXISTS dev_bolid_data');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
dev_bolid_devices -
dev_bolid_data -
*/
  $data = <<<EOD
 dev_bolid_devices: ID int(10) unsigned NOT NULL auto_increment
 dev_bolid_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_bolid_devices: TYPE varchar(10) NOT NULL DEFAULT ''
 dev_bolid_devices: PORT varchar(255) NOT NULL DEFAULT ''
 dev_bolid_data: ID int(10) unsigned NOT NULL auto_increment
 dev_bolid_data: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_bolid_data: VALUE varchar(255) NOT NULL DEFAULT ''
 dev_bolid_data: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 dev_bolid_data: TYPE varchar(10) NOT NULL DEFAULT ''
 dev_bolid_data: TYPE_NUM int(10) NOT NULL DEFAULT '1'
 dev_bolid_data: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 dev_bolid_data: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgRmViIDE5LCAyMDE5IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
