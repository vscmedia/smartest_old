<?php

class SmartestDatabaseTableHelper{
    
    protected $database;
    
    public function __construct($connection_name = ''){
        
        if(strlen($connection_name)){
            $this->database = SmartestDatabase::getInstance($connection_name);
        }else{
            if(isset($_SESSION)){
                $this->database = SmartestPersistentObject::get('db:main');
            }else{
                throw new SmartestException("Tried to construct a SmartestDatabaseTableHelper object with neither an active session or a specified connection name;");
            }
        }
        
    }
    
    public function getTables(){
        
        $cache_name = strtolower($this->database->getConnectionName()).'_tables';
        
        if(SmartestCache::hasData($cache_name, true) && count(SmartestCache::load($cache_name, true))){
			$tables = SmartestCache::load($cache_name, true);
		}else{
			$tables = $this->database->getTables();
			SmartestCache::save($cache_name, $tables, -1, true);
		}
		
		return $tables;
        
    }
    
    public function tableExists($table){
        
        $tables = $this->getTables();
        return in_array($table, $tables);
        
    }
    
    public function tableHasColumn($table, $column){
        
        if($this->tableExists($table)){
            
            $cols = $this->getColumnNames($table);
            return in_array($column, $cols);
            
        }else{
	        
	        throw new SmartestException('The table \''.$table.'\' does not exist.');
	        
	    }
        
    }
    
    public function getColumnNames($table){
        
        if($this->tableExists($table)){
            
            $cache_name = strtolower($this->database->getConnectionName()).'_'.$table.'_columns';
            
            if(SmartestCache::hasData($cache_name, true)){
			    $columns = SmartestCache::load($cache_name, true);
		    }else{
			    $columns = $this->database->getColumnNames($table);
			    SmartestCache::save($cache_name, $columns, -1, true);
		    }
		    
		    return $columns;
		
	    }else{
	        
	        throw new SmartestException('Tried to get column names on a table, \''.$table.'\', that does not exist.');
	        
	    }
        
    }
    
    public function flushCache(){
        
        $cn = strtolower($this->database->getConnectionName());
        $cache_name = $cn.'_tables';
        
        $tables = $this->getTables();
        SmartestCache::clear($cache_name, true);
        
        foreach($tables as $t){
            $cache_name = $cn.'_'.$t.'_columns';
            SmartestCache::clear($cache_name, true);
        }
    }
    
}