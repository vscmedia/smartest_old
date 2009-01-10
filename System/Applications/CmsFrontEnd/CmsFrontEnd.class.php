<?php

class CmsFrontEnd extends SmartestSystemApplication{

	// protected $smarty;
	public $url;
	protected $_site;
	protected $_page;
	
	function __moduleConstruct(){
		
		// look up site by domain
		$this->_site = $this->manager->getSiteByDomain($_SERVER['HTTP_HOST']);
		
		if(is_object($this->_site)){
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		}
		
	}
	
	public function getPage(){
	    return $this->_page;
	}
	
	public function renderPageFromUrl(){
		
		if(is_object($this->_site)){
		    
		    if(!strlen($this->url)){
		        
		        // this is the home page
		        $this->_page = new SmartestPage;
		        $this->_page->find($this->_site->getTopPageId());
		        
		        // $this->send($this->_page, '_page');
		        $this->renderPage();
		        
		    }else if($this->_page = $this->manager->getNormalPageByUrl($this->url, $this->_site->getId())){
		        
		        // we are viewing a static page
		        // $this->send($this->_page, '_page');
		        $this->renderPage();
		        
		    }else if($this->_page = $this->manager->getItemClassPageByUrl($this->url, $this->_site->getId())){
		        
		        // we are viewing a meta-page (based on an item from a data set)
		        // $this->send($this->_page, '_page');
		        $this->renderPage();
		        
		    }else{
		        
        		$this->send($this->renderNotFoundPage(), '_page');
        		
        	}
        	
        	return Quince::NODISPLAY;
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
	
	public function renderPageFromId($get){
		
		if(is_object($this->_site)){
		    
		    if(isset($get['tag_name'])){
		        
		        // Page is a list of tagged content, not a real page.
		        
		        $tag_identifier = SmartestStringHelper::toSlug($get['tag_name']);
        	    $tag = new SmartestTag;

        	    if($tag->hydrateBy('name', $tag_identifier)){
        	        
        	        $tag_page_id = $this->_site->getTagPageId();
        	        
                    $p = new SmartestTagPage;
                    $p->find($tag_page_id);
                    $p->assignTag($tag);
                    
                    $this->_page = $p;
                    // $this->send($this->_page, '_page');
                    $this->renderPage();

        	    }else{
        	        // $this->send($this->renderNotFoundPage(), '_page');
        	        $this->renderNotFoundPage();
        	    }
		        
		    }else{
    		    
    		    $page_webid = $get['page_id'];
    		    
    		    if($this->_page = $this->manager->getNormalPageByWebId($page_webid, $this->_site->getId())){
		        
		            // we are viewing a static page
		            // $this->send($this->_page, '_page');
		            $this->renderPage();
		        
		        }else if($get['item_id'] && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $get['item_id'], $this->_site->getId())){
		        
		            // we are viewing a meta-page (based on an item from a data set)
		            // $this->send($this->_page, '_page');
		            $this->renderPage();
		        
		        }else{
        		
        		    // $this->send($this->renderNotFoundPage(), '_page');
        		    $this->renderNotFoundPage();
        		
        	    }
        	
    	    }
    	    
    	    return Quince::NODISPLAY;
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;
        	    
        }
        
	}
	
	public function renderEditableDraftPage($get){
		
		$page_webid = $get['page_id'];
		
		if(is_object($this->_site)){
		    
		    if($this->_page = $this->manager->getNormalPageByWebId($page_webid, $this->_site->getId(), true)){
		        
		        // $this->send($this->_page, '_page');
		        $this->_page->setDraftMode(true);
		        $this->renderPage(true);
		        
		    }else if($get['item_id'] && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $get['item_id'], $this->_site->getId(), true)){
		        
		        // we are viewing a meta-page (based on an item from a data set)
		        // $this->send($this->_page, '_page');
		        $this->_page->setDraftMode(true);
		        $this->renderPage(true);
		        
		    }else{
        		
        		// $this->send($this->renderNotFoundPage(), '_page');
        		$this->renderNotFoundPage();
        		
        	}
        	
        	return Quince::NODISPLAY;
		    
	    }else{
        	    
        	include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
		
	}
	
	public function searchDomain($get){
	    
	    if(is_object($this->_site)){
            
            // search pages and stuff
            $search_page_id = $this->_site->getSearchPageId();
	        
            $p = new SmartestSearchPage;
            $p->hydrate($search_page_id);
            $p->setSearchQuery($get['q']);
            
            $this->_page = $p;
            $this->send($this->_page, '_page');
            
            return Quince::NODISPLAY;
            
        }else{

            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;

        }
	}
	
	/* public function tagListPage($get){
	    // echo $get['tag_name'];
	    if(is_object($this->_site)){
            
            $tag_page_id = $this->getSite()->getTagPageId();
            // echo $tag_page_id;
            $p = new SmartestPage;
            $p->hydrate($tag_page_id);
            $this->_page = $p;
            // $this->manager->getNormalPageByWebId($page_webid, $this->_site->getId())
            
        }else{
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;
        }
        
	} */
	
	public function renderSiteTagSimpleRssFeed($get){
	    
	    if(is_object($this->_site)){
	    
	        $tag_identifier = SmartestStringHelper::toSlug($get['tag_name']);
    	    $tag = new SmartestTag;
	    
    	    if($tag->hydrateBy('name', $tag_identifier)){
    	        $objects = $tag->getObjectsOnSite($this->_site->getId(), true);
    	        // print_r($objects);
    	        $rss = new SmartestRssOutputHelper($objects);
    	        $rss->setTitle($this->_site->getName()." - Tagged Content: ".$tag->getLabel());
    	        $rss->send();
    	        
    	    }else{
    	        // $objects = array();
    	        $this->renderNotFoundPage();
    	    }
    	    
    	    return Quince::NODISPLAY;
	    
	    }else{
        	    
        	include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
	
	public function downloadAsset($get){
	    
	    if(is_object($this->_site)){
	        
	        $database = SmartestPersistentObject::get('db:main');
	        
	        $asset_url = $get['url'].'.'.$get['suffix'];
	        $asset_webid = $get['key'];
	    
	        $sql = "SELECT * FROM Assets WHERE asset_site_id='".$this->_site->getId()."' AND asset_url='".$asset_url."' AND asset_webid='".$asset_webid."'";
	        $result = $database->queryToArray($sql);
	        
	        if(count($result)){
	            
	            $asset = new SmartestAsset;
	            $asset->hydrate($result[0]);
	            
	            if($asset->usesLocalFile()){
    		        $download = new SmartestDownloadHelper($asset->getFullPathOnDisk());
    		    }else{
    		        $download = new SmartestDownloadHelper($asset->getTextFragment()->getContent());
    		        $download->setDownloadFilename($asset->getDownloadableFilename());
    		    }

    		    $ua = $this->getUserAgent()->getAppName();

        		if($ua == 'Explorer' || $ua == 'Opera'){
        		    $mime_type = 'application/octetstream';
        		}else{
        		    $mime_type = 'application/octet-stream';
        		}

        		$download->setMimeType($mime_type);
        		$download->send();
	            
	        }
	    
        }
        
        exit;
	    
	}
	
	private function renderPage($draft_mode=false){
	    
	    $ph = new SmartestWebPagePreparationHelper($this->_page);
	    
	    $overhead_finish_time = microtime(true);
		$overhead_time_taken = number_format(($overhead_finish_time - SM_START_TIME)*1000, 2, ".", "");
		
		define("SM_OVERHEAD_TIME", $overhead_time_taken);
	    
	    $html = $ph->fetch($draft_mode);
	    
	    $fc = new SmartestFilterChain("WebPageBuilder");
	    $fc->setDraftMode($draft_mode);
	    $html = $fc->execute($html);
	    
	    echo $html;
	    
	    exit;
	    
	}
	
	private function renderNotFoundPage(){
	    
	    if(is_object($this->_site)){
	        
	        $error_page_id = $this->_site->getErrorPageId();
	        $this->_page = new SmartestPage;
	        $this->_page->find($error_page_id);
	        
	        $this->renderPage();
	        
	        return Quince::NODISPLAY;
	        
        }
		
	}
	
}