<?php

class SmartestExternalFeed extends SmartestExternalUrl implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_feed = null;
    protected $_feed_error = false;
    
    public function offsetGet($offset){
        switch($offset){
            case "data":
            return $this->getFeedData();
            case "items":
            return $this->getItems();
            case "has_error":
            return (bool) $this->_feed_error;
            case "error_message":
            return $this->_feed_error;
/*             case "_host":
            return $this->getValue();
            case '_request':
            return $this->getValue();
            case '_protocol':
            return $this->getValue();
            case "encoded":
            case "urlencoded":
            return urlencode($this->getValue());
            case 'empty':
            return !strlen($this->getValue());
            case 'string':
            return $this->__toString(); */
        }
        
        return parent::offsetGet($offset);
    }
    
    public function setValue($v){
        $v = str_replace('feed://', 'http://', $v);
        $v = str_replace('feed:https://', 'https://', $v);
        $this->_value = $v;
    }
    
    public function getFeedData($refresh=false){
        
        if($refresh || !$this->_feed){
            
            $feed = new SimplePie();
            $feed->set_cache_location(SM_ROOT_DIR.'System/Cache/SimplePie/');
            $feed->set_feed_url($this->getValue());
            $feed->set_input_encoding('UTF-8');
            $feed->set_item_class('SmartestExternalFeedItem');
            $feed->handle_content_type();
            
            if($feed->init() && !$feed->error()){
                $this->_feed = $feed;
                return $this->_feed;
            }else if($feed->error()){
                $this->_feed_error = $feed->error();
                return false;
            }
            
        }
        
        return $this->_feed;
        
    }
    
    public function getItems($maximum=10){
        
        if($this->getFeedData()){
            return new SmartestArray($this->getFeedData()->get_items(0, $maximum));
        }else{
            return array();
        }
        
    }
    
}