<?php

class SmartestPageRenderingDataRequestHandler implements ArrayAccess{

    protected $_page;
    protected $_all_tags = null;
    protected $_site;
    
    public function __construct(SmartestPage $page){
        $this->assignPage($page);
    }
    
    public function assignPage(SmartestPage $page){
        $this->_page = $page;
    }
    
    public function getNavigationDataRequestHandler(){
        return new SmartestPageNavigationDataRequestHandler($this->_page);
    }
    
    public function getSite(){
        if(!$this->_site){
            $this->_site = $this->_page->getParentSite();
        }
        return $this->_site;
    }
    
    public function getAllTags(){
        if(!$this->_all_tags){
            $du = new SmartestDataUtility;
    	    $this->_all_tags = $du->getTags();
        }
        return $this->_all_tags;
    }
    
    public function getPrincipalItem(){
        if($this->isItem()){
            return $this->_page->getPrincipalItem();
        }else{
            return null;
        }
    }
    
    public function isItem(){
        return $this->_page instanceof SmartestItemPage;
    }
    
    public function offsetGet($offset){
        
        if($this->_page instanceof SmartestItemPage){
            $model_varname = SmartestStringHelper::toVarName($this->_page->getPrincipalItem()->getModel()->getName());
        }else{
            $model_varname = '_X_';
        }
        
        switch($offset){
            
            case "tag":
            return $this->_page->getTag();
            
            case "page":
            return $this->_page;
        
            case "site":
            return $this->getSite();
        
            case "all_tags":
            return $this->getAllTags();
        
            case "authors":
            return $this->_page->getAuthors();
        
            case "fields":
            return $this->_page->getPageFieldDefinitions();
        
            case "request":
            return SmartestPersistentObject::get('request_data');
        
            case "navigation":
            return $this->getNavigationDataRequestHandler();
        
            case "principal_item":
            case "item":
            case $model_varname:
            return $this->getPrincipalItem();
        
            case "is_item":
            return $this->isItem();
        
            case "has_item":
            return $this->getPrincipalItem() instanceof SmartestCmsItem;
        
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetExists($offset){}
    public function offsetUnset($offset){}

}