<?php

class SmartestPageUrl extends SmartestBasePageUrl{

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'pageurl_';
		$this->_table_name = 'PageUrls';
		
	}
	
	public function existsOnSite($url, $site_id){
	    
	    $sql = "SELECT page_id from Pages, PageUrls WHERE pageurl_page_id = page_id AND pageurl_url='".$url."' AND page_site_id='".$site_id."'";
	    $result = $this->database->queryToArray($sql);
	    
	    return count($result) ? true : false;
	    
	}
	
	public function __toString(){
	    return $this->getUrl();
	}
	
}