<?php

class SmartestSiteIdentificationHelper{
    
    protected $database;
    
    public function __construct(){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    public function getSiteByDomain($domain){
	    
	    $sql = "SELECT * FROM Sites WHERE site_domain='".$domain."'";
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result)){
	        $site = new SmartestSite;
	        $site->hydrate($result[0]);
	        return $site;
	    }else{
	        return false;
	    }
	    
	}
	
	public function getSiteByPageWebId($web_id){
	    
	    $sql = "SELECT Sites.*, Pages.page_webid FROM Pages, Sites WHERE Sites.site_id=Pages.page_site_id AND Pages.page_webid='".$web_id."'";
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result)){
	        $site = new SmartestSite;
	        $site->hydrate($result[0]);
	        return $site;
	    }else{
	        return false;
	    }
	    
	}
    
}