<?php

class SmartestTagPage extends SmartestPage{
    
    protected $_tag;
    
    public function assignTag(SmartestTag $tag){
        $this->_tag = $tag;
        $this->_tag->setDraftMode($this->getDraftMode());
    }
    
    public function getTitle($force_static=false){
        if(is_object($this->_tag) && !$force_static){
            return $this->_tag->getLabel();
        }else{
            return $this->_properties['title'];
        }
    }
    
    public function getFormattedTitle(){
        $separator = $this->getParentSite()->getTitleFormatSeparator();
        return $this->getParentSite()->getName().' '.$separator.' Tag '.$separator.' '.$this->_tag->getLabel();
    }
    
    public function getDefaultUrl(){
        return 'tags/'.$this->_tag->getName().'.html';
    }
    
    public function fetchRenderingData(){
        
        $data = parent::fetchRenderingData();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), false);
        $this->_tag->setDraftMode($this->getDraftMode());
        $data->setParameter('tag', $this->_tag);
        return $data;
        
    }
    
    /* public function __toArray(){
        $data = parent::__toArray();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), true);
        // $data['formatted_title'] = "";
        $data['title'] = $this->getTitle();
        $data['tag'] = $this->_tag->__toArray();
        return $data;
    } */
    
    public function offsetGet($offset){
        
        switch($offset){
            case "tag":
            return $this->_tag;
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function getCacheAsHtml(){
        return 'FALSE';
    }
    
}