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
		
		define('SM_AJAX_CALL', false);
		
		if($this->lookupSiteDomain()){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
		    
		    if(strlen($this->getRequest()->getRequestString())){
		        
		        try{
		        
		            if($this->_page = $this->manager->getNormalPageByUrl($this->getRequest()->getRequestString(), $this->_site->getId())){

        		        // we are viewing a static page
        		        if($this->_page->getLastPublished()){
        		            header("Last-Modified: ".date('D, j M Y H:i:s e', $this->_page->getLastPublished())); // Tue, 15 Nov 1994 12:45:26 GMT
        		        }
        		        $this->renderPage();

        		    }else if($this->_page = $this->manager->getItemClassPageByUrl($this->getRequest()->getRequestString(), $this->_site->getId())){

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
		        
		        if($this->_page->getLastPublished()){
		            header("Last-Modified: ".date('D, j M Y H:i:s e', $this->_page->getLastPublished())); // Tue, 15 Nov 1994 12:45:26 GMT
		        }
		        
		        $this->renderPage();
		        
		    }
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
	
	public function renderPageFromId($get){
		
		define('SM_AJAX_CALL', false);
		
		if($this->lookupSiteDomain()){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
		    
		    // var_dump($this->getRequestParameter('tag'));
		    
		    if($this->getRequestParameter('tag_name') && $this->getRequestParameter('tag')){
		        
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
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;
        	    
        }
        
	}
	
	public function renderEditableDraftPage($get){
		
		define('SM_AJAX_CALL', false);
		
		define('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN', $this->getUser()->hasToken('edit_containers_in_preview', false));
		
		$page_webid = $this->getRequestParameter('page_id');
		
		if($this->_site = $this->manager->getSiteByPageWebId($page_webid)){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
	        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
	        
	        if($this->_page = $this->manager->getNormalPageByWebId($page_webid, true)){
                
                if(in_array($this->_page->getId(), $this->_site->getSpecialPageIds()->getParameters())){
                    
                    if($this->_page->getId() == $this->_site->getSpecialPageIds()->g('search_page_id') && $this->getRequestParameter('q')){
                        // Search page
                        $p = $this->_page->copy('SmartestSearchPage');
                        $p->setSearchQuery($this->getRequestParameter('q'));
                        $this->_page = $p;
                    }
                    
                    if($this->_page->getId() == $this->_site->getSpecialPageIds()->g('tag_page_id') && $this->getRequestParameter('tag_name')){
                        
                        // Tag page
                        $p = $this->_page->copy('SmartestTagPage');
                        $t = new SmartestTag;
                        
                        if($t->hydrateBy('name', $this->getRequestParameter('tag_name'))){
                            $p->assignTag($t);
                            $p->setDraftMode(true);
                            $this->_page = $p;
                        }
                        
                    }
                    
                    if($this->_page->getId() == $this->_site->getSpecialPageIds()->g('user_page_id') && $this->getRequestParameter('author_id')){
                        
                        // User page
                        $p = $this->_page->copy('SmartestUserPage');
                        $u = new SmartestUser;
                        
                        if($u->find($this->getRequestParameter('author_id'))){
                            $p->assignUser($u);
                            $this->_page = $p;
                        }
                        
                    }
                }
                
                if($this->getRequestParameter('hide_newwin_link')){
    	            $this->setFormReturnUri();
    	            $this->setFormReturnDescription('page preview');
    	        }
                
    	        $this->_page->setDraftMode(true);
    	        $this->renderPage(true);

    	    }else if($get['item_id'] && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $get['item_id'], true)){
                
                if($this->getRequestParameter('hide_newwin_link')){
    	            $this->setFormReturnUri();
    	            $this->setFormReturnDescription('page preview');
    	        }
                
    	        $this->_page->setDraftMode(true);
    	        $this->renderPage(true);

    	    }else{

        		$this->renderNotFoundPage();

        	}
		    
		}
		
	}
	
	public function searchDomain(){
	    
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
	        
	        if($this->_site->getIsEnabled()){
	        
	            $database = SmartestDatabase::getInstance('SMARTEST');
	        
    	        $asset_url = urldecode($this->getRequestParameter('url'));
    	        $asset_webid = $this->getRequestParameter('key');
	        
    	        $sql = "SELECT * FROM Assets WHERE (asset_site_id='".$this->_site->getId()."' OR asset_shared='1') AND asset_url='".$asset_url."' AND asset_webid='".$asset_webid."'";
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
                
                    if($this->getRequestParameter('use_file_mime')){
                        $mime_type = $asset->getMimeType();
                    }else{
                		if($ua == 'Explorer' || $ua == 'Opera'){
                		    $mime_type = 'application/octetstream';
                		}else{
                		    $mime_type = 'application/octet-stream';
                		}
            		}
        		
            		$download->setMimeType($mime_type);
            		$download->send();
	            
    	        }else{
    	            // echo "File not found";
    	            $this->renderNotFoundPage(SM_ERROR_FILE_NOT_FOUND);
    	        }
	        
            }else{
                
                echo "Site not enabled";
                
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
	        
	        ///// START FILTER CHAIN
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
	
	private function renderNotFoundPage($error=SM_ERROR_PAGE_NOT_FOUND){
	    
	    if($this->lookupSiteDomain()){
	        
	        $draft_mode = ($this->getRequest()->getAction() == 'renderEditableDraftPage');
	        
	        $error_page_id = $this->_site->getErrorPageId();
	        $this->_page = new SmartestPage;
	        $this->_page->find($error_page_id);
	        $this->_page->setDraftMode($draft_mode);
	        define('SM_CMS_PAGE_SITE_ID', $this->_page->getSiteId());
	        
	        header("HTTP/1.1 404 Not Found");
	        $this->renderPage($draft_mode);
	        
        }else{
            
            // site domain not recognised, so we don't even knkow whether a page with this URL exists or not!
            
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
	
	public function getAuthorProfile(){
	    if($this->lookupSiteDomain()){
	        $u = new SmartestUser;
	        if($u->findBy('username', $this->getRequestParameter('username'))){
	            $p = new SmartestUserPage;
	            if($p->find($this->_site->getUserPageId())){
	                $p->assignUser($u);
	                $this->_page = $p;
	                $this->renderPage();
                }else{
                    // page designated as user page doesn't exist, or no page has been designated
                }
	        }else{
	            // User not recognised
	            $this->renderNotFoundPage();
	        }
        }
	}
	
	public function buildRobotsTxtFile(){
	    header('Content-type: text/plain');
	}
	
	public function systemStatusAsXml(){
	    header('Content-type: application/xml');
	}
	
	public function getCaptchaImage(){
	    
	    // Include Captcha Library
	    require SM_ROOT_DIR.'Library/Securimage/securimage.php';
	    
	    $img = new securimage();
	    $img->ttf_file        = SM_ROOT_DIR.'Library/Securimage/AHGBold.ttf';
        //$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
        //$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
        $img->image_height    = (is_numeric($this->getRequestParameter('height')) && $this->getRequestParameter('height') > 60) ? $this->getRequestParameter('height') : 90;                                // width in pixels of the image
        $img->image_width     = (is_numeric($this->getRequestParameter('width')) && $this->getRequestParameter('width') > 60 * M_E) ? $this->getRequestParameter('width') : $img->image_height * M_E;          // a good formula for image size
        //$img->perturbation    = .75;                               // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
        $img->text_color      = new Securimage_Color("#444444");   // captcha text color
        //$img->num_lines       = 8;                                 // how many lines to draw over the image
        //$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
        //$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
        //$img->signature_color = new Securimage_Color(rand(0, 64),
        //                                             rand(64, 128),
        //                                             rand(128, 255));  // random signature color
	    $img->show();
	}
	
}