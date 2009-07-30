<?php

class SmartestSiteCreationHelper{

    public function createNewSite(SmartestParameterHolder $p, $initial_user=''){
        
        if($initial_user instanceof SmartestUser){
            $u = $initial_user;
        }else if(SmartestPersistentObject::get('user') instanceof SmartestUser){
            $u = SmartestPersistentObject::get('user');
        }else{
            SmartestLog::getInstance('system')->log("Could not create new site without valid user. None given.", SM_LOG_ERROR);
            throw new SmartestException("Tried to create site without logged in user or valid user object");
        }
        
        $site = new SmartestSite;
        $site->setName($p->getParameter('site_name'));
        $site->setTitleFormat('$site | $page');
        $site->setDomain($p->getParameter('site_domain'));
        $site->setAdminEmail($p->getParameter('site_admin'));
        $site->setAutomaticUrls('OFF');
	    $site->save();
	    SmartestLog::getInstance('system')->log("User {$u->__toString()} created a new site record: '{$site->getName()}/{$site->getDomain()}'", SM_LOG_DEBUG);
	    
	    if($p->getParameter('site_master_template') == '_DEFAULT'){
	        $master_template = '';
	    }else if($p->getParameter('site_master_template') == '_BLANK'){
	        $master_template_name = SmartestFileSystemHelper::getFileName(SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Masters/'.SmartestStringHelper::toVarName($site->getName()).'.tpl'));
	        $master_template_contents = str_replace('default.tpl', $master_template_name, file_get_contents(SM_ROOT_DIR.'System/Install/Samples/default.tpl'));
	        if(file_put_contents(SM_ROOT_DIR.'Presentation/Masters/'.$master_template_name, $master_template_contents)){
	            $master_template = $master_template_name;
	        }else{
	            $master_template = '';
	            SmartestLog::getInstance('system')->log("Could not create ".SM_ROOT_DIR.'Presentation/Masters/'.$master_template_name.": Permission denied", SM_LOG_WARNING);
	        }
	    }else{
	        if(is_file(SM_ROOT_DIR.'Presentation/Masters/'.$p->getParameter('site_master_template'))){
	            $master_template = $p->getParameter('site_master_template');
	        }else{
	            $master_template = '';
	            SmartestLog::getInstance('system')->log("Could not set ".SM_ROOT_DIR.'Presentation/Masters/'.$p->getParameter('site_master_template')." as master template for new site: File does not exist", SM_LOG_WARNING);
	        }
	    }
    
	    $home_page = new SmartestPage;
	    $home_page->setTitle('Home');
	    $home_page->setName('home');
	    $home_page->setDraftTemplate($master_template);
	    $home_page->setWebid(SmartestStringHelper::random(32));
	    $home_page->setSiteId($site->getId());
	    $home_page->setCreatedbyUserid($u->getId());
	    $home_page->setOrderIndex(0);
	    $home_page->save();
	    $home_page->addAuthorById($u->getId());
	    $site->setTopPageId($home_page->getId());
	    SmartestLog::getInstance('system')->log("Added home page to new site (page ID {$home_page->getId()})", SM_LOG_DEBUG);
    
	    $error_page = new SmartestPage;
	    $error_page->setTitle('Page not found');
	    $error_page->setName('404-error');
	    $error_page->setSiteId($site->getId());
	    $error_page->setDraftTemplate($master_template);
	    $error_page->setLiveTemplate($master_template);
	    $error_page->setParent($home_page->getId());
	    $error_page->setWebid(SmartestStringHelper::random(32));
	    $error_page->setCreatedbyUserid($u->getId());
	    $error_page->setOrderIndex(1024);
	    $error_page->setIsPublished('TRUE');
	    $error_page->save();
	    $error_page->addAuthorById($u->getId());
	    $site->setErrorPageId($error_page->getId());
	    SmartestLog::getInstance('system')->log("Added 404 page to new site (page ID {$error_page->getId()})", SM_LOG_DEBUG);
        
        $search_page = new SmartestPage;
	    $search_page->setTitle('Search Results');
	    $search_page->setName('search');
	    $search_page->setSiteId($site->getId());
	    $search_page->setDraftTemplate($master_template);
	    $search_page->setLiveTemplate($master_template);
	    $search_page->setParent($home_page->getId());
	    $search_page->setWebid(SmartestStringHelper::random(32));
	    $search_page->setCreatedbyUserid($u->getId());
	    $search_page->setOrderIndex(1022);
	    $search_page->save();
	    $search_page->addAuthorById($u->getId());
	    $site->setSearchPageId($search_page->getId());
	    SmartestLog::getInstance('system')->log("Added search page to new site (page ID {$search_page->getId()})", SM_LOG_DEBUG);
	    
	    $tag_page = new SmartestPage;
	    $tag_page->setTitle('Tagged Content');
	    $tag_page->setName('tag');
	    $tag_page->setSiteId($site->getId());
	    $tag_page->setDraftTemplate($master_template);
	    $tag_page->setLiveTemplate($master_template);
	    $tag_page->setParent($home_page->getId());
	    $tag_page->setWebid(SmartestStringHelper::random(32));
	    $tag_page->setCreatedbyUserid($u->getId());
	    $tag_page->setOrderIndex(1023);
	    $tag_page->save();
	    $tag_page->addAuthorById($u->getId());
	    $site->setTagPageId($tag_page->getId());
	    SmartestLog::getInstance('system')->log("Added tag page to new site (page ID {$tag_page->getId()})", SM_LOG_DEBUG);
	    
	    $site->save();
    
	    /* $logo_upload = new SmartestUploadHelper('site_logo');
	    $logo_upload->setUploadDirectory(SM_ROOT_DIR.'Public/Resources/Images/SiteLogos/');
    
	    if($logo_upload->hasDotSuffix('gif', 'png', 'jpg', 'jpeg')){
			$logo_upload->save();
			$site->setLogoImageFile($logo_upload->getFileName());
		}else{
		    $site->setLogoImageFile('default_site.jpg');
		} */
	
		self::createSiteDirectory($site);
	
		if(!$u->hasGlobalPermission('site_access')){
		    $u->addToken('site_access', $site->getId());
		}
	
		if(!$u->hasGlobalPermission('modify_user_permissions')){
		    $u->addToken('modify_user_permissions', $site->getId());
		}
		
		return $site;
        
    }
    
    public static function createSiteDirectory(SmartestSite $site){
        
        $site_dir = SM_ROOT_DIR.'Sites/'.substr(SmartestStringHelper::toCamelCase($site->getName()), 0, 64).'/';
	    
	    if(is_dir($site_dir)){
	        // $old_site_dir = 
	        // $folder = $site->getName().microtime();
	        // $site_dir = SM_ROOT_DIR.'Sites/'.sha1($folder).'/';
	        $site_dir = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Sites/'.substr(SmartestStringHelper::toCamelCase($site->getName()), 0, 64).'/');
	    }
	    
	    mkdir($site_dir);
	    
	    if(!is_dir($site_dir.'Presentation')){mkdir($site_dir.'Presentation');}
	    if(!is_dir($site_dir.'Configuration')){mkdir($site_dir.'Configuration');}
	    if(!is_file($site_dir.'Configuration/site.yml')){file_put_contents($site_dir.'Configuration/site.yml', '');}
	    if(!is_dir($site_dir.'Library')){mkdir($site_dir.'Library');}
	    if(!is_dir($site_dir.'Library/Actions')){mkdir($site_dir.'Library/Actions');}
	    
	    $actions_class_name = SmartestStringHelper::toCamelCase($site->getName()).'Actions';
	    $class_file_contents = file_get_contents(SM_ROOT_DIR.'System/Base/ClassTemplates/SiteActions.class.php.txt');
	    $class_file_contents = str_replace('__TIMESTAMP__', time('Y-m-d h:i:s'), $class_file_contents);
	    if(!is_file($site_dir.'Library/Actions/SiteActions.class.php')){file_put_contents($site_dir.'Library/Actions/SiteActions.class.php', $class_file_contents);}
	    chmod($site_dir.'Library/Actions/SiteActions.class.php', 0666);
	    $site->setDirectoryName(substr(SmartestStringHelper::toCamelCase($site->getName()), 0, 64));
		
		$site->save();
        
    }

}