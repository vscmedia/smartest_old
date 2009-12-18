<?php

class SmartestItemPageUrl extends SmartestPageUrl{

    protected $_item;
    
    public function setItem($item){
        $this->_item = $item;
    }
    
    public function getItemSpecificUrl(){
        
        $url = $this->getUrl();
        $url = str_replace(':name', $this->_item->getSlug(), $url);
        $url = str_replace(':webid', $this->_item->getWebId(), $url);
        $url = str_replace(':id', $this->_item->getId(), $url);
        return $url;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "url":
            return $this->getItemSpecificUrl();
        }
        
        return parent::offsetGet($offset);
        
    }

}