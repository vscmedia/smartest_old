<?php

/**
 * Contains the Settings module for website
 *
 * PHP versions 5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Eddie Tejeda <eddie@visudo.com>
 */


class Settings extends SmartestSystemApplication{

	function startPage(){
		
	}
	
	function checkForUpdates(){
		
		// latest
		$contents = file_get_contents("http://update.visudo.net/smartest");
		$unserializer = new XML_Unserializer(); 
		$status = $unserializer->unserialize($contents); 		
		
		if (PEAR::isError($status)) { 
			 die($status->getMessage()); 
		}
		
		$latest = $unserializer->getUnserializedData();

		//current
		$contents = file_get_contents("System/CoreInfo/package.xml");
		$unserializer = new XML_Unserializer(); 
		$status = $unserializer->unserialize($contents); 		
		
		if (PEAR::isError($status)) { 
			 die($status->getMessage()); 
		}
		
		$current = $unserializer->getUnserializedData();
		
		$release = false; 
		
		if($latest['release']['version'] > $current['release']['version']){
			$release = $latest;
		}else if($latest['release']['version'] < $current['release']['version']){
			$release = "downgrade";
		}
		
		return (array("release"=>$release,"settings"=>$this->manager->getSettings())); 
	}

	function updateGeneral($get, $post){
    
		$post = array_filter($post, array($this->manager, "filterSubmit"));
		return $this->manager->setSettings($post);
    
	}

	/* function cartSettings(){
		return $this->manager->getSettings();    
	} */
  
    /* function showModels($get){
		
		$user_id = $get['user_id'];
		$sql = "SELECT * FROM ItemClasses WHERE itemclass_userid = '$user_id'";
		$models = $this->database->queryToArray($sql);
		$username = $this->database->specificQuery("username", "user_id", $user_id, "Users");
		return array("models" =>$models, "username"=>$username, "itemClassCount"=>count($models));
		
	}

	function showPages($get){

		$user_id = $get['user_id'];
		$sql = "SELECT * FROM Pages WHERE page_createdby_userid = '$user_id'";
		$pages = $this->database->queryToArray($sql);
		$username = $this->database->specificQuery("username", "user_id", $user_id, "Users");
		return array("pages" =>$pages, "username"=>$username, "pageCount"=>count($pages));
	} */

}