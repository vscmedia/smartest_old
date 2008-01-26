<?php
/**
 * Implements the statitics class
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

require_once 'XML/Serializer.php'; 
require_once 'XML/Unserializer.php'; //TODO: Maybe use SimpleXML instead?

class StatisticsManager{
  
  var $database = null;
  function StatisticsManager(){
    $this->database = $_SESSION['database'];
  }


  
  function request(){
    
    if($_SERVER['REQUEST_URI'] == "/favicon.ico" || $_SERVER['REQUEST_URI'] == "/robot.txt"){
      return;
    }
    
    if($_SESSION['user_tracked'] == true){
      //we track user only at entry. 
      return;
    }
    else{
      $_SESSION['user_tracked'] = true;
    }
    
    $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : "";
    $sessionid = session_id();
    $date = date("Y-m-d h:M:s");
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
    $request = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
    $country = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";

    $sql = "INSERT INTO  `Visitor` (   `visitor_date` , `visitor_country`, `visitor_referer` ,  `visitor_hostname` ,  `visitor_request` ,  `visitor_status` ,  `visitor_session_id` ,  `visitor_user_agent` ,  `visitor_username` ) 
                             VALUES(  ".mktime().",     '$country',             '$referer',      '$hostname',             '$request',     '".$_SERVER['REDIRECT_STATUS']."', '$sessionid', '".$_SERVER['HTTP_USER_AGENT']."', '$username')";
    $this->database->query($sql);
  }

  //http://dknss.mirrors.phpclasses.org/browse/file/1729.html
  function detectLocation(){
    require_once("System/NetGeo.class.php");
  
    $netgeo=new NetGeo();
    $ip=GetEnv("REMOTE_ADDR");
    if($netgeo->GetAddressLocation($ip,$location)){
      return $location["COUNTRY"];
    }
    else{
      return false;
    }
  }
  
}
?>
