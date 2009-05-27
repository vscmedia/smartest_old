<?php

class SmartestDataValueAsset extends SmartestAsset{
    
    protected $_display_params;
    protected $_draft_mode = false;
    
    public function __toString(){
	    return $this->render();
	}
	
	public function render(){
	    if($this->getId()){
	        return parent::render(array(), $this->_display_params, $this->_draft_mode);
        }else{
            if($this->_draft_mode){
                return '<em>[No file selected for this value]</em>';
            }
        }
	}
	
	public function setDraftMode($m){
	    $this->_draft_mode = (bool) $m;
	}
	
	public function getDraftMode($m){
	    return $this->_draft_mode;
	}
	
	public function setContextInfo($info){
	    
	    if(is_array($info)){
	        $ph = new SmartestParameterHolder('Asset Display Content Info: '.$this->getStringId());
	        $ph->loadArray($info);
	    }
	    
	    $this->_display_params = $info;
	    
	}
	
	public function getContentInfo(){
	    return $this->_display_params;
	}
    
}