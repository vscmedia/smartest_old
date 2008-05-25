<?php

class SmartestTodoItemType{
    
    protected $_id;
    protected $_category;
    protected $_label;
    protected $_description;
    protected $_table;
    protected $_class;
    protected $_foreign_key_field;
    protected $_uri_field;
    protected $_autocomplete;
    protected $_action;
    
    public function __construct($type_data){
        
        $this->_id = $type_data['id'];
        $this->_category = $type_data['category'];
        $this->_label = $type_data['label'];
        $this->_description = $type_data['description'];
        $this->_table = $type_data['table'];
        $this->_class = $type_data['class'];
        $this->_foreign_key_field = $type_data['foreignkeyfield'];
        $this->_uri_field = $type_data['urifield'];
        $this->_autocomplete = SmartestStringHelper::toRealBool($type_data['autocomplete']);
        $this->_action = $type_data['action'];
        
    }
    
    public function __toString(){
        return $this->_id;
    }
    
    public function __toArray(){
        return array(
            'id'=>$this->_id,
            'category'=>$this->_category,
            'label'=>$this->_label,
            'description'=>$this->_description,
            'table'=>$this->_table,
            'class'=>$this->_class,
            'foreignkeyfield'=>$this->_foreign_key_field,
            'urifield'=>$this->_uri_field,
            'autocomplete'=>$this->_autocomplete,
            'action'=>$this->_action
        );
    }
    
    public function getId(){
        return $this->_id;
    }
    
    public function getCategory(){
        return $this->_category;
    }
    
    public function getLabel(){
        return $this->_label;
    }
    
    public function getDescription(){
        return $this->_description;
    }
    
    public function getTable(){
        return $this->_table;
    }
    
    public function getClass(){
        return $this->_class;
    }
    
    public function getForeignKeyField(){
        return $this->_foreign_key_field;
    }
    
    public function getUriField(){
        return $this->_uri_field;
    }
    
    public function getAutoComplete(){
        return $this->_autocomplete;
    }
    
    public function getAction(){
        return $this->_action;
    }
    
}