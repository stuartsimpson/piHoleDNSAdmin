<?php
$api = true;
header('Content-type: application/json');
require("scripts/pi-hole/php/password.php");
require("scripts/pi-hole/php/auth.php");
check_cors();

// Set maximum execution time to 10 minutes
ini_set("max_execution_time","600");

$data = array("message"=>'api_locationDNS.php executing');

function getDNSFileName(){
  $dnsFile = '';
  $localDNSconfig = fopen("/etc/dnsmasq.d/02-lan.conf", "r");
  if($localDNSconfig){
    $line = fgets($localDNSconfig);
    $list = preg_split('/\=+/', $line, -1, PREG_SPLIT_NO_EMPTY);
    $dnsFile = preg_replace('/\s/', '', $list[1]);
  }
  if($dnsFile){ 
    return($dnsFile);
  } else {
    return(null);
  }
}

function saveEntry($ipAddress, $fqdn, $name){
  $dnsFile = getDNSFileName();

  try{
    $fp = @fopen($dnsFile, "a");
    if ($fp) {
      fwrite($fp, $ipAddress . "\t" . $fqdn . "\t" . $name);
      fclose($fp);
    }
  } catch( Exception $e){
    $data = array_merge($data,  array( 'error'=>$e->getMessage()));
  }
}

function deleteEntry($fqdn){
  $dnsFile = getDNSFileName();
  $newDNSFile = array();

  try{
    $fp = @fopen($dnsFile, "r");
    while(! feof($fp)){
      $line = fgets($fp);
      $entry = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
      if(isset($entry[0])){
        if($fqdn!==$entry[1]){
          array_push($newDNSFile, $line );
        }
      }
    }

    $fp = @fopen($dnsFile, "w");
    foreach($newDNSFile as $line){
      fwrite($fp, $line);
    }
    fclose($fp);
  } catch( Exception $e){
    $data = array_merge($data,  array( 'error'=>$e->getMessage()));
  }
}

function loadDNSFile(){
  $dnsFile = getDNSFileName();

	$localDNS = array();
  $fp = fopen($dnsFile, "r");

	while(! feof($fp)){
    $line = fgets($fp);
    $entry = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
    if(isset($entry[0])){
      $ipAddress = $entry[0];
      $fqdn = $entry[1];
      $name = $entry[2];
      array_push($localDNS, array( 'ipAddress'=>$ipAddress, 'fqdn'=>$fqdn, 'name'=>$name));
    }
  }

  return(array('network'=> $localDNS));
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $auth){
  $data = array("service"=>$_POST["service"]);
  $service=$_POST["service"];
  switch($service):
    case 'saveDNSEntry':
      $ipAddress = $_POST['ipAddress'];
      $fqdn = $_POST['fqdn'];
      $name = $_POST['name'];
      if($ipAddress && $fqdn && $name){
        saveEntry($ipAddress, $fqdn, $name);
      }
      break;
      case 'deleteDNSEntry':
        $fqdn = $_POST['fqdn'];
        if($fqdn){
          $data = array_merge($data, array('fqdnToDelete'=>$fqdn));
          deleteEntry($fqdn);
        }
        break;
      case 'restartDNS':
      shell_exec("sudo pihole restartdns");
      $data = array_merge($data, array('success' => true));
    break;
  endswitch;
} else if($_SERVER['REQUEST_METHOD'] === 'GET' && $auth){
  $data = array_merge($data,  loadDNSFile());
}


echo json_encode($data);

