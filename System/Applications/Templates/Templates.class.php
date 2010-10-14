<?php
  
include_once SM_ROOT_DIR."System/Applications/Assets/AssetsManager.class.php";

class Templates extends SmartestSystemApplication{

	private $AssetsManager;
	
	/* function __moduleConstruct(){
	    
		$this->AssetsManager = new AssetsManager();
		
	} */
	
	public function startPage(){          	
		
		$this->setTitle("Your Templates");
		$this->setFormReturnUri();
		
		$h = new SmartestTemplatesLibraryHelper;
		$types = $h->getTypes();
		$locations = $h->getUnWritableStorageLocations();
		$this->send($types, 'types');
		$this->send($locations, 'locations');
		
		$this->setFormReturnDescription('template types');
		
		$this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId()), 'recently_edited');
		
	}

	/* public function containerTemplates(){
		
		$this->setFormReturnUri();
		
		$templates = $this->AssetsManager->getAssetsByTypeCode("SM_ASSETTYPE_CONTAINER_TEMPLATE", $this->getSite()->getId());
		
		if(count($templates)){
			$this->send($templates, "assetList");
		}else {
		    $this->send(array(), "assetList");
			$this->send("No members of this type", "error");
		}
	} */
	
	public function listByType($get){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('templates');
	    $h = new SmartestTemplatesLibraryHelper;
	    $type_code = $get['type'];
	    $types = $h->getTypes();
	    
	    if(in_array($type_code, $h->getTypeCodes())){
	        
	        $type = $types[$type_code];
	        // echo ;
	        $this->send(is_writable(SM_ROOT_DIR.$type['storage']['location']), 'dir_is_writable');
	        $this->send($type, 'type');
	        $this->send(true, 'show_list');
	        
	        switch($type_code){
	            
	            case "SM_ASSETTYPE_MASTER_TEMPLATE":
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_MASTER_TEMPLATE'), 'recently_edited');
	            $templates = $h->getMasterTemplates($this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_CONTAINER_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_CONTAINER_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_CONTAINER_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_ITEMSPACE_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_ITEMSPACE_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_ITEMSPACE_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_ART_LIST_TEMPLATE":
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_ART_LIST_TEMPLATE'), 'recently_edited');
	            $templates = $h->getArticulatedListTemplates($this->getSite()->getId());
	            break;
	            
	        }
	        
	        $this->send($templates, 'templates');
	        $this->send(count($templates), 'count');
	    
        }
	    
	}
	
	/* public function listItemTemplates(){
	    
		$path = SM_ROOT_DIR.'Presentation/ListItems/'; 
		
		$this->setFormReturnUri();
		$templates = SmartestFileSystemHelper::getDirectoryContents($path, false, SM_DIR_SCAN_FILES);
		
		if(count($templates) > 0){
		    $this->send($templates, "templateList");
		}else {
		    $this->send(array(), "templateList");
		    $this->send("No members of this type", "error");
		}
		
	} */
	
	/* public function masterTemplates(){
		
		$this->setFormReturnUri();
		
		$h = new SmartestTemplatesLibraryHelper;
		$templates = $h->getMasterTemplates($this->getSite()->getId());
		
		if(count($templates)>0){
		    $this->send($templates, "templateList");
		}else {
		    $this->send(array(), "templateList");
		    $this->send("No members of this type", "error");
		}
		
	} */
	
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
	        // $files_array[$i]['suggested_name'] = SmartestStringHelper::removeDotSuffix($files_array[$i]['filename']);
	        $files_array[$i]['suggested_name'] = SmartestStringHelper::toTitleCaseFromFileName(SmartestStringHelper::removeDotSuffix($files_array[$i]['filename']));
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
	        $this->addUserMessage($message, SmartestUserMessage::WARNING);
	        $show_form = false;
	    }else if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
	        // $this->send($message, 'error_message');
	        $this->addUserMessage($message, SmartestUserMessage::WARNING);
	        $show_form = false;
	    }else{
	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$get['template']);
	        $force_shared = ($template->isInUseOnMultipleSites($get['asset_type']) || (count($template->getSitesWhereUsed()) > 0 && !in_array($this->getSite()->getId(), $template->getSiteIdsWhereUsed())));
	        $this->send($force_shared, 'force_shared');
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
                $success = SmartestFileSystemHelper::move($current_path, $move_to);
                $filename = SmartestFileSystemHelper::baseName($move_to);
            }else{
                $move_to = $current_path;
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
	
	public function convertTemplateType($get){
	    
	    $t = new SmartestTemplateAsset;
	    $id = (int) $get['template_id'];
	    
	    if($t->find($id)){
	        $this->send($t, 'template');
	        $is_convertable = $t->isConvertable();
	        if($is_convertable){
	            $alh = new SmartestAssetsLibraryHelper;
	            $possible_types = $alh->getSelectedTypes($t->getTypeConvertOptions());
	            $this->send(array_values($possible_types), 'possible_types');
	            $this->send($t->getTypeInfo(), 'current_type');
	        }else{
	            
	        }
	        $this->send($is_convertable, 'is_convertable');
	    }else{
	        $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function updateTemplateType($get, $post){
	    
	    $template_id = (int) $post['template_id'];
	    $t = new SmartestTemplateAsset;
	    
	    if($t->find($template_id)){
	        if($t->isConvertable()){
	            
	            $h = new SmartestTemplatesLibraryHelper;
	            $types = $h->getTypes();
	            
	            if(array_key_exists($post['new_type'], $types)){
	                $t->setType($post['new_type']);
	                $t->save();
	                $this->addUserMessageToNextRequest("The template was successfully converted to type: \"".$types[$post['new_type']]['label']."\"", SmartestUserMessage::SUCCESS);
                }else{
                    $this->addUserMessageToNextRequest("The new type was not recognized", SmartestUserMessage::ERROR);
                }
	            
	        }else{
	            $this->addUserMessageToNextRequest("This template cannot be converted at the moment.", SmartestUserMessage::WARNING);
	        }
	    }else{
	        $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function addTemplate($get){
		
		// $type = (in_array($get['type'], array('SM_PAGE_MASTER_TEMPLATE', 'SM_LIST_ITEM_TEMPLATE', 'SM_CONTAINER_TEMPLATE'))) ? $get['type'] : 'SM_PAGE_MASTER_TEMPLATE';
		
		$h = new SmartestTemplatesLibraryHelper;
		$types = $h->getTypes();
		
		if(isset($get['type'])){
		
		    $type_id = $get['type'];
    		$type = $types[$type_id];
		
    		$title = "Add a new ".strtolower($type['label']);
    		$path = SM_ROOT_DIR.$type['storage']['location'];
    		$this->send(true, 'type_specified');
		    
		    $this->send($type, 'template_type');
		    
		/* switch($type){
		    case "SM_PAGE_MASTER_TEMPLATE":
		    case "SM_ASSETTYPE_MASTER_TEMPLATE":
		    $title = "Add a Page Master Template";
		    $path = SM_ROOT_DIR."Presentation/Masters/";
		    break;
		    
		    case "SM_LIST_ITEM_TEMPLATE":
		    case "SM_ASSETTYPE_ART_LIST_TEMPLATE":
		    $title = "Add a List Item Template";
		    $path = SM_ROOT_DIR."Presentation/ListItems/";
		    break;
		    
		    case "SM_CONTAINER_TEMPLATE":
		    case "SM_ASSETTYPE_CONTAINER_TEMPLATE":
		    $title = "Add a Container Template";
		    $path = SM_ROOT_DIR."Presentation/Layouts/";
		    break;
		} */
		
		    $allow_save = is_writable($path);
		    $this->send($path, 'path');
		    $this->send(SmartestStringHelper::toVarName('untitled '.$type['label']), 'default_name');
		
	    }else{
	        
	        $this->send($types, 'types');
	        $title = "Add a new template";
	        $this->send(false, 'type_specified');
	        $this->send('untitled_template', 'default_name');
	        
	        $allow_save = false;
	        
	        foreach($types as $t){
	            if($t['storage']['writable']){
	                $allow_save = true;
	                break;
	            }
	        }
	        
	    }
		
		$this->setTitle($title);
		
		$formTemplateInclude = "addTemplate.tpl";
		$this->send($allow_save, 'allow_save');
		$this->send($title, 'interface_title');
		
	}
	
 	function saveNewTemplate($get, $post){
		
		$h = new SmartestTemplatesLibraryHelper;
		$types = $h->getTypes();
		
		// $template_type = $post['template_type'];
		$type_id = $post['template_type'];
		
		if(isset($types[$type_id])){
		    
		    $type = $types[$type_id];
		
    		$path = SM_ROOT_DIR.$type['storage']['location'];
		
    		if($post['add_type'] == "DIRECT"){
		    
    			$content  = $post['template_content'];
			
    			if(substr($content, 0, 9) == '<![CDATA['){
    			    $content = substr($content, 9);
    			}
			
    			if(substr($content, -3) == ']]>'){
    			    $content = substr($content, 0, -3);
    			}
			
    			$stringid = SmartestStringHelper::toVarName($post['template_name']);
    			$file     = $post['template_name'];
			
    			if(SmartestStringHelper::getDotSuffix($file) != 'tpl'){
    			    $file = SmartestFileSystemHelper::removeDotSuffix($stringid).'.tpl';
    			}
			    
			    // var_dump($stringid);
			    
    			$full_filename = SmartestFileSystemHelper::getUniqueFileName($path.$file);
    			$final_filename = basename($full_filename);
			
    		}elseif($post['add_type'] == "UPLOAD"){
		    
    		    $uploader = new SmartestUploadHelper('template_upload');
    			$uploader->setUploadDirectory($path);
			    
    			if(!$uploader->hasDotSuffix('tpl')){
        			$uploader->setFileName(SmartestStringHelper::toVarName(SmartestFileSystemHelper::removeDotSuffix($uploader->getFileName())).".tpl");
        		}
    		
        		$final_filename = $uploader->getFileName();
        		$full_filename = $path.$final_filename;
			
    		}
		
    		$new_template = new SmartestTemplateAsset;
		
    		$new_template->setType($type_id);
    		$new_template->setCreated(time());
    		$new_template->setStringid($stringid);
    		$new_template->setWebid(SmartestStringHelper::random(32));
    		$new_template->setUrl($final_filename);
    		$new_template->setSiteId($this->getSite()->getId());
    		$shared = (isset($post['template_shared']) && $post['template_shared']) ? 1 : 0;
    		$new_template->setShared($shared);
		
    		// $this->addUserMessage($full_filename);
		
    		if($post['add_type'] == "DIRECT"){
			
    			if(SmartestFileSystemHelper::save($full_filename, stripslashes($post['template_content']), true)){
    				// $this->setFormReturnVar('savedNewTemplate', 'true');
    				$this->addUserMessageToNextRequest('The file was saved successfully', SmartestUserMessage::SUCCESS);
    				$file_success = true;
    			}else{
    				// $this->setFormReturnVar('savedNewTemplate', 'failed');
    				$this->addUserMessageToNextRequest('There was a problem creating the file', SmartestUserMessage::WARNING);
    				$file_success = false;
    			}
			
    		}else if($post['add_type'] == "UPLOAD"){
		
    		    if($uploader->save()) { // Move the file over
    			    // $this->setFormReturnVar('savedNewTemplate', 'true');
    			    $this->addUserMessageToNextRequest('The file was saved successfully', SmartestUserMessage::SUCCESS);
    			    $file_success = true;
    		    }else{ // Couldn't save the file
    		        $this->addUserMessageToNextRequest('There was a problem creating the file', SmartestUserMessage::WARNING);
    		        $file_success = false;
    		    }
		
    		}
    		
    	    if($file_success){
    	        // Add the template asset to the database
    	        $new_template->save();
    	    }
		
	    }else{
	        
	        $this->addUserMessageToNextRequest('The given template type code was not recognized', SmartestUserMessage::ERROR);
	        
	    }
		
		$this->formForward();
	}
			
	public function editTemplate($get){
		
		$template_type = $get['type'];
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $get['asset_type'];
	    $template_id = $get['template'];
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $this->send($template, 'template');
	            $this->send($template->getContentForEditor(), "template_content");
	            $show_form = true;
	            $this->send($type, 'type');
	            $this->send('asset', 'edit_type');
	            $location = $template->getStorageLocation();
	            $dir_is_writable = is_writable(SM_ROOT_DIR.$location);
	            $file_is_writable = is_writable(SM_ROOT_DIR.$location.$template->getUrl());
        		$title = 'Edit '.$type['label'];
        		$this->send($type, 'type_info');
        		$this->send($template->IsConvertable(), 'is_convertable');
        		$this->send($template->getImportedStylesheets(), 'stylesheets');
        		$template->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
        		$this->getUser()->addRecentlyEditedTemplateById($template_id, $this->getSIte()->getId());
        		$this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), $template->getType()), 'recently_edited');
        		
	        }else{
	            $this->addUserMessage("The template ID was not recognized");
	            $show_form = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        // && SmartestFileSystemHelper::isSafeFileName($file, $path)
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessage($message, SmartestUserMessage::ERROR);
         	        $show_form = false;
         	    }else{
         	        $path = realpath(SM_ROOT_DIR.$location.$template_id);
         	        if(SmartestFileSystemHelper::isSafeFileName($path, SM_ROOT_DIR.$location)){
         	            $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
             	        $this->send($template, 'template');
             	        $this->send($template->getContentForEditor(), 'template_content');
             	        $location = $template->getStorageLocation();
        	            $dir_is_writable = is_writable(SM_ROOT_DIR.$location);
        	            $file_is_writable = is_writable(SM_ROOT_DIR.$location.$template->getUrl());
        	            $this->send('file', 'edit_type');
             	        $show_form = true;
             	        $this->send(false, 'is_convertable');
             	        $this->send(array(), 'stylesheets');
     	            }else{
     	                $show_form = false;
     	                $this->send(false, 'is_convertable');
     	                $this->addUserMessage("You are not allowed to edit that file. This incident has been logged.", SmartestUserMessage::ACCESS_DENIED);
     	            }
         	    }
     	        
 	        }else{
 	            // type not recognized
 	        }
	        
	    }
	    
	    $is_editable = $show_form && $dir_is_writable && $file_is_writable;
        
	    $this->send($dir_is_writable, 'dir_is_writable');
		$this->send($file_is_writable, 'file_is_writable');
	    $this->send($title, "interface_title");
	    $this->send($show_form, "show_form");
	    $this->send($is_editable, "is_editable");
	    
	    /* if(in_array($type_code, $h->getTypeCodes())){
		
		    $type = $types[$type_code];
	        $this->send($type, 'type');
	        $this->send(true, 'show_form');
	        $this->send('Edit '.strtolower($type['label']), 'show_form');
		
		    switch($type_code){
		    
		        case 'SM_PAGE_MASTER_TEMPLATE':
			
			    $path = SM_ROOT_DIR."Presentation/Masters/";
			
			
		
		}else if($template_type == 'SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $get['template_name'];
			$title = 'Edit list item template';
			$this->setFormReturnUri();
			
		}else if($template_type == 'SM_CONTAINER_TEMPLATE'){
    		
    		$path = SM_ROOT_DIR."Presentation/Layouts/";
    		$template = new SmartestAsset;
    		$title = 'Edit container template';
    		$this->setFormReturnUri();
    		
    		if($template->hydrate($get['template_id'])){
    		    $template_name = $template->getUrl();
    		}
    		
    		$this->send($template->getId(), "template_id");
    		
    	}else{
    	    $this->addUserMessage('The template type is invalid.', SmartestUserMessage::ERROR);
    	} */
		
		// $file = $path.$template_name;
		
		// make sure the file exists and is in the right place. only templates in the $path directory should be readable.
		/* if(is_file($file) && SmartestFileSystemHelper::isSafeFileName($file, $path)){
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
		$this->send($show_form, "show_form"); */

	}
	
	function updateTemplate($get, $post){
		
		$h = new SmartestTemplatesLibraryHelper;
		$edit_type = $post['edit_type'];
		$type_code = $post['type'];
		
		if($edit_type == 'imported'){
	        
	        $template_id = (int) $post['template_id'];
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            
	            if(is_writable($template->getStorageLocation(true)) && is_writable($template->getFullPathOnDisk())){
	                $allow_update = true;
                }else{
                    $allow_update = false;
                    $this->addUserMessageToNextRequest("The file cannot be written. Please check permissions.", SmartestUserMessage::WARNING);
                }
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	            $allow_update = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        $filename = $post['filename'];
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);
    	        
         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        
         	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_update = false;
         	        
         	    }else{
         	        
         	        // echo SM_ROOT_DIR.$location.$filename.' ';
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$filename);
         	        
         	        // echo $template->getStorageLocation(true).' ';
         	        
         	        if(is_writable($template->getStorageLocation(true)) && is_writable($template->getFullPathOnDisk())){
    	                $allow_update = true;
                    }else{
                        $allow_update = false;
                        $this->addUserMessageToNextRequest("The file cannot be written. Please check permissions.", SmartestUserMessage::WARNING);
                    }
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_update = false;
 	        }
	        
	    }
	    
	    if($allow_update){
	        
	        $content = $post['template_content'];

    		if(substr($content, 0, 9) == '<![CDATA['){
    		    $content = substr($content, 9);
    		}

    		if(substr($content, -3) == ']]>'){
    		    $content = substr($content, 0, -3);
    		}

    		$template_content = stripslashes($content);
    		
    		SmartestFileSystemHelper::save($template->getFullPathOnDisk(), $template_content, true);
    		
    		if($edit_type == 'imported'){
    		    $template->setModified(time());
    		    $template->save();
    		}
	        
	    }
	    
	    if($post['_submit_action'] == "continue"){
	        if($edit_type == 'imported'){
	            $url_id = $template->getId();
	        }else{
	            $url_id = $post['filename'];
	        }
	        $this->redirect("/templates/editTemplate?asset_type=".$type_code."&template=".$url_id);
	    }else{
	        $this->addUserMessageToNextRequest("The template was successfully saved", SmartestUserMessage::SUCCESS);
	        $this->formForward();
	    }
		
		/* $data = $post;
		unset($data['template_content']);
		print_r($data); */
		
		/* $template_type = $post['type'];
		
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
  		
  		$this->formForward(); */
  		
	}
	
	function templateInfo($get){
	    
	    $template_id = $get['template'];
	    
	    $template = new SmartestTemplateAsset;

		if($template->find($template_id)){
		    
		    $this->setFormReturnUri();
		    
		    // $data = $asset;
		    
		    // $comments = $asset->getComments();
		    // $this->send($comments, 'comments');
		    // $this->send($asset->getGroups(), 'groups');
		    // $this->send($asset->getPossibleGroups(), 'possible_groups');
		    
		    $this->send($template->getTypeInfo(), 'asset_type');
		    $this->send($template, 'asset'); 
		    
		}
	    
	}

	function deleteTemplate($get){
	    
	    $template_type = $get['type'];
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $get['asset_type'];
	    $template_id = $get['template'];
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $allow_delete = true;
	            $location = $template->getStorageLocation();
	            $delete_type = 'imported';
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized");
	            $allow_delete = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_delete = false;
         	    }else{
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        // $location = $template->getStorageLocation();
    	            $allow_delete = true;
    	            $delete_type = 'file';
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_delete = false;
 	        }
	        
	    }
		
		if($allow_delete){
		
		    $new_file = SmartestFileSystemHelper::getUniqueFileName($template->getFullPathOnDisk());
		    
		    if($template->delete()){
                $this->addUserMessageToNextRequest('The template was deleted successfully.', SmartestUserMessage::SUCCESS);
    		}else{
    			$this->addUserMessageToNextRequest('Couldn\'t create new copy. Please check file permissions.', SmartestUserMessage::WARNING);
    		}
		
	    }
		
		$this->formForward();
			
		/* $template_type = $get['type'];
			
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
			
		$this->formForward(); */
	}
	
	
	
	
	function duplicateTemplate($get){
		
		$template_type = $get['type'];
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $get['asset_type'];
	    $template_id = $get['template'];
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $allow_copy = true;
	            $location = $template->getStorageLocation();
	            $copy_type = 'imported';
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized");
	            $allow_copy = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_copy = false;
         	    }else{
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        // $location = $template->getStorageLocation();
    	            $allow_copy = true;
    	            $copy_type = 'file';
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_copy = false;
 	        }
	        
	    }
		
		if($allow_copy){
		
		    $new_file = SmartestFileSystemHelper::getUniqueFileName($template->getFullPathOnDisk());
		    
		    if(copy($template->getFullPathOnDisk(), $new_file)){
                
                if($copy_type == 'imported'){
                    
                    $new_asset = new SmartestTemplateAsset;
                    $new_asset->setType($template->getType());
        		    $new_asset->setStringId(SmartestStringHelper::toVarName(basename($new_file)));
    			    $new_asset->setUrl(basename($new_file));
    			    $new_asset->setWebid(SmartestStringHelper::random(32));
    			    $new_asset->setCreated(time());
    			    $new_asset->setUserId($this->getUser()->getId());
    			    $new_asset->setSiteId($this->getSite()->getId());
    			    $new_asset->save();
    			    
			    }

    			$this->addUserMessageToNextRequest('Your new copy was created successfully as '.basename($new_file).'.', SmartestUserMessage::SUCCESS);

    		}else{
    			$this->addUserMessageToNextRequest('Couldn\'t create new copy. Please check file permissions.', SmartestUserMessage::WARNING);
    		}
		
	    }
		
		$this->formForward();
		
	}
	
	function downloadTemplate($get){
		
		$template_type = $get['type'];
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $get['asset_type'];
	    $template_id = $get['template'];
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $allow_download = true;
	            $location = $template->getStorageLocation();
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized");
	            $allow_download = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$get['asset_type']." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_download = false;
         	    }else{
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        $location = $template->getStorageLocation();
    	            $allow_download = true;
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_download = false;
 	        }
	        
	    }
		
		if($allow_download){
		
		    $ua = $this->getUserAgent()->getAppName();
		
    		if($ua == 'Explorer' || $ua == 'Opera'){
    		    $mime_type = 'application/octetstream';
    		}else{
    		    $mime_type = 'application/octet-stream';
    		}
		
    		$download = new SmartestDownloadHelper($template->getFullPathOnDisk());
    		$download->setMimeType($mime_type);
    		$download->send();
		
	    }else{
	        
	        $this->formForward();
	        
	    }
		
	}
}