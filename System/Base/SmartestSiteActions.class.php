<?php

class SmartestSiteActions{
    
    protected function getPresentationLayer(){
	    return SmartestPersistentObject::get('presentationLayer');
	}
	
	protected function getUserAgent(){
	    return SmartestPersistentObject::get('userAgent');
	}
	
	protected function getPage(){
	    return SmartestPersistentObject::get('currentPage');
	}
	
	protected function setTitle($page_title){
		$this->getPresentationLayer()->assign("sectionName", $page_title);
	}
    
    final protected function send($data, $name=""){
        $this->getPresentationLayer()->assign($name, $data);
    }
    
}