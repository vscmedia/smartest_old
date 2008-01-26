<?php

require_once 'PEAR.php';
require_once 'XML/Unserializer.php';
require_once 'DB.php';

define("UNSUPPORTED_QUERY", false);

class SmartestDatabase{

	var $username;
	var $password;
	var $host;
	var $database;
	var $phptype;
	var $peardb;
	var $dsn;
	var $lastQuery;
	var $queryHistory;
	var $result;

	function Database($configFile){
		
		if(!file_exists($configFile)){
			die('XmlData class constructor did not recieve a file');
		}
		
		// load xml
		$option = array('complexType' => 'array', 'parseAttributes' => TRUE);
    	$unserialized = new XML_Unserializer($option);
    	$result = $unserialized->unserialize($configFile, true);
    	
    	if (PEAR::isError($result)) {
    		die($result->getMessage());
    	}
    	
    	// load contents from xml file
    	$data = $unserialized->getUnserializedData();
    	
    	$this->username = $data['username'];
    	$this->password = $data['password'];
    	$this->host     = $data['host'];
    	$this->database = $data['database'];
    	$this->phptype  = $data['phptype'];
    	$this->dsn      = $this->phptype."://".$this->username.":".$this->password."@".$this->host."/".$this->database;
		
		$this->lastQuery = "no queries made yet";
		$this->queryHistory = array();
	}
	
	function connect(){
		$db  = new DB();
    	$this->peardb = $db->connect($this->dsn);
		$this->peardb->setFetchMode(DB_FETCHMODE_ASSOC);
	}
	
	function recordQuery($querystring){
		
		if(PEAR::isError($this->peardb)){
			$error = "ERROR: ".$this->peardb->getMessage();
		}else{
			$error = "Query OK";
		}
		$this->lastQuery = $querystring;
		array_push($this->queryHistory, $querystring."; ".$error);
	}
	
	function queryToArray($querystring){
		
		$result = $this->peardb->query($querystring);
		if(!$this->peardb->isError($result)){
			
			$data = array();
			
			while ($row =& $result->fetchRow()){
				$data[] = $row;
			}
			
			$this->recordQuery($querystring);
			return $data;
			
		}else{
			$this->recordQuery($querystring);
			return array();
		}
		
	}
	
	function getQueryType($querystring){
		if(preg_match( '/^(\w+?)\s/i', $querystring, $match)){
			return strtoupper(trim($match[0]));
		}else{
			return false;
		}
	}
	
	function query(){
		switch (strtoupper($this->getQueryType($querystring))){
			
			case 'UPDATE': // update and delete queries return number of affected rows.
			case 'DELETE':
				
				if($result = $this->rawQuery($querystring)){
					// return @mysql_affected_rows($result);
				}
				break;

			case 'INSERT': // do insert query and return id of last newly inserted row.
				
				if($result = $this->rawQuery($querystring)){
					// return @mysql_insert_id($this->dblink);
				}
				
				break;

			case 'SELECT': // select query returns data as array.
				
				if($data = $this->queryToArray($querystring)){
					return $data;
				}else{
					return false;
				}
				
				break;

			default:
				return UNSUPPORTED_QUERY;
		}
	}
	
	function rawQuery(){
		
	}
	
	function howMany($querystring){
		return count($this->queryToArray($querystring));
	}
	
	function specificQuery(){
		
	}
	
	function getDebugInfo(){
		return $this->queryHistory;
	}

}

?>