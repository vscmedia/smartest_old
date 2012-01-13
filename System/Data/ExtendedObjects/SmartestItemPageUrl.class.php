<?php

class SmartestItemPageUrl extends SmartestPageUrl{

    protected $_item;
    
    public function setItem($item){
        $this->_item = $item;
    }
    
    public function __toString(){
        return $this->getItemSpecificUrl();
    }
    
    public function getItemSpecificUrl(){
        
        $url = ''.$this->getUrl();
        $url = str_replace(':name', $this->_item->getSlug(), $url);
        $url = str_replace(':long_id', $this->_item->getWebId(), $url);
        $url = str_replace(':id', $this->_item->getId(), $url);
        return $url;
        
    }
    
    public function usesName(){
        return strpos($this->getUrl(), ':name') !== false;
    }
    
    public function usesId(){
        return strpos($this->getUrl(), ':id') !== false;
    }
    
    public function usesLongId(){
        return strpos($this->getUrl(), ':long_id') !== false;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "url":
            return $this->getItemSpecificUrl();
            case "encoded":
            return urlencode($this->getItemSpecificUrl());
        }
        
        return parent::offsetGet($offset);
        
    }

}