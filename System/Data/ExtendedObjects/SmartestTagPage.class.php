<?php

class SmartestTagPage extends SmartestPage{
    
    protected $_tag;
    
    public function assignTag(SmartestTag $tag){
        $this->_tag = $tag;
    }
    
    /* public function getTitle(){
        if(is_object($this->_tag)){
            return $this->_properties['title'].' | '.$this->_tag->getLabel();
        }else{
            return $this->_properties['title'];
        }
    } */
    
    public function getDefaultUrl(){
        return 'tags/'.$this->_tag->getName().'.html';
    }
    
    public function fetchRenderingData(){
        
        $data = parent::fetchRenderingData();
        $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), false);
        // print_r($data);
        return $data;
        
    }
    
    public function __toArray(){
        $data = parent::__toArray();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), true);
        // $data['formatted_title'] = "";
        $data['tag'] = $this->_tag->__toArray();
        return $data;
    }
    
    public function getCacheAsHtml(){
        return 'FALSE';
    }
    
}