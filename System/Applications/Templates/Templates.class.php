<?php
  
include_once SM_ROOT_DIR."System/Applications/Assets/AssetsManager.class.php";

class Templates extends SmartestSystemApplication{

	private $AssetsManager;
	
	function __moduleConstruct(){
	    
		$this->AssetsManager = new AssetsManager();
		
	}
	
	public function startPage(){          	
		$this->setTitle("Your Templates");
		$this->setFormReturnUri();
	}

	public function containerTemplates(){
		
		$this->setFormReturnUri();
		
		$templates = $this->AssetsManager->getAssetsByTypeCode("SM_ASSETTYPE_CONTAINER_TEMPLATE", $this->getSite()->getId());
		
		if(count($templates)){
			// return array("assetList"=>$assetTypeMembers);
			$this->send($templates, "assetList");
		}else {
		    $this->send(array(), "assetList");
			$this->send("No members of this type", "error");
		}
	} 
	
	public function listItemTemplates(){
	    
		$path = SM_ROOT_DIR.'Presentation/ListItems/'; 
		
		$this->setFormReturnUri();
		$templates = SmartestFileSystemHelper::getDirectoryContents($path, false, SM_DIR_SCAN_FILES);
		
		if(count($templates) > 0){
		    $this->send($templates, "templateList");
		}else {
		    $this->send(array(), "templateList");
		    $this->send("No members of this type", "error");
		}
		
	} 
	
	public function masterTemplates(){
		
		$this->setFormReturnUri();
		
		$h = new SmartestTemplatesLibraryHelper;
		$templates = $h->getMasterTemplates($this->getSite()->getId());
		
		if(count($templates)>0){
		    $this->send($templates, "templateList");
		}else {
		    $this->send(array(), "templateList");
		    $this->send("No members of this type", "error");
		}
		
	}
	
	public function import(){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $non_template_categories = $h->getCategoryShortNames();
	    $templates_key = array_search('templates', $non_template_categories);
	    unset($non_template_categories[$templates_key]);
	    $types = $h->getTypes();
	    
	    $location_types = $h->getTypeCodesByStorageLocation($non_template_categories);
	    $locations = array_keys($location_types);
	    $location_types_info = $location_types;
	    
	    foreach($location_types_info as $path => &$l){
	        foreach($l as &$type){
	            $type = $types[$type];
	        }
	    }
	    
	    $this->send($location_types_info, 'types_info');
	    
	    // now, get a list of the file names for each location that can be found in the database
	    foreach($location_types as $location => $types){
            
            $sql = "SELECT asset_url FROM Assets WHERE asset_type IN ('".implode("', '", $types)."')";
            $result = SmartestDatabase::getInstance('SMARTEST')->queryToArray($sql);
            $db_files[$location] = array();
            
            foreach($result as $f){
                if(strlen($f['asset_url'])){
                    $db_files[$location][] = $f['asset_url'];
                }
            }
        }
        
        // now, get a list of the file names for each location, whether or not they can be found in the database.
        foreach($locations as $location){
            
            $disk_files[$location] = array();
            $disk_files[$location] = SmartestFileSystemHelper::getDirectoryContents($location, false, SM_DIR_SCAN_FILES);
            
        }
        
        // now, compare the list of what is found in each location with what exists in the database for those types.
        foreach($locations as $location){
            
            $new_files[$location] = array();
            foreach($disk_files[$location] as $file_on_disk){
                // if the file is not in the database,
                if(!in_array($file_on_disk, $db_files[$location])){
                    // it is a new file.
                    $new_files[$location][] = $file_on_disk;
                }
            }
        }
        
        $this->send($new_files, 'new_files');
	    
	}
	
	public function addTemplateData($get, $post){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $location_types = $h->getTypeCodesByStorageLocation();
	    
	    if(isset($post['new_files']) && is_array($post['new_files'])){
	        $new_files = $post['new_files'];
	    }else{
	        $new_files = array();
	    }
	    
	    $files_array = array();
	    $i = 0;
	    
	    // $types_list_for_unknown_extensions = $h->getImportableFileTypes();
	    // $this->send($types_list_for_unknown_extensions, 'all_importable_types');
	    
	    foreach($new_files as $f){
	        
	        $files_array[$i] = array();
	        $types = $h->getPossibleTypesBySuffix(SmartestStringHelper::getDotSuffix($f));
	        $files_array[$i]['filename'] = basename($f);
	        $files_array[$i]['suggested_name'] = SmartestStringHelper::removeDotSuffix($files_array[$i]['filename']);
	        $files_array[$i]['current_directory'] = dirname($f).'/';
	        
	        if(count($types)){
	            $files_array[$i]['possible_types'] = $types;
	            $files_array[$i]['suffix_recognized'] = true;
            }else{
                $files_array[$i]['possible_types'] = $h->getAcceptableNameOptionsForUnknownSuffix($files_array[$i]['filename'], $files_array[$i]['current_directory']);
                $files_array[$i]['suffix_recognized'] = false;
                $files_array[$i]['actual_suffix'] = SmartestStringHelper::getDotSuffix($f);
            }
            
	        $files_array[$i]['type_code'] = $type['id'];
	        $files_array[$i]['type_label'] = $type['label'];
	        
	        $alh = new SmartestAssetsLibraryHelper;
	        
	        $files_array[$i]['size'] = SmartestFileSystemHelper::getFileSizeFormatted(SM_ROOT_DIR.$f);
	        $i++;
	    }
	    
	    $this->send($files_array, 'files');
	    
	}
	
	public function createTemplateAssetsFromFiles($get, $post){
	    
	    if(isset($post['new_files']) && is_array($post['new_files'])){
	        $new_files = $post['new_files'];
	    }else{
	        $new_files = array();
	    }
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $asset_types = SmartestDataUtility::getAssetTypes();
	    
	    foreach($new_files as $nf){
	        
	        $type = $asset_types[$nf['type']];
	        $required_suffixes = array();
	        
	        foreach($type['suffix'] as $s){
	            $required_suffixes[] = $s['_content'];
	        }
	        
	        $existing_location = dirname($nf['filename']).'/';
	        $existing_suffix = SmartestStringHelper::getDotSuffix($nf['filename']);
	        
	        $required_location = $type['storage']['location'];
	        
	        if($existing_location != $required_location){
	            // The file type has been recognized by its file suffix, but needs to be moved to the right place for the file type chosen by the user (user has been warned about this)
	            $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$required_location.SmartestFileSystemHelper::baseName($nf['filename']));
	            $success = SmartestFileSystemHelper::move(SM_ROOT_DIR.$nf['filename'], $move_to);
	            $filename = SmartestFileSystemHelper::baseName($move_to);
	        }else if(!in_array($existing_suffix, $required_suffixes)){
	            // The file is in the right place, but had an unrecognized file suffix (so needs to be renamed - user has been warned about this)
	            $no_suffix = SmartestStringHelper::removeDotSuffix($nf['filename']);
	            $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$required_location.SmartestFileSystemHelper::baseName($no_suffix).'.'.$required_suffixes[0]);
	            $success = SmartestFileSystemHelper::move(SM_ROOT_DIR.$nf['filename'], $move_to);
	            $filename = SmartestFileSystemHelper::baseName($move_to);
	        }else{
	            $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$nf['filename']);
	            $filename = SmartestFileSystemHelper::baseName($nf['filename']);
	            $success = true;
	        }
	        
	        if($success){
	            
    	        $a = new SmartestAsset;
    	        
    	        $a->setType($nf['type']);
    	        $a->setSiteId($this->getSite()->getId());
    	        $a->setShared(isset($nf['shared']) ? 1 : 0);
    	        $a->setWebid(SmartestStringHelper::random(32));
    	        $a->setStringid(SmartestStringHelper::toVarName($nf['name']));
    	        $a->setUrl($filename);
    	        $a->setUserId($this->getUser()->getId());
    	        $a->setCreated(time());
    	        $a->save();
    	        
            }
	        
	    }
	    
	    $this->addUserMessageToNextRequest(count($new_files)." file".((count($new_files) == 1) ? " was " : "s were ")."successfully added to the repository.", SmartestUserMessage::SUCCESS);
	    $this->formForward();
	    
	}
	
	public function importSingleTemplate($get){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $non_template_categories = $h->getCategoryShortNames();
	    $templates_key = array_search('templates', $non_template_categories);
	    unset($non_template_categories[$templates_key]);
	    $show_form = true;
	    
	    $cat = $h->getTypesByCategory($non_template_categories);
	    $types = $cat['templates']['types'];
	    $this->send($types, 'template_types');
	    
	    $location = $h->getStorageLocationByTypeCode($get['asset_type']);
	    
	    if($location == SmartestAssetsLibraryHelper::ASSET_TYPE_UNKNOWN){
	        $message = "Template type ".$get['asset_type']." was not recognized.";
	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
	        $this->send($message, 'error_message');
	        $show_form = false;
	    }else if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
	        $this->send($message, 'error_message');
	        $show_form = false;
	    }else{
	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$get['template_name']);
	        $this->send($template, 'template');
	    }
	    
	    $this->send($show_form, 'show_form');
	    
	}
	
	public function addSingleTemplateToDatabase($get, $post){
	    
	    $tlh = new SmartestTemplatesLibraryHelper;
	    $types = $tlh->getTypes();
	    
	    $type = $types[$post['template_type']];
	    
	    $existing_location = $post['template_current_storage'];
        
        $required_location = $type['storage']['location'];
        $current_path = realpath(SM_ROOT_DIR.$existing_location.$post['template_filename']);
        
        if(is_file($current_path) && SmartestFileSystemHelper::isSafeFileName($current_path, SM_ROOT_DIR.'Presentation/')){
        
            if($existing_location != $required_location){
                // The file type has been recognized by its file suffix, but needs to be moved to the right place (so needs to be moved - user has been warned about this)
                $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$required_location.SmartestFileSystemHelper::baseName($current_path));
                // var_dump($required_location);
                $success = SmartestFileSystemHelper::move($current_path, $move_to);
                $filename = SmartestFileSystemHelper::baseName($move_to);
            }else{
                $move_to = SmartestFileSystemHelper::getUniqueFileName($current_path);
                $filename = SmartestFileSystemHelper::baseName($move_to);
                $success = true;
            }
        
            if($success){
            
    	        $a = new SmartestAsset;
	        
    	        $a->setType($post['template_type']);
    	        $a->setSiteId($this->getSite()->getId());
    	        $a->setShared(isset($post['template_shared']) ? 1 : 0);
    	        $a->setWebid(SmartestStringHelper::random(32));
    	        $a->setStringid(SmartestStringHelper::toVarName($post['template_name']));
    	        $a->setUrl($filename);
    	        $a->setUserId($this->getUser()->getId());
    	        $a->setCreated(time());
    	        $a->save();
                
                $this->addUserMessageToNextRequest("The template has been successfully imported to the repository.", SmartestUserMessage::SUCCESS);

            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The file you tried to import was not found or was outside the templates directory.", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function addTemplate($get){
		
		$type = (in_array($get['type'], array('SM_PAGE_MASTER_TEMPLATE', 'SM_LIST_ITEM_TEMPLATE', 'SM_CONTAINER_TEMPLATE'))) ? $get['type'] : 'SM_PAGE_MASTER_TEMPLATE';
		
		switch($type){
		    case "SM_PAGE_MASTER_TEMPLATE":
		    $title = "Add a Page Master Template";
		    $path = SM_ROOT_DIR."Presentation/Masters/";
		    break;
		    
		    case "SM_LIST_ITEM_TEMPLATE":
		    $title = "Add a List Item Template";
		    $path = SM_ROOT_DIR."Presentation/ListItems/";
		    break;
		    
		    case "SM_CONTAINER_TEMPLATE":
		    $title = "Add a Container Template";
		    $path = SM_ROOT_DIR."Presentation/Layouts/";
		    break;
		}
		
		$this->setTitle($title);
		
		// Allow the user to make the addition.
		
		$allow_save = is_writable($path);

		$formTemplateInclude = "addTemplate.tpl";
				
		$this->send($allow_save, 'allow_save');
		$this->send($path, 'path');
		$this->send($title, 'interface_title');
		$this->send($type, 'template_type');
	}
	
 	function saveNewTemplate($get, $post){
		
		$template_type = $post['template_type'];
		
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
		    
			$path = SM_ROOT_DIR."Presentation/Masters/";
			
		}else if($template_type == 'SM_LIST_ITEM_TEMPLATE'){
		    
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			
		}else if($template_type == 'SM_CONTAINER_TEMPLATE'){
		    
    		$path = SM_ROOT_DIR."Presentation/Layouts/";
    		
    	}
		
		if($post['add_type'] == "DIRECT"){
		    
			$content  = $post['template_content'];
			
			if(substr($content, 0, 9) == '<![CDATA['){
			    $content = substr($content, 9);
			}
			
			if(substr($content, -3) == ']]>'){
			    $content = substr($content, 0, -3);
			}
			
			$stringid = SmartestStringHelper::toVarName($post['template_filename']);
			$file     = $post['template_filename'];
			
			if(!in_array(SmartestStringHelper::getDotSuffix($file), array('tpl', 'html'))){
			    $file = $stringid.'.tpl';
			}
			
			$full_filename = SmartestFileSystemHelper::getUniqueFileName($path.$file);
			$final_filename = basename($full_filename);
			
		}elseif($post['add_type'] == "UPLOAD"){
		    
		    // var_dump($_FILES);
		    
			// $file = $_FILES['template_uploaded']['name'];
			$upload = new SmartestUploadHelper('template_upload');
			$upload->setUploadDirectory($path);
			
			if(!$upload->hasDotSuffix('tpl', 'html')){
    			$upload->setFileName(SmartestStringHelper::toVarName($upload->getFileName()).".tpl");
    		}
    		
    		$final_filename = $upload->getFileName();
    		$full_filename = $path.$final_filename;
			
		}
		
		if($template_type == 'SM_CONTAINER_TEMPLATE'){
		    
		    // Add the template asset to the database
		    $new_template = new SmartestAsset;
    		
    		$new_template->setType('SM_ASSETTYPE_CONTAINER_TEMPLATE');
    		$new_template->setStringid($stringid);
    		$new_template->setWebid(SmartestStringHelper::random(32));
    		$new_template->setUrl($final_filename);
    		$new_template->setSiteId($this->getSite()->getId());
    		$shared = (isset($post['template_shared']) && $post['template_shared']) ? 1 : 0;
    		$new_template->setShared($shared);
    		$new_template->save();
    		
		}
		
		// $filename = $path.$file;
		$this->addUserMessage($full_filename);
		
		if($post['add_type'] == "DIRECT"){
			
			if(SmartestFileSystemHelper::save($full_filename, stripslashes($post['template_content']), true)){
				$this->setFormReturnVar('savedNewTemplate', 'true');
				$this->addUserMessageToNextRequest('The file was saved successfully', SmartestUserMessage::SUCCESS);
			}else{
				$this->setFormReturnVar('savedNewTemplate', 'failed');
				$this->addUserMessageToNextRequest('There was a problem creating the file', SmartestUserMessage::WARNING);
			}
			
		}else if($post['add_type'] == "UPLOAD"){
		
		    if($upload->save()) { // Move the file over
			    $this->setFormReturnVar('savedNewTemplate', 'true');
			    $this->addUserMessageToNextRequest('The file was saved successfully', SmartestUserMessage::SUCCESS);
		    }else{ // Couldn't save the file
		        $this->addUserMessageToNextRequest('There was a problem creating the file', SmartestUserMessage::WARNING);
			    // $this->setFormReturnVar('savedNewTemplate', 'failed');
		    }
		
		}
		
		$this->formForward();
	}
			
	function editTemplate($get){
		
		$template_type = $get['type'];
		
		$show_form = false;
		
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $get['template_name'];
			$title = 'Edit master template';
			$show_form = true;
			$this->setFormReturnUri();
		
		}else if($template_type == 'SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $get['template_name'];
			$title = 'Edit list item template';
			$show_form = true;
			$this->setFormReturnUri();
			
		}else if($template_type == 'SM_CONTAINER_TEMPLATE'){
    		
    		$path = SM_ROOT_DIR."Presentation/Layouts/";
    		$template = new SmartestAsset;
    		$title = 'Edit container template';
    		$show_form = true;
    		$this->setFormReturnUri();
    		
    		if($template->hydrate($get['template_id'])){
    		    $template_name = $template->getUrl();
    		}
    		
    		$this->send($template->getId(), "template_id");
    		
    	}else{
    	    $this->addUserMessage('The template type is invalid.', SmartestUserMessage::ERROR);
    	}
		
		$file = $path.$template_name;
		
		// make sure the file exists and is in the right place. only templates in the $path directory should be readable.
		if(is_file($file) && SmartestFileSystemHelper::isSafeFileName($file, $path)){
		    $contents = htmlentities(SmartestFileSystemHelper::load($file, true), ENT_COMPAT, 'UTF-8');
	    }else{
	        $show_form = false;
	        $this->addUserMessage('The template file '.$template_name.' does not exist, or is out of the editing scope.', SmartestUserMessage::ERROR);
	    }
		
		$this->send($path, 'path');
		$this->send(is_writable($path), 'dir_is_writable');
		$this->send(is_writable($file), 'file_is_writable');
		
		$is_editable = (is_writable($path) && is_writable($file) && $show_form);
		$this->send($is_editable, 'is_editable');
		
		$formTemplateInclude = "editTemplate.tpl";
		$this->send($contents, "template_content");
		$this->send($template_type, "template_type");
		$this->send($template_name, "template_name");
		$this->send($title, "interface_title");
		$this->send($show_form, "show_form");

	}
	
	function updateTemplate($get, $post){
		
		$template_type = $post['type'];
		
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $post['filename'];
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $post['filename'];
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			$template = new SmartestAsset;
			
			if($template->hydrate($post['template_id'])){
    		    $template_name = $template->getUrl();
    		}
			
		}
		
		$content = $post['template_content'];
		
		if(substr($content, 0, 9) == '<![CDATA['){
		    $content = substr($content, 9);
		}
		
		if(substr($content, -3) == ']]>'){
		    $content = substr($content, 0, -3);
		}
		
		$template_content = stripslashes($content);
		
		$file = $path.$template_name;
		
		if(SmartestFileSystemHelper::save($file, $template_content, true)){
			$this->setFormReturnVar('success', 'true');
			$this->addUserMessageToNextRequest('Your changes were saved successfully.');
		}else{
			$this->setFormReturnVar('success', 'failed');
			$this->addUserMessageToNextRequest('Couldn\'t save changes. Check file permissions.');
		}
  		
  		$this->formForward();
	}

	/* function removeTemplate($get){
		
		// make this 'type' like the others
		$template_type = $get['template_code'];
			
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			
		}
			
		$template_name=$get['template_name'];
		$draftpage_template_inuse=$this->manager->getTemplateInUseInDraftPage($template_name);
		$livepage_template_inuse=$this->manager->getTemplateInUseInLivePage($template_name);
		$draftpage_count = count($draftpage_template_inuse);
		$livepage_count = count($livepage_template_inuse);
			
		return array("template_code"=>$template_type, "template_name"=>$template_name, "draft_templates"=>$draftpage_template_inuse, "live_templates"=>$livepage_template_inuse, "draftpage_count"=>$draftpage_count, "livepage_count"=>$livepage_count); 
	} */
	
	function deleteTemplate($get){
			
		$template_type = $get['type'];
			
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $get['template_name'];
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $get['template_name'];
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			$asset = new SmartestAsset;
			$asset->hydrate($get['template_id']);
			$template_name = $asset->getUrl();
			
		}
		
		$old_filename = $path.$template_name;
		$new_filename = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Documents/Deleted/'.$template_name);
		
		// make sure the file, which has been passed via the url, isn't outside of $path.
		if(SmartestFileSystemHelper::isSafeFileName($old_filename, $path)){
		    
		    //// add bit in here that does the database work for container templates
		    if($template_type == 'SM_CONTAINER_TEMPLATE'){

    			if($asset->delete()){
    			    
    			    $this->addUserMessageToNextRequest('The template was successfully deleted.');
        			$this->setFormReturnVar('deletedTemplate', 'true');
    			}else{
    			    
    			    $this->addUserMessageToNextRequest('The template could not be deleted. Please check your file permissions.');
    			    $this->setFormReturnVar('deletedTemplate', 'failed');
    			}

    		}else{
    		    
    		    if(rename($old_filename, $new_filename)){

    		        $this->addUserMessageToNextRequest('The template was successfully deleted.');
        			$this->setFormReturnVar('deletedTemplate', 'true');

    		    }else{
    		        
    		        $this->addUserMessageToNextRequest('The template could not be deleted. Please check your file permissions.');
    			    $this->setFormReturnVar('deletedTemplate', 'failed');
        			
    		    }
    		    
    		}
		    
		    
			
		}else{
		    
		    $this->addUserMessageToNextRequest('The file you are trying to delete is outside the current editing scope.');
		    $this->setFormReturnVar('deletedTemplate', 'failed');
		    
		}
			
		$this->formForward();
	}
	
	
	
	
	function duplicateTemplate($get){
		
		$template_type = $get['type'];
		
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $get['template_name'];
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $get['template_name'];
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			$asset = new SmartestAsset;
			$asset->hydrate($get['template_id']);
			$template_name = $asset->getUrl();
			
			$new_asset = new SmartestAsset;
			// $new_asset->setTypeId(4);
			$new_asset->setType('SM_ASSETTYPE_CONTAINER_TEMPLATE');
			
		}
		
		$file = $path.$template_name;
		$new_file = SmartestFileSystemHelper::getUniqueFileName($file);
		
		// $oldtemplate_name = $get['template_name'];
		
		/// replace this with SmartestFileSystemHelper::getUniqueFileName();
		// $newtemplate_name = $this->manager->getUniqueFilename($path, $oldtemplate_name);
		
		if(copy($file, $new_file)){
		    
		    //// add bit in here that does the database work for container templates

    		if($template_type == 'SM_CONTAINER_TEMPLATE'){

    			$new_asset->setStringId(SmartestStringHelper::toVarName(basename($new_file)));
    			$new_asset->setUrl(basename($new_file));
    			$new_asset->setWebid(SmartestStringHelper::random(32));
    			$new_asset->save();

    		}
    		
			$this->setFormReturnVar('savedTheCopy', 'true');
			$this->addUserMessageToNextRequest('Your new copy was created successfully as '.basename($new_file).'.');
			
		}else{
			$this->setFormReturnVar('savedTheCopy', 'false');
			$this->addUserMessageToNextRequest('Couldn\'t create new copy. Please check file permissions.');
		} 
		
		$this->formForward();
	}
	
	function downloadTemplate($get){
		
		$template_type = $get['type'];
		
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $get['template_name'];
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $get['template_name'];
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			$asset = new SmartestAsset;
			$asset->hydrate($get['template_id']);
			$template_name = $asset->getUrl();
			
		}
		
		$file = $path.$template_name;
			
		$ua = $this->getUserAgent()->getAppName();
		
		if($ua == 'Explorer' || $ua == 'Opera'){
		    $mime_type = 'application/octetstream';
		}else{
		    $mime_type = 'application/octet-stream';
		}
		
		$download = new SmartestDownloadHelper($file);
		$download->setMimeType($mime_type);
		$download->send();
		
	}
}