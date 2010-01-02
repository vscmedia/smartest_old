<?php

/**
 *
 * PHP versions 4,5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Marcus Gilroy-Ware <marcus@visudo.com>
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

// phpinfo();

require_once 'XML/Serializer.php';

class Assets extends SmartestSystemApplication{
	
	public function getAssetClasses($get){
		
	}
	
	public function getAssetTypes(){
	    
	    $h = new SmartestAssetsLibraryHelper;
		
		$this->setTitle("Files");
		$this->setFormReturnUri(); // set the url of the page to be return to
		$this->setFormReturnDescription('file types');
		
		$assetTypes_old = $this->manager->getAssetTypes();
		$assetTypes = $h->getTypesByCategory(array('templates'));
		
		$this->send($assetTypes, "assetTypeCats");
		
		$recent = $this->getUser()->getRecentlyEditedAssets($this->getSite()->getId());
        $this->send($recent, 'recent_assets');
		
	}
	
	public function addPlaceholder($get){
		
		$asset_class_types = SmartestDataUtility::getAssetClassTypes();
		
		$placeholder_name = SmartestStringHelper::toVarName($get['name']);
		
		$this->send($placeholder_name, 'name');
		$this->send($asset_class_types, 'types');
		
	}
	
	public function addContainer($get){
		
		$container_name = SmartestStringHelper::toVarName($get['name']);
		
		$this->send($container_name, 'name');
		$this->send($asset_class_types, 'types');
		
	}
	
	public function insertPlaceholder($get, $post){
		
		$placeholder = new SmartestPlaceholder;
		
		if($post['placeholder_name']){
		    $name = SmartestStringHelper::toVarName($post['placeholder_name']);
		}else{
		    $name = SmartestStringHelper::toVarName($post['placeholder_label']);
		}
		
		if($placeholder->exists($name, $this->getSite()->getId())){
	        $this->addUserMessageToNextRequest("A placeholder with the name \"".$name."\" already exists.", SmartestUserMessage::WARNING);
	    }else{
		    $placeholder->setLabel($post['placeholder_label']);
		    $placeholder->setName($name);
		    $placeholder->setSiteId($this->getSite()->getId());
		    $placeholder->setType($post['placeholder_type']);
		    $placeholder->save();
		    $this->addUserMessageToNextRequest("A new container with the name \"".$name."\" has been created.", SmartestUserMessage::SUCCESS);
		}
		
		$this->formForward();
	}
	
	public function insertContainer($get, $post){
		
		if($post['container_name']){
		    $name = SmartestStringHelper::toVarName($post['container_name']);
		}else{
		    $name = SmartestStringHelper::toVarName($post['container_label']);
		}
		
		$container = new SmartestContainer;
		
		if($container->exists($name, $this->getSite()->getId())){
	        $this->addUserMessageToNextRequest("A container with the name \"".$name."\" already exists.", SmartestUserMessage::WARNING);
	    }else{
		    $container->setLabel($post['container_label']);
		    $container->setName($name);
		    $container->setSiteId($this->getSite()->getId());
		    $container->setType('SM_ASSETCLASS_CONTAINER');
		    $container->save();
		    $this->addUserMessageToNextRequest("A new container with the name \"".$name."\" has been created.", SmartestUserMessage::SUCCESS);
	    }
		
		$this->formForward();
	}
	
	public function getAssetTypeMembers($get){
		
		$code = strtoupper($get["asset_type"]);
		$mode = isset($get["mode"]) ? (int) $get["mode"] : 1;
		
		$this->send($mode, 'mode');
		
		$this->setFormReturnUri();
		
		if($this->manager->getIsValidAssetTypeCode($code)){
			$assets = $this->manager->getAssetsByTypeCode($code, $this->getSite()->getId(), $mode);	
		}
		
		$types_array = SmartestDataUtility::getAssetTypes();
		
		if(in_array($code, array_keys($types_array))){
		    
		    $type = $types_array[$code];
		    $this->send('editableasset', 'sidebartype');
		    
		    if(isset($type['source_editable']) && SmartestStringHelper::toRealBool($type['source_editable'])){
		        $this->send(true, 'allow_source_edit');
		    }else{
		        $this->send(false, 'allow_source_edit');
		    }
		    
		    $this->send($type, 'type');
		    
		    $recent = $this->getUser()->getRecentlyEditedAssets($this->getSite()->getId(), $code);
  	        $this->send($recent, 'recent_assets');
		    
		}else{
		    $this->send('noneditableasset', 'sidebartype');
		}
		
		$this->send($type['label'], 'type_label');
		$this->send($type['id'], 'type_code');
		$this->send(count($assets), 'num_assets');
		
		if(count($assets) > 0){
		    $this->send($assets, 'assets');
		    $this->setTitle($type['label']." Files");
		    $this->setFormReturnDescription('files');
		}else {
			return array("error"=>"No members of this type");
		}
	}
	
	public function toggleAssetArchived($get){
	    
	    $a = new SmartestAsset;
	    
	    if($a->hydrate((int) $get['asset_id'])){
	        
	        if($a->getIsArchived() == 1){
	            $a->setIsArchived(0);
	        }else if($a->getIsArchived() == 0){
	            $a->setIsArchived(1);
	        }
	        
	        $a->save();
	    }
	    
	    $this->formForward();
	    
	}
	
	public function detectNewUploads(){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $database = SmartestPersistentObject::get('db:main');
	    
	    // first, get the folders where uploads will be found, and match those to types
	    $location_types = $h->getTypeCodesByStorageLocation($h->getNonImportableCategoryNames());
	    $locations = array_keys($location_types);
	    $types = $h->getTypes();
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
            $result = $database->queryToArray($sql);
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
	
	public function enterNewFileData($get, $post){
	    
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
	        
	        /* if(count($types)){
	            $files_array[$i]['possible_groups'] = $alh->getAssetGroupsThatAcceptType($types[0]['type']['id']);
            } */
	        $files_array[$i]['size'] = SmartestFileSystemHelper::getFileSizeFormatted(SM_ROOT_DIR.$f);
	        // $files_array[$i]['correct_directory'] = $type['storage']['location'];
	        $i++;
	    }
	    
	    $this->send($files_array, 'files');
	    
	}
	
	public function createAssetsFromNewUploads($get, $post){
	    
	    if(isset($post['new_files']) && is_array($post['new_files'])){
	        $new_files = $post['new_files'];
	    }else{
	        $new_files = array();
	    }
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $asset_types = $h->getTypes();
	    
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
	            // The file type has been recognized by its file suffix, but needs to be moved to the right place (so needs to be moved - user has been warned about this)
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
	        
    	        /* if(isset($nf['group']) && is_numeric($nf['group'])){
    	            $a->addToGroupById($nf['group'], true);
    	        } */
            }
	        
	    }
	    
	    $this->addUserMessageToNextRequest(count($new_files)." file".((count($new_files) == 1) ? " was " : "s were ")."successfully added to the repository.", SmartestUserMessage::SUCCESS);
	    $this->formForward();
	    
	}
	
	public function addAsset($get){
		
		$asset_type = SmartestStringHelper::toConstantName($get['asset_type']);
		
		$types_array = SmartestDataUtility::getAssetTypes();
		
		if(in_array($asset_type, array_keys($types_array))){
		    
		    $type = $types_array[$asset_type];
		    $this->setTitle("Add a new ".$type['label']);
		    $this->send($type['id'], 'type_code');
		    $this->send($type, 'new_asset_type_info');
		    
		    $alh = new SmartestAssetsLibraryHelper;
		    $possible_groups = $alh->getAssetGroupsThatAcceptType($asset_type, $this->getSite()->getId());
		    $this->send($possible_groups, 'possible_groups');
		    
		    if(isset($type['param'])){

    	        $raw_xml_params = $type['param'];

    	        foreach($raw_xml_params as $rxp){
    	            if(isset($rxp['default'])){
    	                $params[$rxp['name']] = $rxp['default'];
                    }else{
                        $params[$rxp['name']] = '';
                    }
    	        }
    	        
    	    }else{
    	        $params = array();
    	    }
		    
		    $suffixes = array();
		    
		    $this->send($params, 'params');
		    
		    if(is_array($type['suffix'])){
		        foreach($type['suffix'] as $s){
		            $suffixes[] = $s['_content'];
		        }
		    }
		    
		    if($type['storage']['type'] == 'database' || $type['category'] == "browser_instructions"){
		        $starting_mode = 'direct';
		    }else{
		        $starting_mode = 'upload';
		    }
		    
		    if($type['category'] != 'image'){
		        $form_include = "add.".strtolower(substr($asset_type, 13)).".tpl";
	        }else{
	            $form_include = "add.image.tpl";
	        }
	        
	        if($type['storage']['type'] != 'database'){
	            $path = SM_ROOT_DIR.$type['storage']['location'];
	            $allow_save = is_writable($path);
	            $this->send($allow_save, 'allow_save');
	            $this->send($path, 'path');
            }else{
                $this->send(true, 'allow_save');
            }
	        
	        $this->send($starting_mode, 'starting_mode');
	        $this->send(json_encode($suffixes), 'suffixes');
	        
		}else{
		    $this->send($asset_type, 'wanted_type');
		    $this->setTitle("Asset Type Not Recognized.");
		    $form_include = "add.default.tpl";
		}
		
		$this->send($form_include, 'form_include');
		
	}
	
	
	public function saveNewAsset($get, $post){
	    
	    if($this->getUser()->hasToken('create_assets')){
	    
	        $asset_type = $post['asset_type'];
	    
    	    $everything_ok = true;
	    
    	    $types_array = SmartestDataUtility::getAssetTypes();
		
    		if(in_array($asset_type, array_keys($types_array))){
		    
    		    $type = $types_array[$asset_type];
		    
    		    $asset = new SmartestAsset;
    		    $asset->setType($asset_type);
    		    $asset->setSiteId($this->getSite()->getId());
    		    $shared = isset($post['asset_shared']) ? 1 : 0;
    		    $asset->setShared($shared);
    		    $asset->setUserId($this->getUser()->getId());
    		    $asset->setCreated(time());
    		    $asset->setLanguage(strtolower(substr($post['asset_language'], 0, 3)));
		    
    		    $suffixes = array();
		    
    		    if(is_array($type['suffix'])){
    		        foreach($type['suffix'] as $s){
    		            $suffixes[] = $s['_content'];
    		        }
    		    }
		    
    		    if($post['input_mode'] == 'direct'){
		        
    		        // create filename
    		        if(isset($post['new_filename']) && strlen($post['new_filename'])){
		            
    		            if(in_array(SmartestStringHelper::getDotSuffix($post['new_filename']), $suffixes)){
    		                $filename = $post['new_filename'];
    		                $string_id = SmartestStringHelper::toVarName($post['new_filename']);
    		            }else{
    		                $filename = SmartestStringHelper::toVarName($post['new_filename']).'.'.$suffixes[0];
    		                $string_id = SmartestStringHelper::toVarName($post['new_filename']);
    		            }
		            
    		            if(isset($post['string_id']) && strlen($post['string_id'])){

        		            $string_id = SmartestStringHelper::toVarName($post['string_id']);

        		        }
		            
    		        }else if(isset($post['string_id']) && strlen($post['string_id'])){
		            
    		            if(in_array(SmartestStringHelper::getDotSuffix($post['string_id']), $suffixes)){
    		                $filename = $post['string_id'];
    		                $string_id = SmartestStringHelper::toVarName($post['string_id']);
    		            }else{
    		                $filename = SmartestStringHelper::toVarName($post['string_id']).'.'.$suffixes[0];
    		                $string_id = SmartestStringHelper::toVarName($post['string_id']);
    		            }
		            
		            
    		        }else{
    		            $this->addUserMessageToNextRequest("Error: Neither a file name nor a string_id were provided.", SmartestUserMessage::WARNING);
    		            SmartestLog::getInstance('site')->log('Neither a file name nor a string_id were provided when adding a new file.', SmartestLog::WARNING);
    		            $everything_ok = false;
    		        }
		        
    		        $asset->setStringid($string_id);
		        
    		        $content = $post['content'];
		        
    		        $new_temp_file = SM_ROOT_DIR.'System/Temporary/'.md5(microtime(true)).'.tmp';
    		        SmartestFileSystemHelper::save($new_temp_file, $content, true);
		        
    		        if($type['storage']['type'] == 'database'){
                    
                        // add contents of file in System/Temporary/ to database as a text fragment
                        $asset->getTextFragment()->setContent(SmartestFileSystemHelper::load($new_temp_file, true));
                        $asset->setUrl($filename);
                    
        		    }else{
    		        
        		        $intended_file_name = SM_ROOT_DIR.$type['storage']['location'].$filename;
        		        $final_file_name = SmartestFileSystemHelper::getUniqueFileName($intended_file_name);
        		        SmartestFileSystemHelper::save($final_file_name, '');
    		        
        		        // $new_temp_file
        		        // copy the file from System/Temporary/ to the location dictated by the Type
        		        // delete copy in System/Temporary/ if necessary
    		            
    		            if(is_file($new_temp_file)){
    		            
        		            if(!SmartestFileSystemHelper::move($new_temp_file, $final_file_name)){
            		            $everything_ok = false;
            		            $message = sprintf("Could not move %s to %s. Please check file permissions.", basename($new_temp_file), basename($final_file_name));
            		            $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
            		            SmartestLog::getInstance('site')->log($message, SmartestLog::ERROR);
            		            SmartestLog::getInstance('site')->log("File that failed to move to final location is still stored at: ".$new_temp_file, SmartestLog::NOTICE);
            		        }else{
            		            $asset->setUrl(basename($final_file_name));
            		            $asset->setWebid(SmartestStringHelper::random(32));
            		        }
        		        
    		            }else{
    		                
    		                SmartestLog::getInstance('site')->log("Temporary upload ".$new_temp_file." was unexpectedly not created.", SmartestLog::ERROR);
    		                
    		            }
        		    }
		        
    		    }else{ // The new asset is being uploaded
		        
    		        // create upload helper
    		        $upload = new SmartestUploadHelper('new_file');
    		        $upload->setUploadDirectory(SM_ROOT_DIR.'System/Temporary/');
		        
    		        if(!$upload->hasDotSuffix($suffixes)){
            			$upload->setFileName(SmartestStringHelper::toVarName($upload->getFileName()).'.'.$suffixes[0]);
            		}
        		
    		        // create filename based on existing filename
    		        $raw_filename = $upload->getFileName();
    		        $filename = SmartestStringHelper::toSensibleFileName($raw_filename);
		        
    		        // give it hashed name for now and save it to disk
    		        $upload->setFileName(md5(microtime(true)).'.tmp');
    		        $r = $upload->save();
    		        
    		        $new_temp_file = SM_ROOT_DIR.'System/Temporary/'.$upload->getFileName();
		        
    		        // create string id based on actual file name
    		        $string_id = SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix($filename));
    		        $asset->setStringid($string_id);
		        
    		        if($type['storage']['type'] == 'database'){
		            
    		            // if storage type is database, save the file to System/Temporary/ and get its contents
    		            $content = SmartestFileSystemHelper::load($new_temp_file, true);
    		            $asset->getTextFragment()->setContent($content);
    		            $asset->setUrl($filename);
		            
    		        }else{
		            
    		            // if storage type is file, save the upload in the location dictated by the Type
    		            // echo $intended_file_name;
		            
        		        $intended_file_name = SM_ROOT_DIR.$type['storage']['location'].$filename;
        		        $final_file_name = SmartestFileSystemHelper::getUniqueFileName($intended_file_name);
        		        
        		        if(is_file($new_temp_file)){
    		        
        		            if(!SmartestFileSystemHelper::move($new_temp_file, $final_file_name)){
            		            $everything_ok = false;
            		            // $this->addUserMessageToNextRequest(sprintf("Could not move %s to %s. Please check file permissions.", basename($new_temp_file), basename($final_file_name)), SmartestUserMessage::ERROR);
            		            // $this->addUserMessageToNextRequest(sprintf("Could not move %s to %s. Please check file permissions.", $new_temp_file, $final_file_name));
            		            $message = sprintf("Could not move %s to %s. Please check file permissions.", basename($new_temp_file), basename($final_file_name));
            		            $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
            		            SmartestLog::getInstance('site')->log($message, SmartestLog::ERROR);
            		            SmartestLog::getInstance('site')->log("File that failed to move to final location is still stored at: ".$new_temp_file, SmartestLog::NOTICE);
            		        }else{
            		            $asset->setUrl(basename($final_file_name));
            		        }
        		        
        		        }else{
    		                
    		                $everything_ok = false;
    		                $this->addUserMessageToNextRequest("Temporary upload ".$new_temp_file." was unexpectedly not created.", SmartestUserMessage::ERROR);
    		                SmartestLog::getInstance('site')->log("Temporary upload ".$new_temp_file." was unexpectedly not created.", SmartestLog::ERROR);
    		                
    		            }
    		        
    		        }
		        
    		    }
		    
    		    $asset->setWebid(SmartestStringHelper::random(32));
		    
    		    if(isset($post['params']) && is_array($post['params'])){
    		        $param_values = serialize($post['params']);
    		        $asset->setParameterDefaults($param_values);
    	        }
		    
    		    if($everything_ok){
    		        $asset->setCreated(time());
    		        $this->getUser()->addRecentlyEditedAssetById($asset->getId(), $this->getSite()->getId());
    		        $asset->save();
    		        
    		        if(strlen($post['initial_group_id']) && is_numeric($post['initial_group_id'])){
    		            $group = new SmartestAssetGroup;
    		            
    		            if($group->find($post['initial_group_id'])){
    		                $asset->addToGroupById($post['initial_group_id'], true);
    		                $message = sprintf("The file was successfully saved as '%s' and added to group '%s'.", $asset->getUrl(), $group->getLabel());
    		                $status = SmartestUserMessage::SUCCESS;
    		            }else{
    		                $message = sprintf("The file was successfully saved as '%s', but the selected group ID was not recognized.", $asset->getUrl());
    		                $status = SmartestUserMessage::INFO;
    		            }
    		            
    		        }else{
    		            $message = sprintf("The file was successfully saved as '%s'", $asset->getUrl());
    		            $status = SmartestUserMessage::SUCCESS;
    		        }
    		        
    		        $this->addUserMessageToNextRequest($message, $status);
    		        SmartestLog::getInstance('site')->log($this->getUser().' created file: '.$asset->getUrl(), SmartestLog::USER_ACTION);
    		    }else{
    		        $this->addUserMessageToNextRequest("There was an error creating the new file.", SmartestUserMessage::ERROR);
    		        SmartestLog::getInstance('site')->log("There was an error creating the new file.", SmartestLog::ERROR);
    		    }
		    
    		    $this->formForward();
		    
    		}else{
		    
    		    $this->addUserMessageToNextRequest("The asset type was not recognized.", SmartestUserMessage::ERROR);
    		    SmartestLog::getInstance('site')->log("The asset type was not recognized.", SmartestLog::ERROR);
    		    $this->formForward();
		    
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't currently have permission to add new files.", SmartestUserMessage::ACCESS_DENIED);
	        SmartestLog::getInstance('site')->log("You don't currently have permission to add new files.", SmartestLog::ACCESS_DENIED);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function assetGroups(){
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    $groups = $alh->getAssetGroups($this->getSite()->getId());
	    
	    $this->send($groups, 'groups');
	    
	}
	
	public function assetGroupsByType($get){
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    
	    $code = $get['asset_type'];
	    
	    $types_array = SmartestDataUtility::getAssetTypes();
		
		if(in_array($code, array_keys($types_array))){
		    
		    $groups = $alh->getAssetGroupsThatAcceptType($get['asset_type'], $this->getSite()->getId());

    	    $this->send($groups, 'groups');
    	    $this->send($get['asset_type'], 'type_code');
    	    $this->send($types_array[$code], 'type');
    	    
    	    // print_r($groups);
		    
		}else{
		    $this->addUserMessageToNextRequest('The file type was not recognized.', SmartestUserMessage::ERROR);
		}
	    
	}
	
	public function newAssetGroup($get){
	    
	    $asset_types = SmartestDataUtility::getAssetTypes();
	    $placeholder_types = SmartestDataUtility::getAssetClassTypes(true);
	    
	    if($get['filter_type']){
	        $this->send($get['filter_type'], 'filter_type');
	    }
	    
	    $this->send($asset_types, 'asset_types');
	    $this->send($placeholder_types, 'placeholder_types');
	    
	}
	
	public function newAssetGroupFromPlaceholder($get){
	    
	    $placeholder_id = (int) $get['placeholder_id'];
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $mode = (isset($get['mode']) && $get['mode'] == 'live') ? "live" : "draft";
	        
	        $draft_mode = ($mode == "draft");
	        
	        $definitions = $placeholder->getDefinitions($draft_mode, $this->getSite()->getId());
	        
	        $this->send($placeholder, 'placeholder');
	        $this->send($definitions, 'definitions');
	        $this->send($mode, 'mode');
	    
	    }
	    
	}
	
	public function createAssetGroup($get, $post){
	    
	    $set = new SmartestAssetGroup;
	    $set->setLabel($post['asset_group_label']);
	    $set->setName(SmartestStringHelper::toVarName($post['asset_group_label']));
	    
	    if($post['asset_group_type'] == 'ALL'){
	        $set->setFilterType('SM_SET_FILTERTYPE_NONE');
	    }else{
	        switch(substr($post['asset_group_type'], 0, 1)){
	            case 'A':
	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETTYPE');
	            break;
	            case 'P':
	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
	            break;
	        }
	    }
	    
	    $set->setFilterValue(($post['asset_group_type'] == 'ALL') ? null : substr($post['asset_group_type'], 2));
	    $set->setSiteId($this->getSite()->getId());
	    $set->setShared(0);
	    $set->save();
	    
	    $this->redirect('/assets/editAssetGroupContents?group_id='.$set->getId());
	    
	}
	
	public function createNewAssetGroupFromPlaceholder($get, $post){
	    
	    $placeholder_id = (int) $post['placeholder_id'];
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        if(isset($post['asset_ids']) && is_array($post['asset_ids'])){
	        
	            $set = new SmartestAssetGroup;
    	        $set->setLabel($post['asset_group_label']);
    	        $set->setName(SmartestStringHelper::toVarName($post['asset_group_label']));
    	        $set->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
    	        $set->setSiteId($this->getSite()->getId());
    	        $set->setShared(0);
    	        $set->setFilterValue($placeholder->getType());
    	        $set->save();
	        
	            foreach($post['asset_ids'] as $asset_id){
	                $set->addAssetById($asset_id, false);
	            }
	            
	            $this->addUserMessageToNextRequest("A group was successfully created and ".count($post['asset_ids'])." files were added to it.", SmartestUserMessage::SUCCESS);
	            $this->redirect("/assets/browseAssetGroup?group_id=".$set->getId());
	            
            }else{
                
                $this->addUserMessageToNextRequest("No group was created because no files were selected.", SmartestUserMessage::INFO);
                $this->redirect("/websitemanager/placeholderDefinitions?placeholder_id=".$placeholder->getId());
                
            }
            
        }else{
            $this->addUserMessageToNextRequest("The placeholder ID was not recognized.", SmartestUserMessage::ERROR);
            $this->redirect("/websitemanager/placeholders");
        }
        
	}
	
	public function browseAssetGroup($get){
	    
	    $group_id = $get['group_id'];
	    $mode = isset($get["mode"]) ? (int) $get["mode"] : 1;
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('file group');
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        $this->send($group->getMembers(false, $mode), 'assets');
	        $this->send($group, 'group');
	        $this->send($mode, 'mode');
	        $this->send(count($group->getMembers(false, $mode)), 'num_assets');
	        
	    }
	    
	}
	
	public function editAssetGroup($get){
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($get['group_id'])){
	        
	        $this->send($group, 'group');
	        $this->send($this->getUser()->hasToken('edit_file_group_name'), 'allow_name_edit');
	        
	        if($group->isUsableForPlaceholders()){
	            if($group->isUsedForPlaceholders()){
	                $this->send(false, 'allow_shared_toggle');
	            }else{
	                $this->send(true, 'allow_shared_toggle');
	            }
	        }else{
	            $this->send(true, 'allow_shared_toggle');
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function updateAssetGroup($get, $post){
	    
	    $group = new SmartestAssetGroup;
	    $group_id = (int) $post['group_id'];
	    
	    if($group->find($group_id)){
	        
	        $group->setLabel($post['group_label']);
	        
	        if($this->getUser()->hasToken('edit_file_group_name')){
	            $group->setName(SmartestStringHelper::toVarName($post['group_name']));
	        }
	        
	        if($group->isUsedForPlaceholders()){
                $group->setShared(1);
            }else{
                $shared = (isset($post['group_shared']) && $post['group_shared']) ? 1 : 0;
                $group->setShared($shared);
            }
            
            $group->save();
            
            $this->addUserMessageToNextRequest("The file group was updated", SmartestUserMessage::SUCCESS);
            $this->redirect('/assets/editAssetGroup?group_id='.$group_id);
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The file group ID was not recognized", SmartestUserMessage::ERROR);
	        $this->redirect('/assets/assetGroups');
	        
	    }
	    
	}
	
	public function editAssetGroupContents($get){
	    
	    $group_id = $get['group_id'];
	    
	    $this->setFormReturnUri();
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        $this->send($group->getOptions(), 'non_members');
	        $this->send($group->getMembers(), 'members');
	        $this->send($group, 'group');
	        
	    }
	    
	}
	
	public function transferSingleAsset($get, $post){
	    
	    if($post['group_id']){
	        $request = $post;
	    }else{
	        $request = $get;
	    }
	    
	    $group_id = $request['group_id'];
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        $asset_id = (int) $request['asset_id'];
	        $asset = new SmartestAsset;
	        
	        if($asset->find($asset_id)){
	            // TODO: Check that the asset is the right type for this group
	            if($request['transferAction'] == 'add'){
	                $group->addAssetById($asset_id);
                }else{
                    $group->removeAssetById($asset_id);
                }
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function transferAssets($get, $post){
	    
	    $group_id = $post['group_id'];
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        if($post['transferAction'] == 'add'){
	            
	            $asset_ids = (isset($post['available_assets']) && is_array($post['available_assets'])) ? $post['available_assets'] : array();
	            
	            foreach($asset_ids as $aid){
	            
	                $group->addAssetById($aid);
	            
	            }
	            
	        }else{
	            
	            $asset_ids = (isset($post['used_assets']) && is_array($post['used_assets'])) ? $post['used_assets'] : array();
	            
	            foreach($asset_ids as $aid){
	            
	                $group->removeAssetById($aid);
	            
	            }
	            
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function assetInfo($get){
	    
	    $asset_id = $get['asset_id'];
	    
	    $asset = new SmartestAsset;

		if($asset->find($asset_id)){
		    
		    $this->setFormReturnUri();
		    
		    $data = $asset;
		    
		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		    $this->send($asset->getGroups(), 'groups');
		    $this->send($asset->getPossibleGroups(), 'possible_groups');
		    
		    if(isset($data['type_info']['source_editable']) && SmartestStringHelper::toRealBool($data['type_info']['source_editable'])){
		        $this->send(true, 'allow_source_edit');
		    }else{
		        $this->send(false, 'allow_source_edit');
		    }
		    
		    if(isset($data['type_info']['parsable']) && SmartestStringHelper::toRealBool($data['type_info']['parsable'])){
		        $this->send(true, 'show_publish');
		        $this->send(true, 'show_attachments');
		    }else{
		        $this->send(false, 'show_publish');
		        $this->send(false, 'show_attachments');
		    }
		    
		    $this->send($data, 'asset'); 
		    
		}
	    
	}
    
    public function editAsset($get, $post){

		$asset_id = $get['asset_id'];

		if(!isset($get['from'])){
		    // $this->setFormReturnUri();
		}

		$asset = new SmartestAsset;
		
		if($asset->find($asset_id)){
            
            $assettype_code = $asset->getType();
			$types_data = SmartestDataUtility::getAssetTypes();
			$default_params = $asset->getDefaultParams();

			if(array_key_exists($assettype_code, $types_data)){

			    $asset_type = $types_data[$assettype_code];
			    
			    $asset->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
			    $this->getUser()->addRecentlyEditedAssetById($asset->getId(), $this->getSite()->getId());
			    
			    if(isset($asset_type['editable']) && SmartestStringHelper::toRealBool($asset_type['editable'])){

    			    $formTemplateInclude = "edit.".strtolower(substr($assettype_code, 13)).".tpl";

    			    if($asset_type['storage']['type'] == 'database'){
    			        if($asset->usesTextFragment()){
    			            $content = htmlspecialchars($asset->getTextFragment()->getContent(), ENT_COMPAT, 'UTF-8');
    			        }
			        }else{
			            $file = SM_ROOT_DIR.$asset_type['storage'].$asset->getUrl();
			            $content = htmlspecialchars(SmartestFileSystemHelper::load($asset->getFullPathOnDisk()), ENT_COMPAT, 'UTF-8');
			        }
                    
                    
                    
                    if(isset($asset_type['source_editable']) && SmartestStringHelper::toRealBool($asset_type['source_editable'])){
        		        $this->send(true, 'allow_source_edit');
        		    }else{
        		        $this->send(false, 'allow_source_edit');
        		    }
        		    
        		    if(isset($asset_type['parsable']) && SmartestStringHelper::toRealBool($asset_type['parsable'])){
        		        $this->send(true, 'show_publish');
        		        $this->send(true, 'show_attachments');
        		    }else{
        		        $this->send(false, 'show_publish');
        		        $this->send(false, 'show_attachments');
        		    }
                    
                    $content = SmartestStringHelper::protectSmartestTags($content);
                    
			        $this->send($content, 'textfragment_content');

			    }else{
			        $formTemplateInclude = "edit.default.tpl";
			    }
			    
			    if($asset_type['storage']['type'] != 'database'){
    	            
    	            // if(SmartestStringHelper::toRealBool($asset_type['editable'])){
    	            
        	            $path = SM_ROOT_DIR.$asset_type['storage']['location'];
        	            $dir_is_writable = is_writable($path);
        	            $file_is_writable = is_writable($path.$asset->getUrl());
        	            
        	            $this->send($path, 'path');
        	            $this->send($dir_is_writable, 'dir_is_writable');
        	            $this->send($file_is_writable, 'file_is_writable');
    	            
        	            $allow_save = $dir_is_writable && $file_is_writable;
        	            
        	            $this->send($allow_save, 'allow_save');
    	            
	                /* }else{
	                    
	                    $this->send(true, 'dir_is_writable');
        	            $this->send(true, 'file_is_writable');
                        $this->send(true, 'allow_save');
	                    
	                } */
    	            
                }else{
                    $this->send(true, 'dir_is_writable');
    	            $this->send(true, 'file_is_writable');
                    $this->send(true, 'allow_save');
                }

    			$this->send($formTemplateInclude, "formTemplateInclude");
    			$this->setTitle('Edit File | '.$asset_type['label']);
    			$this->send($asset_type, 'asset_type');
    			$this->send($asset->__toArray(), 'asset');
    			
    			$this->send($asset->getGroups(), 'groups');
    		    $this->send($asset->getPossibleGroups(), 'possible_groups');


		    }else{
		        // asset type is not supported
		    }

		}else{
		    // asset ID was not recognised
		}
	}
	
	function editTextFragmentSource($get, $post){

		$asset_id = $get['asset_id'];

		if(!isset($get['from'])){
		    $this->setFormReturnUri();
		}

		$asset = new SmartestAsset;

		if($asset->find($asset_id)){
		    
			$assettype_code = $asset->getType();
			$types_data = SmartestDataUtility::getAssetTypes();
			$default_params = $asset->getDefaultParams();

			if(array_key_exists($assettype_code, $types_data)){

			    $asset_type = $types_data[$assettype_code];
                        
			    if(isset($asset_type['editable']) && SmartestStringHelper::toRealBool($asset_type['editable'])){

    			    $formTemplateInclude = "edit.".strtolower(substr($assettype_code, 13)).".tpl";

    			    if($asset_type['storage']['type'] == 'database'){
    			        if($asset->usesTextFragment()){
    			            // $content = utf8_encode(htmlspecialchars(stripslashes($asset->getTextFragment()->getContent()), ENT_COMPAT, 'UTF-8'));
    			            $content = htmlspecialchars(stripslashes($asset->getTextFragment()->getContent()), ENT_COMPAT, 'UTF-8');
    			        }
			        }else{
			            $file = SM_ROOT_DIR.$asset_type['storage'].$asset->getUrl();
			            $content = htmlspecialchars(SmartestFileSystemHelper::load($asset->getFullPathOnDisk()), ENT_COMPAT, 'UTF-8');
			        }

			        $this->send($content, 'textfragment_content');
			        
			        if(isset($asset_type['parsable']) && SmartestStringHelper::toRealBool($asset_type['parsable'])){
        		        $this->send(true, 'show_publish');
        		        $this->send(true, 'show_attachments');
        		    }else{
        		        $this->send(false, 'show_publish');
        		        $this->send(false, 'show_attachments');
        		    }
    			    
    			    $content = SmartestStringHelper::separateParagraphs($content);
    			    
			    }else{
			        $formTemplateInclude = "edit.default.tpl";
			    }

    			$this->send($formTemplateInclude, "formTemplateInclude");
    			$this->setTitle('Edit Asset | '.$asset_type['label']);
    			$this->send($asset_type, 'asset_type');
    			$this->send($asset->__toArray(), 'asset');

		    }else{
		        // asset type is not supported
		        $this->addUserMessage('The asset type \''.$assettype_code.'\' is not supported.', SmartestUserMessage::WARNING);
		    }

		}else{
		    // asset ID was not recognised
		    $this->addUserMessage('The asset ID was not recognized.', SmartestUserMessage::ERROR);
		}
	}
	
	public function approveAsset($get){
	    
	    $asset_id = $get['asset_id'];
	    
	    if($this->getUser()->hasToken('approve_assets')){
	        $asset = new SmartestAsset;

    		if($asset->hydrate($asset_id)){
    		    
    		    $asset->setIsApproved(1);
    		    SmartestLog::getInstance('site')->log("User {$this->getUser()} approved changes to file: {$asset->getUrl()} ({$asset->getId()}).", SmartestLog::USER_ACTION);
    		    
    		    if($todo = $this->getUser()->getTodo('SM_TODOITEMTYPE_APPROVE_ASSET', $asset->getId())){
	                $todo->complete();
                }
    		    
    		    $this->addUserMessageToNextRequest('The file has been approved.', SmartestUserMessage::SUCCESS);
    		    
    		}else{
    		    $this->addUserMessage('The asset ID was not recognized.', SmartestUserMessage::ERROR);
    		}
    		
	    }else{
	        $this->addUserMessageToNextRequest('You don\'t have permission to approve files for use on this site.', SmartestUserMessage::ACCESS_DENIED);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function publishTextAsset($get){
	    
	    $asset_id = $get['asset_id'];
        
        // if($this->getUser()->hasToken('publish_text_assets') || $this->getUser()->hasToken('publish_all_assets')){
        
		    $asset = new SmartestAsset;

    		if($asset->hydrate($asset_id)){
		    
    		    $assettype_code = $asset->getType();
    			$types_data = SmartestDataUtility::getAssetTypes();

    			if(array_key_exists($assettype_code, $types_data)){
		        
    		        $asset_type = $types_data[$assettype_code];
		        
    		        if(isset($asset_type['editable']) && SmartestStringHelper::toRealBool($asset_type['editable'])){
    		            // if($asset->getisApproved() || $this->getUser()->hasToken('publish_unapproved_text_assets') || $this->getUser()->hasToken('publish_unapproved_assets')){
    	                    if($asset->getTextFragment()->publish()){
    	                        $this->addUserMessageToNextRequest('The file has been successfully published.', SmartestUserMessage::SUCCESS);
                            }else{
                                $this->addUserMessageToNextRequest('There was an error publishing file. Please check file permissions.', SmartestUserMessage::ERROR);
                            }
                        /* }else{
                            $this->addUserMessageToNextRequest('The file could not be published because it requires approval first.', SmartestUserMessage::ACCESS_DENIED);
                        } */
    	            }else{
	                
    	            }
		        
    		    }else{
        		    // asset type is not supported
        		    $this->addUserMessageToNextRequest('The asset type \''.$assettype_code.'\' is not supported.', SmartestUserMessage::WARNING);
        	    }

        	}else{
    		    // asset ID was not recognised
    		    $this->addUserMessageToNextRequest('The asset ID was not recognized.', SmartestUserMessage::ERROR);
        	}
    	
	    /* }else{
	        
	        $this->addUserMessageToNextRequest('You don\'t have permission to publish text files', SmartestUserMessage::ACCESS_DENIED);
	        
	    } */
    	
    	$this->formForward();
		
	}
	
	public function addTodoItem($get){
	    
	    $asset_id = (int) $get['asset_id'];
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrate($asset_id)){
	        
	        $uhelper = new SmartestUsersHelper;
	        $users = $uhelper->getUsersOnSiteAsArrays($this->getSite()->getId());
	        
	        $this->send($users, 'users');
	        $this->send($asset->__toArray(), 'asset');
	        $this->send($this->getUser()->__toArray(), 'user');
	        
	        $todo_types = SmartestTodoListHelper::getTypesByCategoryAsArrays('SM_TODOITEMCATEGORY_ASSETS', true);
	        $this->send($todo_types, 'todo_types');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The asset ID was not recognised.', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function insertTodoItem($get, $post){
	    
	    $asset_id = (int) $post['asset_id'];
	    
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrate($asset_id)){
	        
	        $user = new SmartestUser;
	        $user_id = (int) $post['todoitem_receiving_user_id'];
	        
	        if($user->hydrate($user_id)){
	            
	            // $user->assignTodo('SM_TODOITEMTYPE_EDIT_ITEM', $item_id, $this->getUser()->getId(), SmartestStringHelper::sanitize())
	            
	            $todo_type = SmartestStringHelper::sanitize($post['todoitem_type']);
	            
	            $type = SmartestTodoListHelper::getType($todo_type);
                
                $message = SmartestStringHelper::sanitize($post['todoitem_description']);
                
        	    if(isset($message{1})){
        	        $input_message = SmartestStringHelper::sanitize($message);
        	    }else{
        	        $input_message = $type->getDescription();
        	    }
        	    
        	    $priority = (int) $post['todoitem_priority'];
        	    $size     = (int) $post['todoitem_size'];
	            
	            $todo = new SmartestTodoItem;
	            $todo->setReceivingUserId($user->getId());
        	    $todo->setAssigningUserId($this->getUser()->getId());
        	    $todo->setForeignObjectId($asset->getId());
        	    $todo->setTimeAssigned(time());
        	    $todo->setDescription($input_message);
        	    $todo->setType($todo_type);
        	    $todo->setPriority($priority);
        	    $todo->setSize($size);
        	    $todo->save();
        	    
        	    if(!$todo->isSelfAssigned()){
        	        
        	        $message = 'Hi '.$user.",\n\n".$this->getUser()." has added a new task to your to-do list. Please visit ".SM_CONTROLLER_DOMAIN."smartest/todo for more information.\n\nYours truly,\nThe Smartest Web Content Management Platform";
        	        $user->sendEmail('New To-do Assigned', $message);
        	        
        	    }
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The user ID was not recognized.', SmartestUserMessage::ERROR);
	            
	        }
	        
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The asset ID was not recognized.', SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function previewAsset($get){
	    
	    $this->setTitle("Preview File");
	    $asset = new SmartestRenderableAsset;
	    $asset_id = (int) $get['asset_id'];
	    
	    if(!defined('SM_CMS_PAGE_SITE_ID')){
	        define('SM_CMS_PAGE_SITE_ID', $this->getSite()->getId());
        }
	    
	    if($asset->hydrate($asset_id)){
	        
	        // $data = $asset->__toArray(false, true); // don't include object, do include owner info
		    
		    if(isset($asset['type_info']['source_editable']) && SmartestStringHelper::toRealBool($asset['type_info']['source_editable'])){
		        $this->send(true, 'allow_source_edit');
		    }else{
		        $this->send(false, 'allow_source_edit');
		    }
		    
		    if(isset($asset['type_info']['parsable']) && SmartestStringHelper::toRealBool($asset['type_info']['parsable'])){
		        
		        if($this->getUser()->hasToken('publish_assets')){
		            $this->send(true, 'show_publish');
	            }else{
	                $this->send(false, 'show_publish');
	            }
	            
		        $this->send(true, 'show_attachments');
		        
		    }else{
		        $this->send(false, 'show_publish');
		        $this->send(false, 'show_attachments');
		    }
		    
		    if($this->getUser()->hasToken('approve_assets') && $asset->getIsApproved() == 0){
		        $this->send(true, 'allow_approve');
		    }else{
		        $this->send(false, 'allow_approve');
		    }
		    
		    // $page = $this->getSite()->getHomePage();
    	    
    	    /* $wpb = new SmartestWebPageBuilder('_preview');
    	    $wpb->assignPage($page);
    	    $wpb->setDraftMode(true);
    	    $wpb->prepareForRender();
    	    
    	    $wpb->assign('domain', SM_CONTROLLER_DOMAIN);
    	    $wpb->template_dir = SM_ROOT_DIR.'Presentation/';
    		$wpb->compile_dir = SM_ROOT_DIR.'System/Cache/Smarty/';
    		$wpb->cache_dir = SM_ROOT_DIR.'System/Cache/Smarty/';
    		$wpb->config_dir = SM_ROOT_DIR.'Configuration/';
    	    
    	    ob_start();
    	    $wpb->_renderAssetObject($asset, array());
    	    $asset_html = ob_get_contents();
    	    ob_end_clean();
    	    // echo $asset_html;
    	    $this->send($asset_html, 'preview_html'); */
    	    
    	    
    	    // for html reusability
    	    $this->send($asset['type_info'], 'asset_type');
    	    
    	    $html = $asset->render(true);
    	    
    	    $this->send($html, 'html');
    	    $this->send($asset, 'asset');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The asset ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	function textFragmentElements($get){
	    
	    $asset_id = $get['asset_id'];

		if(!isset($get['from'])){
		    $this->setFormReturnUri();
		}

		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){

			$assettype_code = $asset->getType();
			$types_data = SmartestDataUtility::getAssetTypes();
			$default_params = $asset->getDefaultParams();

			if(array_key_exists($assettype_code, $types_data)){

			    $asset_type = $types_data[$assettype_code];

    			if(isset($asset_type['editable']) && SmartestStringHelper::toRealBool($asset_type['editable'])){

    			    $attachments = $asset->getTextFragment()->getAttachments();
    			    // print_r($attachments);
			        $this->send($attachments, 'attachments');
			        
			        if(isset($asset_type['parsable']) && SmartestStringHelper::toRealBool($asset_type['parsable'])){
        		        $this->send(true, 'show_preview');
        		        $this->send(true, 'show_attachments');
        		    }else{
        		        $this->send(false, 'show_preview');
        		        $this->send(false, 'show_attachments');
        		    }
    			    
			    }else{
			        // $formTemplateInclude = "edit.default.tpl";
			    }

    			$this->send($formTemplateInclude, "formTemplateInclude");
    			$this->setTitle('Attached Files');
    			$this->send($asset_type, 'asset_type');
    			$this->send($asset->__toArray(), 'asset');

		    }else{
		        // asset type is not supported
		        $this->addUserMessage('The asset type \''.$assettype_code.'\' is not supported.', SmartestUserMessage::WARNING);
		    }

		}else{
		    // asset ID was not recognised
		    $this->addUserMessage('The asset ID was not recognized.', SmartestUserMessage::ERROR);
		}
	    
	}

	public function updateAsset($get, $post){
        
        $asset_id = $post['asset_id'];

		$asset = new SmartestAsset;

		if($asset->find($asset_id)){
            
            if($asset->getUserId() == $this->getUser()->getId() || $this->getUser()->hasToken('modify_assets')){
            
		        $param_values = serialize($post['params']);
    		    $asset->setParameterDefaults($param_values);

    		    $content = $post['asset_content'];
    		    $content = SmartestStringHelper::unProtectSmartestTags($content);
    		    $asset->setContent($content);
    	        $asset->setLanguage(strtolower(substr($post['asset_language'], 0, 3)));
    	        $asset->setModified(time());
                $asset->save();
                
    		    $this->addUserMessageToNextRequest("The file has been successfully updated.", SmartestUserMessage::SUCCESS);
    		    $success = true;
    		    // $message = 
		    
		    }else{
    	        $this->addUserMessageToNextRequest("You don't have permission to edit assets created by other users.", SmartestUserMessage::WARNING);
    	        $success = false;
    	    }

		}else{
		    $this->addUserMessageToNextRequest("The file you are trying to update no longer exists or has been deleted by another user.", SmartestUserMessage::WARNING);
		    $success = false;
		}
		
	    // $this->formForward();
	    
	    if($post['_submit_action'] == "continue" && $success){
	        if(isset($post['editor']) && $post['editor'] == 'source'){
	            $this->redirect("/assets/editTextFragmentSource?asset_id=".$asset->getId());
	        }else{
	            $this->redirect("/assets/editAsset?asset_type=".$asset->getType()."&asset_id=".$asset->getId());
            }
	    }else{
	        // $this->addUserMessageToNextRequest($message, $message_type);
	        $this->formForward();
	    }

	}
	
	public function defineAttachment($get){
	    
	    $asset_id = $get['asset_id'];
        $attachment_name = SmartestStringHelper::toVarName($get['attachment']);
        $this->send($attachment_name, 'attachment_name');
        
		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){
            
           if($asset->getUserId() == $this->getUser()->getId() || $this->getUser()->hasToken('modify_assets')){
            
			    $assettype_code = $asset->getType();
    			$types_data = SmartestDataUtility::getAssetTypes();
			
    			if(array_key_exists($assettype_code, $types_data)){
                
                    $attachable_files = $this->manager->getAttachableFiles($this->getSite()->getId());
                    $this->send($attachable_files, 'files');
                
                    $textfragment = $asset->getTextFragment();
                
                    if(is_object($textfragment)){
                
                        $current_def = $textfragment->getAttachmentCurrentDefinition($attachment_name);
                
                        $this->send($asset->getTextFragment()->getId(), 'textfragment_id');
                
                        $attached_asset_id = $current_def->getAttachedAssetId();
                        $this->send($attached_asset_id, 'attached_asset_id');
                        
                        $zoom = $current_def->getZoomFromThumbnail();
                        $this->send($zoom, 'zoom');
                        
                        $trs = $current_def->getThumbnailRelativeSize();
                        $this->send($trs, 'relative_size');
                        
                        $alignment = $current_def->getAlignment();
                        $this->send($alignment, 'alignment');
                
                        $caption = $current_def->getCaption();
                        $this->send($caption, 'caption');
                
                        $caption_alignment = $current_def->getCaptionAlignment();
                        $this->send($caption_alignment, 'caption_alignment');
                
                        $float = $current_def->getFloat();
                        $this->send($float, 'float');
                
                        $border = $current_def->getBorder();
                        $this->send($border, 'border');
                
                    }
                
    			    $asset_type = $types_data[$assettype_code];

        			$this->send($formTemplateInclude, "formTemplateInclude");
        			$this->setTitle('Define Attachment: '.$attachment_name);
        			$this->send($asset_type, 'asset_type');
        			$this->send($asset, 'asset');

    		    }else{
    		        // asset type is not supported
    		        $this->addUserMessage('The asset type \''.$assettype_code.'\' is not supported.', SmartestUserMessage::WARNING);
    		    }
		    
		    }else{
    	        $this->addUserMessageToNextRequest("You don't have permission to edit assets created by other users.", SmartestUserMessage::WARNING);
    	    }

		}else{
		    // asset ID was not recognised
		    $this->addUserMessage('The asset ID was not recognized.', SmartestUserMessage::ERROR);
		}
	    
	}
	
	public function updateAttachmentDefinition($get, $post){
	    
	    $textfragment_id = $post['textfragment_id'];
	    $attachment_name = SmartestStringHelper::toVarName($post['attachment_name']);
	    
	    $tf = new SmartestTextFragment;
	    
	    if($tf->find($textfragment_id)){
	        
	        $current_def = $tf->getAttachmentCurrentDefinition($attachment_name);
	        
	        if(!$current_def->getTextFragmentId()){
	            $current_def->setTextFragmentId($textfragment_id);
	        }
	        
	        $current_def->setAttachedAssetId((int) $post['attached_file_id']);
	        $current_def->setAttachmentName($attachment_name);
	        $current_def->setZoomFromThumbnail(isset($post['attached_file_zoom']));
	        $current_def->setThumbnailRelativeSize((int) $post['thumbnail_relative_size']);
	        $current_def->setCaption(htmlentities($post['attached_file_caption']));
	        $current_def->setAlignment(SmartestStringHelper::toVarName($post['attached_file_alignment']));
	        $current_def->setCaptionAlignment(SmartestStringHelper::toVarName($post['attached_file_caption_alignment']));
	        $current_def->setFloat(isset($post['attached_file_float']));
	        $current_def->setBorder(isset($post['attached_file_border']));
	        
	        $current_def->save();
	        
	    }else{
	        $this->addUserMessage('The textfragment ID was not recognized.', SmartestUserMessage::ERROR);
	    }
	    
	    
	    $this->formForward();
	    
	}

	public function deleteAssetConfirm($get){

		$asset_id = $get['asset_id'];

		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){

		    $live_instances = $asset->getLiveInstances();
		    $this->send($live_instances, 'live_instances');
		    $this->send(count($live_instances), 'num_live_instances');

		    $draft_instances = $asset->getDraftInstances();
		    $this->send($draft_instances, 'draft_instances');
		    $this->send(count($draft_instances), 'num_draft_instances');

		    $this->setTitle("Delete File?");

		    $this->send($asset->__toArray(), 'asset');

		}else{
		    $this->addUserMessageToNextRequest("The asset ID was not recognized", SmartestUserMessage::ERROR);
		    $this->formForward();
		}

	}

	function deleteAsset($get, $post){

		$asset_id = $post['asset_id'];

	    $asset = new SmartestAsset;

	    if($asset->hydrate($asset_id)){
            
            if($this->getUser()->hasToken('delete_assets') || $asset->getUserId() == $this->getUser()->getId()){
            
	            $asset->delete();
                
	            $this->addUserMessageToNextRequest("The file has been successfully deleted.", SmartestUserMessage::SUCCESS);
    		    $this->formForward();
    		
    		}else{

                $this->addUserMessageToNextRequest("You don't currently have permission to delete files that don't belong to you.", SmartestUserMessage::ACCESS_DENIED);
        		$this->formForward();

            }

	    }else{

	        $this->addUserMessageToNextRequest("The asset ID was not recognized.", SmartestUserMessage::ERROR);
    		$this->formForward();

	    }
		    
	}

	function duplicateAsset($get){

		/*$assettype_code=$get['assettype_code'];
		$asset_id=$this->manager->getNumericAssetId($get['asset_id']);
		$stringid=$this->manager->getStringId($asset_id);
		$name=$this->manager->getUniqueStringId($stringid);
		$assettypeid=$this->manager->getAssetTypeId($assettype_code);

		if($assettype_code=='LINE' || $assettype_code=='TEXT' || $assettype_code=='HTML' ){

			$fragment_content=$this->manager->getFragment($asset_id);
			$textfragment_created=strtotime('now');
			$fragment_id = $this->database->query("INSERT INTO TextFragments(textfragment_content,textfragment_created) VALUE ('$fragment_content','$textfragment_created')");
			$textfragment_asset_id = $this->manager->insertAsset(SmartestStringHelper::random(32), SmartestStringHelper::toVarName($name), '', '', $assettypeid, $fragment_id);
			$this->database->query("UPDATE TextFragments SET textfragment_asset_id='$textfragment_asset_id' WHERE textfragment_id='$fragment_id'");

		}else{

			$oldfilename=$this->manager->getFileName($asset_id);
			$newfilename=$this->manager->getUniqueName($oldfilename);

			if($assettype_code=='TMPL'){
				$path = SM_ROOT_DIR.'Presentation/Layouts/';
			}

			if($assettype_code=='JPEG' || $assettype_code=='GIF' || $assettype_code=='PNG'){
				$path = SM_ROOT_DIR.'Public/Resources/Images/';
			}

			if($assettype_code=='CSS'){
				$path = SM_ROOT_DIR.'Public/Resources/Stylesheets/';
			}

			if($assettype_code=='JSCR'){
				$path = SM_ROOT_DIR.'Public/Resources/Javascript/';
			}

			if($assettype_code=='QTMV' || $assettype_code=='MPEG' || $assettype_code=='SWF'){
				$path = SM_ROOT_DIR.'Public/Resources/Assets/';
			}

			if(copy($path.$oldfilename, $path.$newfilename)){
				$this->manager->insertAsset(SmartestStringHelper::random(32), SmartestStringHelper::toVarName($name), $newfilename, '', $assettypeid, '');
				$this->setFormReturnVar('savedTheCopy', 'true');
			}else{
				$this->setFormReturnVar('savedTheCopy', 'false');
			}
		} */

		$this->formForward();
	}

	function downloadAsset($get){

		$asset_id = $get['asset_id'];

		// echo $asset_id;
		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){

		    if($asset->usesLocalFile()){
		        $download = new SmartestDownloadHelper($asset->getFullPathOnDisk());
		    }else{
		        $download = new SmartestDownloadHelper($asset->getTextFragment()->getContent());
		        $download->setDownloadFilename($asset->getDownloadableFilename());
		    }

		    // echo $download->getDownloadSize();

    		$ua = $this->getUserAgent()->getAppName();

    		if($ua == 'Explorer' || $ua == 'Opera'){
    		    $mime_type = 'application/octetstream';
    		}else{
    		    $mime_type = 'application/octet-stream';
    		}

    		// echo $download->getType();

    		$download->setMimeType($mime_type);
    		$download->send();

		}else{
		    $this->addUserMessageToNextRequest("The asset ID was not recognized.", SmartestUserMessage::ERROR);
		    $this->formForward();
		}

	}
	
	/* function getAssetGroupContents($get){
	    
	    $group_id = (int) $get[''];
	    
	    $q = new SmartestManyToManyQuery("SM_MTMLOOKUP_ASSET_GROUPS");
	    $q->addTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $group_id);
	    $assets = $q->retrieve();
	    
	} */
	
	public function comments($get){
	    
	    $asset_id = $get['asset_id'];

		// echo $asset_id;
		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){

		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		    $this->send($asset, 'asset');
		    
		    if(isset($asset['type_info']['source_editable']) && SmartestStringHelper::toRealBool($asset['type_info']['source_editable'])){
		        $this->send(true, 'allow_source_edit');
		    }else{
		        $this->send(false, 'allow_source_edit');
		    }
		    
		    if(isset($asset['type_info']['parsable']) && SmartestStringHelper::toRealBool($asset['type_info']['parsable'])){
		        
		        if($this->getUser()->hasToken('publish_assets')){
		            $this->send(true, 'show_publish');
	            }else{
	                $this->send(false, 'show_publish');
	            }
	            
		        $this->send(true, 'show_attachments');
		        
		    }else{
		        $this->send(false, 'show_publish');
		        $this->send(false, 'show_attachments');
		    }
		    
		    if($this->getUser()->hasToken('approve_assets') && $asset->getIsApproved() == 0){
		        $this->send(true, 'allow_approve');
		    }else{
		        $this->send(false, 'allow_approve');
		    }

		}else{
		    $this->addUserMessageToNextRequest("The asset ID was not recognized.", SmartestUserMessage::ERROR);
		    $this->redirect('/smartest/assets');
		}
	    
	}
	
	public function attachCommentToAsset($get, $post){
	    
	    $asset_id = $post['asset_id'];

		// echo $asset_id;
		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){

		    $asset->addComment($post['comment_content'], $this->getUser()->getId());
		    $this->formForward();
		    // $this->redirect('/assets/comments?asset_id='.$asset->getId());

		}else{
		    $this->addUserMessageToNextRequest("The asset ID was not recognized.", SmartestUserMessage::ERROR);
		    $this->redirect('/smartest/assets');
		}
	    
	}
	
	public function useAsset(){
	    
	    
	    
	}
		
}
