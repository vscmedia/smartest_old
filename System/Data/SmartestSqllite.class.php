<?php

class SmartestSqllite implements SmartestDataAccessClass{
	
	protected $handle;
	protected $queryHistory = array();
	protected $tables = array();
	public $lastQuery;
	
	public function __construct($database_file='System/Cache/Data/smartest_main.db'){
		if(function_exists('sqlite_open')){
			if($this->handle = sqlite_open($database_file)){
				// connection was successful
				$this->rawQuery('BEGIN;');
			}else{
				throw new SmartestException("Could not connect to SQLite", SM_ERROR_DB);
				return null;
			}
		}else{
			throw new SmartestException("SQLite needs to be installed", SM_ERROR_PHP);
			return null;
		}	
	}
	
	public function queryToArray($querystring, $file='', $line=''){
		
		$result = @sqlite_unbuffered_query($this->handle, $querystring, SQLITE_ASSOC, $last_error);
		$resultset = array();
		
		while($row = sqlite_fetch_array($result)){
			$resultset[] = $row;
		}
		
		$this->recordQuery($querystring, $last_error);
		
		return $resultset;
		
	}
	
	public function rawQuery($querystring, $file='', $line=''){
		$result = @sqlite_unbuffered_query($this->handle, $querystring, SQLITE_ASSOC, $last_error);
		$this->recordQuery($querystring, $last_error);
	}
	
	public function howMany($querystring, $file='', $line=''){
		
	}
	
	public function specificQuery($wantedField, $qualifyingField, $qualifyingValue, $table){
		
	}
	
	public function getTables(){
		
		if(!count($this->tables)){
		
			$sql = "SELECT name FROM sqlite_master WHERE type = 'table'";
			// $result = $this->rawQuery($sql);

			$tables = $this->queryToArray($sql);
		
			$table_names = array();
		
			foreach($tables as $tr){
				$table_names[] = $tr['name'];
			}
			
			$this->tables = $table_names;
		
		}else{
			
			return $this->tables;
			
		}
		
		return $tables;
		
	}
	
	public function getColumns($table){
		
		/* $sql = "SHOW COLUMNS FROM ".$table;
		$result = mysql_query($sql);

		$columns = $this->queryToArray($sql);
		
		return $columns; */
		
	}
	
	public function getColumnNames($table){
		
		/* $sql = "SHOW COLUMNS FROM ".$table;
		$result = mysql_query($sql);

		$columns = $this->queryToArray($sql);
		
		$names = array();
		
		foreach($columns as $column){
			$names[] = $column['Field'];
		}
		
		return $names; */
		
	}
	
	
	protected function getQueryType($querystring){
    
    	if(preg_match( '/^(\w+)\s/i', $querystring, $match)){
			return strtoupper(trim($match[0]));
    	}else{
			return false;
    	}
	}
	
	protected function recordQuery($querystring, $error=null){
		
		if($error){
			$error_string = "SQLITE ERROR: " . $error;
		}else{
			$error_string = "Query OK";
		}
		
		$this->lastQuery = $querystring;
		
		array_push($this->queryHistory, $querystring." ".$error_string);
	}
	
	public function getDebugInfo(){
		return $this->queryHistory;
	}
	
}