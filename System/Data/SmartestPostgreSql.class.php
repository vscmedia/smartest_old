<?php

class SmartestPostgreSql{
    
    public function __construct(SmartestParameterHolder $dbconfig){
        
    }
    
    public function getTables($refresh=false){
		
	}
	
	public function getColumns($table, $refresh=false){
		
	}
	
	public function getColumnNames($table, $refresh=false){
		
	}
	
	public function rawQuery($querystring){
	    
	}
	
	public function queryToArray($querystring, $refresh=false){
	    
	}
    
    public function query($sql){
        
    }
    
    public function executeSqlFile($full_file_path){ 
	    
	}
	
	public function getLastQuery(){
	    
	}
	
	public function getDebugInfo(){
		
	}
    
}