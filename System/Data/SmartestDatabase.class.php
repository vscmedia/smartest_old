<?php

class SmartestDatabase{
    
    protected $pdo;
    protected $lastQuery;
	protected $queryHistory;
	protected $id;
	protected $databaseName;
	
	public function __construct(){
	    
	    // $pdo_dsn = 
	    
	    try{
	        $this->pdo = new PDO();
        }catch (PDOException $e){
            
        }
	}
    
    public function queryToArray(){
        
    }
    
    public function rawQuery(){
        
    }
    
    public function specificQuery(){
        
    }
    
    protected function recordQuery(){
        
    }
    
    public function getDebugInfo(){
        
    }
    
}