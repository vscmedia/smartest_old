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
    
    /* public function getSiteId(){
        
        if(isset($_SESSION)){
            return SmartestPersistentObject::get('current_open_project')->getId();
        }
        
    } */
    
    protected function getCurrentSiteId(){
	    
	    $site_id = 0;
	    
	    if(SM_CONTROLLER_MODULE == 'website'){
	        if(defined('SM_CMS_PAGE_SITE_ID')){
	            $site_id = constant('SM_CMS_PAGE_SITE_ID');
            }else{
                // throw new SmartestException("Site ID not defined.");
                return '0';
            }
        }else{
            if(isset($_SESSION)){
                if(is_object(SmartestSession::get('current_open_project'))){ // make sure the site object exists
                    $site_id = SmartestSession::get('current_open_project')->getId();
                }else{
                    return '0';
                }
            }else{
                return '0';
            }
        }
        
        return $site_id;
	}
	
	public function getFormat(){
	    return $this->getParameter('format');
	}
    
    public function getFormatProcessed(){
        if(!ini_get('date.timezone')){
            date_default_timezone_set('Europe/London');
        }
        $format = str_replace('%TIMESTAMP%', date('Y-m-d H:i:s'), $this->getFormat());
        return $format;
    }
    
    public function getLogFile(){
        if(!ini_get('date.timezone')){
            date_default_timezone_set('Europe/London');
        }
        $file = SM_ROOT_DIR.$this->getParameter('file');
        $file = str_replace('%DAY%', date('Ymd'), $file);
        $file = str_replace('%SITEID%', $this->getCurrentSiteId(), $file);
        return $file;
        
    }
    
}