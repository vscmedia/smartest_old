<?php

class CmsFrontEnd extends SmartestSystemApplication{

	protected $_page;
	
	protected function __smartestApplicationInit(){
	    
	    $this->manager = new SmartestRequestUrlHelper;
	    // print_r($this->_site);
	    // define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
	    
	}
	
	/* protected function lookupSiteDomain(){
	    
	    try{
	    
		    if($this->_site = $this->manager->getSiteByDomain($_SERVER['HTTP_HOST'], $this->url)){
		
    		    if(is_object($this->_site)){
    		        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
    		        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
    		    }
		    
    		    return true;
		
    	    }
	    
        }catch(SmartestRedirectException $e){
            $e->redirect();
        }
	    
	} */
	
	public function getPage(){
	    return $this->_page;
	}
	
	public function renderPageFromUrl(){
		
		// var_dump($this->lookupSiteDomain());
		
		if($this->lookupSiteDomain()){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
		    
		    if(strlen($this->getRequest()->getRequestString())){
		        
		        try{
		        
		            if($this->_page = $this->manager->getNormalPageByUrl($this->getRequest()->getRequestString(), $this->_site->getId())){

        		        // we are viewing a static page
        		        $this->renderPage();

        		    }else if($this->_page = $this->manager->getItemClassPageByUrl($this->getRequest()->getRequestString(), $this->_site->getId())){

        		        // we are viewing a meta-page (based on an item from a data set)
        		        $this->renderPage();

        		    }else{

            		    $this->renderNotFoundPage();

            	    }
            	
        	    }catch(SmartestRedirectException $e){
        	        
        	        $e->redirect();
        	        
        	    }
		        
		    }else{
		        
		        // this is the home page
		        $this->_page = new SmartestPage;
		        $this->_page->find($this->_site->getTopPageId());
		        
		        $this->renderPage();
		        
		    }
        	
        	return Quince::NODISPLAY;
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
	
	public function renderPageFromId($get){
		
		if($this->lookupSiteDomain()){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
		    
		    if($this->getRequestParameter('tag_name')){
		        
		        // Page is a list of tagged content, not a real page.
		        
		        $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag_name'));
        	    $tag = new SmartestTag;

        	    if($tag->findBy('name', $tag_identifier)){
        	        
        	        $tag_page_id = $this->_site->getTagPageId();
        	        
                    $p = new SmartestTagPage;
                    $p->find($tag_page_id);
                    $p->assignTag($tag);
                    
                    $this->_page = $p;
                    $this->renderPage();

        	    }else{
        	        $this->renderNotFoundPage();
        	    }
		        
		    }else{
    		    
    		    $page_webid = $this->getRequestParameter('page_id');
    		    
    		    if($this->_page = $this->manager->getNormalPageByWebId($page_webid, false, $this->_site->getDomain())){
		        
		            // we are viewing a static page
		            $this->renderPage();
		        
		        }else if($this->getRequestParameter('item_id') && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $this->getRequestParameter('item_id'), false, $this->_site->getDomain())){
		        
		            // we are viewing a meta-page (based on an item from a data set)
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
		
		define('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN', $this->getUser()->hasToken('edit_containers_in_preview', false));
		
		$page_webid = $get['page_id'];
		    
	    if($this->_page = $this->manager->getNormalPageByWebId($page_webid, true)){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_page->getSiteId());
	        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
	        $this->_page->setDraftMode(true);
	        $this->renderPage(true);
	        
	    }else if($get['item_id'] && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $get['item_id'], true)){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_page->getSiteId());
	        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
	        $this->_page->setDraftMode(true);
	        $this->renderPage(true);
	        
	    }else{
    		
    		$this->renderNotFoundPage();
    		
    	}
    	
    	return Quince::NODISPLAY;
		
	}
	
	public function searchDomain($get){
	    
	    if($this->lookupSiteDomain()){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
            
            // search pages and all items
            $search_page_id = $this->_site->getSearchPageId();
	        
            $p = new SmartestSearchPage;
            
            if($p->find($search_page_id)){
                $p->setSearchQuery($this->getRequestParameter('q'));
                $this->_page = $p;
                $this->renderPage();
            }
            
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
	
	public function renderSiteTagSimpleRssFeed(){
	    
	    // var_dump($this->_site);
	    
	    if($this->lookupSiteDomain()){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
	        
	        $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag_name'));
    	    $tag = new SmartestTag;
	    
    	    if($tag->findBy('name', $tag_identifier)){
    	        
    	        $objects = $tag->getObjectsOnSite($this->_site->getId(), true);
    	        
    	        $rss = new SmartestRssOutputHelper($objects);
    	        $rss->setTitle($this->_site->getName()." | ".$tag->getLabel());
    	        $rss->send();
    	        
    	    }else{
    	        // echo "page not found";
    	        $this->renderNotFoundPage();
    	    }
	    
	    }else{
        	    
        	include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
	
	public function downloadAsset($get){
	    
	    if($this->lookupSiteDomain()){
	        
	        $database = SmartestDatabase::getInstance('SMARTEST');
	        
	        $asset_url = urldecode($this->getRequestParameter('url'));
	        $asset_webid = $this->getRequestParameter('key');
	        
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
	    
	    if($draft_mode || (is_object($this->_site) && (bool) $this->_site->getIsEnabled())){
	    
    	    $ph = new SmartestWebPagePreparationHelper($this->_page);
	    
    	    $overhead_finish_time = microtime(true);
    		$overhead_time_taken = number_format(($overhead_finish_time - SM_START_TIME)*1000, 2, ".", "");
		
    		if($this->_page instanceof SmartestItemPage){
    		    $this->_page->addHit();
    		}
		
    		define("SM_OVERHEAD_TIME", $overhead_time_taken);
    		SmartestPersistentObject::get('timing_data')->setParameter('overhead_time', microtime(true));
	    
    	    $html = $ph->fetch($draft_mode);
	    
    	    $fc = new SmartestFilterChain("WebPageBuilder");
    	    $fc->setDraftMode($draft_mode);
	        $html = $fc->execute($html);
	        
	        $cth = 'Content-Type: '.$this->getRequest()->getContentType().'; charset='.$this->getRequest()->getCharSet();
	        
    	    header($cth);
	        echo $html;
	        exit;
	    
        }else{
            
            echo "Site not enabled";
            exit;
            
        }
	    
	}
	
	private function renderNotFoundPage(){
	    
	    if($this->lookupSiteDomain()){
	        
	        $draft_mode = ($this->getRequest()->getAction() == 'renderEditableDraftPage');
	        
	        $error_page_id = $this->_site->getErrorPageId();
	        $this->_page = new SmartestPage;
	        $this->_page->find($error_page_id);
	        $this->_page->setDraftMode($draft_mode);
	        define('SM_CMS_PAGE_SITE_ID', $this->_page->getSiteId());
	        
	        header("HTTP/1.1 404 Not Found");
	        $this->renderPage($draft_mode);
	        
	        return Quince::NODISPLAY;
	        
        }else{
            
            
            
        }
		
	}
	
	public function addRating(){
	    
	}
	
	public function submitPageComment(){
	    
	}
	
	public function submitItemComment($get, $post){
	    
    	if($this->lookupSiteDomain()){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
	        
    	    $item = new SmartestItem;
	    
    	    if($item->find((int) $post['item_id'])){
	            
	            $content = strip_tags($post['comment_content']);
	            
        	    $item->attachPublicComment($post['comment_author_name'], $post['comment_author_website'], $content);
    	        $item->save(); // this is needed so that the change to item_num_comments is updated
    	        
        	    $this->redirect($item->getUrl());
	    
            }
        
        }
	    
	}
	
	public function buildXmlSitemap(){
	    if($this->lookupSiteDomain()){
	        header('Content-type: application/xml');
	        $this->send($this->_site->getPagesList(false, false, true), 'pages');
	        $this->send($this->_site, 'site');
        }
	}
	
	public function buildRobotsTxtFile(){
	    header('Content-type: text/plain');
	}
	
	public function systemStatusAsXml(){
	    header('Content-type: application/xml');
	}
	
}