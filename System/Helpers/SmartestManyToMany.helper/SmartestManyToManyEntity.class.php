<?php

class SmartestManyToManyEntity{
    
    protected $_table = '';
    protected $_foreignKey = '';
    protected $_entityIndex;
    protected $_class = '';
    protected $_required = false;
    protected $_default_sort = null;
    
    public function __construct($table, $foreignKey, $entityIndex, $class, $required=false){
        
        $dbth = new SmartestDatabaseTableHelper;
        
        if($dbth->tableExists($table)){
            
            $this->_table = $table;
            
            if($dbth->tableHasColumn($this->_table, $foreignKey)){
                $this->_foreignKey = $foreignKey;
            }else{
                // throw new SmartestException('The column \''.$foreignKey.'\' does not exist in table \''.$table.'\'');
            }
            
        }else{
            // throw new SmartestException('The table \''.$table.'\' does not exist.');
        }
        
        $this->_entityIndex = $entityIndex;
        $this->_class = $class;
        $this->_required = $required;
    }
    
    public function getTable(){
        return $this->_table;
    }
    
    public function getForeignKeyField($add_table=true){
        if($add_table){
            return $this->_table.'.'.$this->_foreignKey;
        }else{
            return $this->_foreignKey;
        }
    }
    
    public function getEntityIndex(){
        return $this->_entityIndex;
    }
    
    public function getClass(){
        return $this->_class;
    }
    
    public function isRequired(){
        return $this->_required;
    }
    
    public function hasDefaultSort(){
        return (bool) $this->_default_sort;
    }
    
    public function getDefaultSort(){
        return $this->_default_sort;
    }
    
    public function setDefaultSort($sort){
        $this->_default_sort = $this->_table.'.'.$sort;
    }
    
}