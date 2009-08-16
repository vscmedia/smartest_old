<?php

class SmartestLogType extends SmartestParameterHolder{
    
    protected $_log_id;
    
    public function __construct($array){
        
        $this->_log_id = $array['id'];
        $this->_name = $array['description'];
        
        foreach($array as $k=>$v){
            $this->setParameter($k, $v);
        }
        
        $this->_read_only = true;
    }
    
    public function getSiteId(){
        
        return SmartestPersistentObject::get('current_open_project')->getId();
        
    }
    
    protected function getCurrentSiteId(){
	    
	    $site_id = 0;
	    
	    if(SM_CONTROLLER_MODULE == 'website'){
	        if(defined('SM_CMS_PAGE_SITE_ID')){
	            $site_id = constant('SM_CMS_PAGE_SITE_ID');
            }else{
                // throw new SmartestException("Site ID not defined.");
            }
        }else if(is_object(SmartestPersistentObject::get('current_open_project'))){ // make sure the site object exists
            $site_id = SmartestPersistentObject::get('current_open_project')->getId();
        }
        
        return $site_id;
	}
	
	public function getFormat(){
	    return $this->getParameter('format');
	}
    
    public function getFormatProcessed(){
        $format = str_replace('%TIMESTAMP%', date('Y-m-d H:i:s'), $this->getFormat());
        return $format;
    }
    
    public function getLogFile(){
        
        $file = SM_ROOT_DIR.$this->getParameter('file');
        $file = str_replace('%DAY%', date('Ymd'), $file);
        $file = str_replace('%SITEID%', $this->getCurrentSiteId(), $file);
        return $file;
        
    }
    
}