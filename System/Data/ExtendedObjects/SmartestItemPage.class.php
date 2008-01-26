<?php

class SmartestItemPage extends SmartestPage{
    
    protected $_identifying_field_name = null;
    protected $_identifying_field_value = null;
    protected $_url_variables = array();
    protected $_principal_item = null;
    protected $_simple_item = null;
    protected $_dataset = null;
    
    public function getDataSet(){
        
        if(!$this->_dataset){
            
            $this->_dataset = new SmartestCmsItemSet;
            
            if($this->_dataset->hydrate($this->getDatasetId())){
                $this->_dataset->getMembers();
            }
        }
        
        return $this->_dataset;
    }
    
    /* public function setItemId($id){
        
    }*/
    
    public function getPrincipalItem(){
        return $this->_principal_item;
    }
    
    public function setPrincipalItem($item){
        // print_r($item);
        $this->_principal_item = $item;
    }
    
    public function assignPrincipalItem(){
        // print_r($this->getDataSet()->getMembers());
        // $items = $this->getDataSet()->getMembers()
        
        // if($item = $this->getDataSet()->getItem($this->getIdentifyingFieldName(), $this->getIdentifyingFieldValue())){
        if($item = SmartestCmsItem::retrieveByPk($this->_simple_item->getId())){
            $this->_principal_item = $item;
            // print_r($this->_principal_item);
        }else{
            return false;
        }
    }
    
    public function setIdentifyingFieldName($field_name){
        if(!isset($this->_identifying_field_name)){
            $this->_identifying_field_name = $field_name;
        }
    }
    
    public function getIdentifyingFieldName(){
        return $this->_identifying_field_name;
    }
    
    public function setIdentifyingFieldValue($field_name){
        if(!isset($this->_identifying_field_value)){
            $this->_identifying_field_value = $field_name;
        }
    }
    
    public function getIdentifyingFieldValue(){
        return $this->_identifying_field_value;
    }
    
    public function setUrlNameValuePair($name, $value){
        $this->_url_variables[$name] = $value;
    }
    
    public function isAcceptableItem($draft_mode=false){
        
        // echo $this->_identifying_field_name;
        // echo $this->_identifying_field_value;
        
        if($this->_identifying_field_name && $this->_identifying_field_value){
            
            $sql = "SELECT * FROM Items WHERE item_".$this->_identifying_field_name."='".$this->_identifying_field_value."'";
            
            if(!$draft_mode){
                $sql .= " AND item_public='TRUE'";
            }
            
            $sql .= " AND item_deleted !='1' LIMIT 1";
            
            // echo $sql;
            
            $result = $this->database->queryToArray($sql);
            
            // print_r($result[0]);
            
            // print_r($this->database->getDebugInfo());
            
            if(count($result)){
                
                // echo $this->getDatasetId();
                
                if($this->getDatasetId() == $result[0]['item_itemclass_id']){
                    $this->_simple_item = new SmartestItem;
                    // print_r($this->_simple_item);
                    $this->_simple_item->hydrate($result[0]);
                // if($this->getDataSet()->hasItem($this->_identifying_field_name, $this->_identifying_field_value)){
                    return true;
                }else{
                    return false;
                }
            
            }else{
                return false;
            }
        }
    }
    
    
}