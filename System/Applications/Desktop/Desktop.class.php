<?php

class Desktop extends SmartestApplication{
    
    function __moduleConstruct(){
        
    }
    
    function startPage(){
        
        if($this->getSite() instanceof SmartestSite){
            
            // code to assemble the desktop goes here
            $this->setTitle('Start Page');
            $this->send('desktop', 'display');
            $this->send($this->getSite()->__toArray(), 'site');
            // print_r($this->getSite());
            
        }else{
            $this->setTitle('Choose a Site');
            // $result = $this->manager->getSites();
            $result = $this->getUser()->getAllowedSites();

    		$sites = array();

    		foreach($result as $site){
    		    /* $ts = new SmartestSite;
    		    if($ts->hydrate($site)){
    		        $sites[] = $ts->__toArray();
    		    } */
    		    $sites[] = $site->__toArray();
    		}
    		
    		$this->send($sites, 'sites');
    		$this->send('sites', 'display');
    		$this->send(count($sites), 'num_sites');
    		$this->send($this->getUser()->hasToken('create_sites'), 'show_create_button');
        }
        
        
    }
    
    function editSite($get){
	    
	    if($this->getSite() instanceof SmartestSite){
		    
		    $site_id = $this->getSite()->getId();
		    // $site = new SmartestSite;
		    // $site->hydrate($site_id);
		    
		    $main_page_templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/Masters/');
		    
		    // print_r($main_page_templates);
		    
		    // $this->send($main_page_templates, 'templates');
		    
		    // $sql = "SELECT * FROM Sites WHERE site_id = $site_id";
		
		    // $result = $this->database->queryToArray($sql);
		    $sitedetails = $this->getSite()->__toArray();
		    // $sitedetails = $this->database->queryToArray($sql);
            
            // print_r($sitedetails);
            
            // print_r();
            
            // $pages = $this->getSite()->getPagesTree();
            
            $pages = $this->getSite()->getPagesList();
            
            // print_r($pages);
            
            // $old_pages = $this->manager->getPagesList($site_id);
            $this->send($pages, 'pages');
            // echo count($pages);
            // print_r($pages);
            
		    $this->setTitle("Edit Site Parameters");
		    $this->send($sitedetails, 'site');
		    // return array("sitedetails"=>$sitedetails);
	    }else{
	        
	        $this->addUserMessageToNextRequest('You must have an open site to open eddit settings.');
	        $this->redirect('/smartest');
	        
	    }
		
	}
	
	function updateSiteDetails($get, $post){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        $site = $this->getSite();
	        $site->setName($post['site_name']);
	        $site->setTitleFormat($post['site_title_format']);
	        $site->setDomain($post['site_domain']);
	        $site->setRoot($post['site_root']);
	        // $site->setErrorTitle($post['site_error_title']);
	        // $site->setErrorTpl($post['site_error_tpl']);
	        $site->setTopPageId($post['site_top_page']);
	        $site->setTagPageId($post['site_tag_page']);
	        $site->setSearchPageId($post['site_search_page']);
	        $site->setErrorPageId($post['site_error_page']);
	        $site->setAdminEmail($post['site_admin_email']);
	        $site->save();
	        
		    // $site_id = $post['site_id'];
		    $this->formForward();
	    }
	}
    
/*    public function siteList(){
		
		$result = $this->manager->getSites();
		
		$sites = array();
		
		foreach($result as $site){
		    $ts = new SmartestSite;
		    if($ts->hydrate($site)){
		        $sites[] = $ts->__toArray();
		    }
		}
		
		$this->setTitle("Select a Site");
		
		// return array("sites"=>$result, 'n');
		$this->send($result, 'oldsites');
		$this->send($sites, 'sites');
	} */
	
	public function openSite($get){
		
		if(@$get['site_id']){
		    
		    if(in_array($get['site_id'], $this->getUser()->getAllowedSiteIds())){
		    
		        $site = new SmartestSite;
		    
		        if($site->hydrate($get['site_id'])){
			        SmartestSession::set('current_open_project', $site);
			        // $this->redirect('/'.SM_CONTROLLER_MODULE.'/sitePages?site_id='.$site->getId());
			        $this->getUser()->reloadTokens();
			        $this->redirect('/smartest');
		        }
	        }else{
	            
	            $this->addUserMessageToNextRequest('You don\'t have permission to access that site. This action has been logged.');
	            $this->redirect('/smartest');
	            
	        }
		}
	}
	
	public function closeCurrentSite($get){
		// if(@$get['site_id']){
			SmartestSession::clear('current_open_project');
			$this->getUser()->reloadTokens();
			$this->redirect('/smartest');
		// }
	}
	
	public function createSite(){
	    if($this->getUser()->hasToken('create_sites')){
	        $this->send(SM_ROOT_DIR, "sm_root_dir");
	        $this->send($this->getUser()->__toArray(), "user");
	        $templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/Masters/');
	        $this->send($templates, 'templates');
	    }else{
	        $this->addUserMessageToNextRequest('You don\'t have permission to create new sites. This action has been logged.');
            $this->redirect('/smartest');
	    }
	}
	
	public function buildSite($get, $post){
	    
	    $site = new SmartestSite;
        // site->setDraftTemplate($post['site_draft_template']);
	    $site->setName($post['site_name']);
        $site->setTitleFormat($post['site_title_format']);
        $site->setDomain($post['site_domain']);
        $site->setRoot($post['site_root']);
        $site->setAdminEmail($post['site_admin_email']);
        $site->setAutomaticUrls('OFF');
	    $site->save();
	    // $this->addUserMessage($site->getLastQuery());
	    
	    $home_page = new SmartestPage;
	    $home_page->setTitle($post['site_home_page_title']);
	    $home_page->setName(SmartestStringHelper::toSlug($post['site_home_page_title']));
	    $home_page->setWebid(SmartestStringHelper::random(32));
	    $home_page->setSiteId($site->getId());
	    $home_page->setCreatedbyUserid($this->getUser()->getId());
	    $home_page->save();
	    $site->setTopPageId($home_page->getId());
	    // $this->addUserMessage($home_page->getLastQuery());
	    
	    $error_page = new SmartestPage;
	    $error_page->setTitle($post['site_error_page_title']);
	    $error_page->setName(SmartestStringHelper::toSlug($post['site_error_page_title']));
	    $error_page->setSiteId($site->getId());
	    $error_page->setParent($home_page->getId());
	    $error_page->setWebid(SmartestStringHelper::random(32));
	    $error_page->setCreatedbyUserid($this->getUser()->getId());
	    $error_page->save();
	    $site->setErrorPageId($error_page->getId());
	    // $this->addUserMessage($error_page->getLastQuery());
	    
	    $tag_page = new SmartestPage;
	    $tag_page->setTitle('Tagged Content');
	    $tag_page->setName('tag');
	    $tag_page->setSiteId($site->getId());
	    $tag_page->setParent($home_page->getId());
	    $tag_page->setWebid(SmartestStringHelper::random(32));
	    $tag_page->setCreatedbyUserid($this->getUser()->getId());
	    $tag_page->save();
	    // $this->addUserMessage($tag_page->getLastQuery());
	    $site->setTagPageId($tag_page->getId());
	    
	    $search_page = new SmartestPage;
	    $search_page->setTitle('Search Results');
	    $search_page->setName('search');
	    $search_page->setSiteId($site->getId());
	    $search_page->setParent($home_page->getId());
	    $search_page->setWebid(SmartestStringHelper::random(32));
	    $search_page->setCreatedbyUserid($this->getUser()->getId());
	    $search_page->save();
	    // $this->addUserMessage($search_page->getLastQuery());
	    $site->setTagPageId($search_page->getId());
	    
	    $logo_upload = new SmartestUploadHelper('site_logo');
	    $logo_upload->setUploadDirectory(SM_ROOT_DIR.'Public/Resources/Images/SiteLogos/');
	    
	    if($logo_upload->hasDotSuffix('gif', 'png', 'jpg', 'jpeg')){
			// $logo_upload->setFileName($upload->getFileName());
			$logo_upload->save();
			$site->setLogoImageFile($logo_upload->getFileName());
		}else{
		    $site->setLogoImageFile('default_site.jpg');
		}
		
		$site->save();
		
		if(!$this->getUser()->hasGlobalPermission('site_access')){
		    // 
		    $this->getUser()->addToken('site_access', $site->getId());
		}
		
		if(!$this->getUser()->hasGlobalPermission('modify_user_permissions')){
		    // 
		    $this->getUser()->addToken('modify_user_permissions', $site->getId());
		}
		
		$this->openSite(array('site_id'=>$site->getId()));
		$this->addUserMessageToNextRequest("The site has successfully been created. You must now log out and back in again to start editing.");
		$this->redirect("/smartest");
	    
	}
    
    function todoList(){
        
        $this->setFormReturnUri();
        
        // print_r($this->manager);
        $this->setTitle('Your To-do List');
        
        // get all self-assigned items, which can be marked as done without a follow-up
        $self_assigned = $this->manager->getSelfAssignedTodoListItemsAsArrays($this->getUser()->getId());
        $this->send($self_assigned, 'self_assigned_tasks');
        $this->send(count($self_assigned), 'num_self_assigned_tasks');
        
        // get all items assigned by other users
        $other_assigned = $this->manager->getAssignedTodoListItemsAsArrays($this->getUser()->getId());
        $this->send($other_assigned, 'assigned_tasks');
        $this->send(count($other_assigned), 'num_assigned_tasks');
        
        // collect other responsibilities from the system
        
        $duty_items = array();
        $total_num_duty_items = 0;
        
        // get Locked Pages
        $locked_pages = $this->manager->getLockedPageDuties($this->getUser()->getId());
        $total_num_duty_items += count($locked_pages);
        $this->send($locked_pages, 'locked_pages');
        
        // get Locked Items
        $locked_items = $this->manager->getLockedItemDuties($this->getUser()->getId());
        $total_num_duty_items += count($locked_items);
        $this->send($locked_items, 'locked_items');
        
        // get Items awaiting approval
        if($this->getUser()->hasToken('approve_item_changes')){
            $items_awaiting_approval = $this->manager->getItemsAwaitingApproval($this->getUser()->getId());
            $total_num_duty_items += count($items_awaiting_approval);
            $this->send($items_awaiting_approval, 'items_awaiting_approval');
            $this->send(true, 'show_items_awaiting_approval');
        }else{
            $this->send(false, 'show_items_awaiting_approval');
        }
        
        // get Pages awaiting approval
        if($this->getUser()->hasToken('approve_page_changes')){
            $pages_awaiting_approval = $this->manager->getPagesAwaitingApproval($this->getUser()->getId());
            $total_num_duty_items += count($pages_awaiting_approval);
            $this->send($pages_awaiting_approval, 'pages_awaiting_approval');
            $this->send(true, 'show_pages_awaiting_approval');
        }else{
            $this->send(false, 'show_pages_awaiting_approval');
        }
        
        // get Items awaiting publishing
        if($this->getUser()->hasToken('publish_approved_items')){
            $items_awaiting_publishing = $this->manager->getItemsAwaitingPublishing($this->getUser()->getId());
            $total_num_duty_items += count($items_awaiting_publishing);
            $this->send($items_awaiting_publishing, 'items_awaiting_publishing');
            $this->send(true, 'show_items_awaiting_publishing');
        }else{
            $this->send(false, 'show_items_awaiting_publishing');
        }
        
        // var_dump($this->getUser()->hasToken('publish_approved_pages'));
        
        // get Pages awaiting publishing
        if($this->getUser()->hasToken('publish_approved_pages')){
            $pages_awaiting_publishing = $this->manager->getPagesAwaitingPublishing($this->getUser()->getId());
            $total_num_duty_items += count($pages_awaiting_publishing);
            $this->send($pages_awaiting_publishing, 'pages_awaiting_publishing');
            $this->send(true, 'show_pages_awaiting_publishing');
        }else{
            $this->send(false, 'show_pages_awaiting_publishing');
        }
        
        $this->send($total_num_duty_items, 'num_duty_items');
        
    }
    
}