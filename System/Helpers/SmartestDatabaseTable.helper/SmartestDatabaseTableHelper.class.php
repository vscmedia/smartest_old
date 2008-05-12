<?php

class SmartestDatabaseTableHelper{
    
    protected $database;
    
    public function __construct(){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    public function getTables(){
        
        if(SmartestCache::hasData('smartest_tables', true)){
			$tables = SmartestCache::load('smartest_tables', true);
		}else{
			$tables = $this->database->getTables();
			SmartestCache::save('smartest_tables', $tables, -1, true);
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
        
            if(SmartestCache::hasData($table.'_columns', true)){
			    $columns = SmartestCache::load($table.'_columns', true);
		    }else{
			    $columns = $this->database->getColumnNames($table);
			    SmartestCache::save($table.'_columns', $columns, -1, true);
		    }
		    
		    return $columns;
		
	    }else{
	        
	        throw new SmartestException('The table \''.$table.'\' does not exist.');
	        
	    }
        
    }
    
    public function flushCache(){
        
        $tables = $this->getTables();
        SmartestCache::clear('smartest_tables', true);
        
        foreach($tables as $t){
            SmartestCache::clear($t.'_columns', true);
        }
    }
    
}