<?php

/**
 * Implements a mysql abstraction layer
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Marcus Gilroy-Ware <marcus@visudo.com>
 */

if(!defined("UNSUPPORTED_QUERY")){
	define("UNSUPPORTED_QUERY", false);
}

class SmartestMysql implements SmartestDataAccessClass{

	protected $dblink;
	public $lastQuery;
	protected $queryHistory;
	protected $id;
	protected $databaseName;
	protected $options = array();
	// protected $remember_password;
  
	function __construct($server, $username, $database, $password="", $remember_password=true){
	    
	    $this->options['server'] = $server;
	    $this->options['username'] = $username;
	    $this->options['database'] = $database;
	    $this->options['remember_password'] = $remember_password;
	    
	    if($remember_password){
	        $this->options['password'] = $password;
        }
        
        if($password){
	        $this->options['password_needed'] = true;
        }else{
            $this->options['password_needed'] = false;
        }
	    
		if($this->dblink = @mysql_connect($server, $username, $password)){
			// @mysql_set_charset("UTF-8", $this->dblink);
			$this->queryHistory = array();
			$this->rawQuery("SET NAMES 'utf8'");
			mysql_select_db($database, $this->dblink);
			$this->databaseName = $database;
			$this->lastQuery = "no queries made yet";
			
		}else{
			throw new SmartestException("Could not connect to MySQL", SM_ERROR_DB);
		}
	}
	
	protected function reconnect(){
	    if($this->options['password_needed'] && isset($this->options['password'])){
	        if($this->dblink = @mysql_connect($this->options['server'], $this->options['username'], $this->options['password'], true)){
    			// @mysql_set_charset("UTF-8", $this->dblink);
    			mysql_query("SET NAMES 'UTF8'", $this->dblink);
    			@mysql_select_db($this->databaseName, $this->dblink);
    			return true;
    		}else{
    			return false;
    		}
	    }else{
	        // password is needed to connect, but not supplied
	        return false;
	    }
	}
	
	public function getTables(){
		
		$sql = "SHOW TABLES FROM ".$this->databaseName;
		// $result = mysql_query($sql);

		$tables = $this->queryToArray($sql);
		
		$table_names = array();
		
		foreach($tables as $tr){
			$table_names[] = $tr["Tables_in_".$this->databaseName];
		}
		
		return $table_names;
		
	}
	
	public function getColumns($table){
		
		$sql = "SHOW COLUMNS FROM ".$table;
		// $result = mysql_query($sql);

		$columns = $this->queryToArray($sql);
		
		return $columns;
		
	}
	
	public function getColumnNames($table){
		
		$sql = "SHOW COLUMNS FROM ".$table;
		// $result = mysql_query($sql);

		$columns = $this->queryToArray($sql);
		
		$names = array();
		
		foreach($columns as $column){
			$names[] = $column['Field'];
		}
		
		return $names;
		
	}
	
	public function rawQuery($querystring, $file='', $line=''){
	    if(!$this->dblink && !$this->reconnect()){
	        throw new SmartestException("Lost connection to to MySQL database", SM_ERROR_DB);
        }else{
	        $result = mysql_query($querystring, $this->dblink);
    	    // var_dump($result);
    		if($result){
    			$this->recordQuery($querystring);
    			$this->id = mysql_insert_id($this->dblink);
    			return $result;
    		}else{
    			$this->recordQuery($querystring);
    			return false;
    		}
	    }
	}
	
	protected function getInsertId(){
		return $this->id;
	}
  
	public function howMany($querystring, $file='', $line=''){
	    
	    if(!$this->dblink && !$this->reconnect()){
	    
	        throw new SmartestException("Lost connection to to MySQL database", SM_ERROR_DB);
        
        }else{
	    
		    if($result = @mysql_query($querystring, $this->dblink)){
			    $cardinality = @mysql_num_rows($result);
			    $this->recordQuery($querystring);
			    return $cardinality;
		    }else{
			    return "0";
		    }
		
	    }
	}
	
	public function queryToArray($querystring, $file='', $line=''){
	    
		if(!$this->dblink && !$this->reconnect()){
	    
	        throw new SmartestException("Lost connection to to MySQL database", SM_ERROR_DB);
        
        }else{
		
		    $resultArray = array();
		
		    $result = @mysql_query($querystring, $this->dblink);
		
		    for($i=0;$i<@mysql_num_rows($result);$i++){
    			$row = @mysql_fetch_array($result, MYSQL_ASSOC);
    			array_push($resultArray, $row);
    		}
		
    		$this->recordQuery($querystring);
		
    		return $resultArray;
		
	    }
	}
	
	protected function recordQuery($querystring, $file='', $line=''){
		
		if(strlen(@mysql_error($this->dblink)) > 0){
			$error = "MySQL ERROR ".@mysql_errno($this->dblink) . ": " . @mysql_error($this->dblink);
		}else{
			$error = "Query OK";
		}
		
		if($file){
		    $querystring .= '; in'.$file;
		}
		
		if($line && is_numeric($line)){
		    $querystring .= ' on line '.$line;
		}
		
		// $querystring .= ';';
		
		$this->lastQuery = $querystring;
		array_push($this->queryHistory, $querystring."; ".$error);
	}
	
	public function specificQuery($wantedField, $qualifyingField, $qualifyingValue, $table){
	    
	    if(!$this->dblink){
		    $this->reconnect();
		}
	    
		$query = "SELECT $wantedField, $qualifyingField FROM $table WHERE $qualifyingField='$qualifyingValue' LIMIT 1";
		if($result = $this->rawQuery($query)){
			// $this->recordQuery($query);
			$value = @mysql_result($result, 0, $wantedField);
			return $value;
		}
	}
	
	protected function getQueryType($querystring){
    
    	if(preg_match( '/^(\w+)\s/i', $querystring, $match)){
			return strtoupper(trim($match[0]));
    	}else{
			return false;
    	}
	}
	
	
	public function query($querystring, $file='', $line='') {
		switch ($this->getQueryType($querystring)){
			
			case 'UPDATE': // update and delete queries return number of affected rows.
			case 'DELETE':
				
				if($result = $this->rawQuery($querystring)){
					return @mysql_affected_rows($result);
				}
				
				break;

			case 'INSERT': // do insert query and return id of last newly inserted row.
				// echo $querystring;
        		$result = $this->rawQuery($querystring);
        		// var_dump($result);
				return @mysql_insert_id($this->dblink);
				
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
	
	public function getDebugInfo(){
		return $this->queryHistory;
	}
}

?>