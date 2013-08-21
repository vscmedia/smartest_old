<?php

/**
 *
 * PHP version 5
 * @author     Marcus Gilroy-Ware <marcus@visudo.com>
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

// phpinfo();

// require_once 'XML/Serializer.php';

class Assets extends SmartestSystemApplication{
	
	public function startPage(){
	    if($this->getApplicationPreference('startpage_view') == 'groups'){
	        $this->forward('assets', 'assetGroups');
	    }else{
	        $this->forward('assets', 'getAssetTypes');
	    }
	}
	
	public function getAssetTypes(){
	    
	    $this->requireOpenProject();
	    
	    $h = new SmartestAssetsLibraryHelper;
	    
	    $this->setApplicationPreference('startpage_view', 'types');
		
		$this->setTitle("Files by type");
		$this->setFormReturnUri(); // set the url of the page to be return to
		$this->setFormReturnDescription('file types');
		
		// $assetTypes_old = $this->manager->getAssetTypes();
		$assetTypes = $h->getTypesByCategory(array('templates'));
		$locations = $h->getUnWritableStorageLocations();
		
		$this->send($assetTypes, "assetTypeCats");
		$this->send($locations, 'locations');
		
		$recent = $this->getUser()->getRecentlyEditedAssets($this->getSite()->getId());
        $this->send($recent, 'recent_assets');
		
	}
	
	public function getAssetTypeMembers($get){
		
		$this->requireOpenProject();
		
		$code = strtoupper($get["asset_type"]);
		$mode = isset($get["mode"]) ? (int) $get["mode"] : 1;
		$this->send($this->getApplicationPreference('asset_list_style', 'grid'), 'list_view');
		
		$this->send($mode, 'mode');
		
		$this->setFormReturnUri();
		
		if($this->manager->getIsValidAssetTypeCode($code)){
			$assets = $this->manager->getAssetsByTypeCode($code, $this->getSite()->getId(), $mode);	
		}
		
		$types_array = SmartestDataUtility::getAssetTypes();
		
		if(in_array($code, array_keys($types_array))){
		    
		    $type = $types_array[$code];
		    $this->send('editableasset', 'sidebartype');
		    
		    if(in_array($code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))){
	            $this->send(true, 'contact_sheet_view');
	        }else{
	            $this->send(false, 'contact_sheet_view');
	        }
		    
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
		
		$this->send(new SmartestString($type['label']), 'type_label');
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
	    
	    if($a->hydrate((int) $this->getRequestParameter('asset_id'))){
	        
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
	    
	    if($this->getRequestParameter('new_files') && is_array($this->getRequestParameter('new_files'))){
	        $new_files = $this->getRequestParameter('new_files');
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
	    
	    $this->requireOpenProject();
	    
	    if($this->getRequestParameter('new_files') && is_array($this->getRequestParameter('new_files'))){
	        $new_files = $this->getRequestParameter('new_files');
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
    	        if(isset($nf['archive'])){
    	            $a->setIsArchived(1);
    	        }
    	        $a->setWebid(SmartestStringHelper::random(32));
    	        $a->setStringid(SmartestStringHelper::toVarName($nf['name']));
    	        $a->setLabel($nf['name']);
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
		
		$this->requireOpenProject();
		$alh = new SmartestAssetsLibraryHelper;
		
		if($this->getRequestParameter('group_id') && !$this->getRequestParameter('for')){
		    $group = new SmartestAssetGroup;
		    if($group->find($this->getRequestParameter('group_id'))){
		        $this->send($group, 'group');
		    }
	    }
	    
	    $this->send($this->getSite()->getLanguageCode(), 'site_language');
	    
	    if($this->getRequestParameter('asset_type')){
            
            $this->send(false, 'require_type_selection');
            
            $asset_type = SmartestStringHelper::toConstantName($this->getRequestParameter('asset_type'));
    	    $types_array = $alh->getTypes(array('templates'));
    	    
    	    if(in_array($asset_type, array_keys($types_array))){
                
                $type = $types_array[$asset_type];
                $this->setTitle("Add a new ".$type['label']);
    		    $this->send($type['id'], 'type_code');
    		    $this->send($type, 'new_asset_type_info');
    		    
    		    $this->send('Unnamed '.addslashes($type['label']), 'start_name');
                
                // what input options are available
                
                $num_input_options = count($type['input_options']);
                $preference_name = 'preferred_input_method_'.substr(md5($asset_type), 0, 8);
                
                if($num_input_options == 1){
                    
                    $input_method_id = $type['input_options'][0];
                    $this->send(array(), 'input_methods');
                    
                }else if($num_input_options > 1){
                    
                    $input_methods = $alh->getInputTypesForAssetType($asset_type);
                    $this->send($input_methods, 'input_methods');
                    
                    if($this->getRequestParameter('input_method')){
                        // does the specified type code exist, and can it be used with this asset type?
                        if(in_array($this->getRequestParameter('input_method'), $alh->getInputTypeCodes()) && in_array($this->getRequestParameter('input_method'), $type['input_options'])){
                            // yes, so use it
                            $input_method_id = $this->getRequestParameter('input_method');
                        }else{
                            // no, so load the first one
                            $input_method_id = $type['input_options'][0];
                            // TODO: make this a user preference (dependent on implementation of FS#388)
                        }
                        
                    }else{
                        $input_method_id = $type['input_options'][0];
                        // TODO: make this a user preference (dependent on implementation of FS#388)
                    }
                    
                }
                
                $this->send($input_method_id, 'input_method');
                $all_input_types = $alh->getInputTypes();
                $template = $all_input_types[$input_method_id]['template'];
                $this->send($template, 'interface_file');
                $this->send('Name this file', 'name_instruction');
                
                // Can a new file be saved?
                if($type['storage']['type'] != 'database' && $type['storage']['type'] != 'external_translated'){
    	            $path = SM_ROOT_DIR.$type['storage']['location'];
    	            $allow_save = is_writable($path);
    	            $this->send($allow_save, 'allow_save');
    	            $this->send($path, 'path');
                }else{
                    $this->send(true, 'allow_save');
                }
                
                // Should it be put in a group?
                $possible_groups = $alh->getAssetGroupsThatAcceptType($asset_type, $this->getSite()->getId());
    		    $this->send($possible_groups, 'possible_groups');
    		    
    		    switch($input_method_id){
    		        
    		        case "SM_ASSETINPUTTYPE_DIRECT_INPUT":
    		        
    		        // load input template and send name
    		        if(isset($type['input_panel'])){
    		            $this->send($type['input_panel'], 'input_panel');
    		            $this->send(true, 'input_panel_set');
		            }else{
		                $this->send(false, 'input_panel_set');
		            }
		            
    		        break;
    		        
    		        case "SM_ASSETINPUTTYPE_FTP_UPLOAD":
    		        // get available unimported files
    		        $this->send($alh->getUnimportedFilesByType($asset_type), 'unimported_files');
    		        $this->send($type['storage']['location'], 'directory');
    		        break;
    		        
    		        case "SM_ASSETINPUTTYPE_BROWSER_UPLOAD":
    		        $mr = ini_get('upload_max_filesize');
            		preg_match('/(\d+)M$/', $mr, $m);
            		$this->send(new SmartestNumeric($m[1]), 'max_upload_size_in_megs');
    		        break;
    		        
    		        case "SM_ASSETINPUTTYPE_URL_INPUT":
    		        break;
    		        
    		    }

    		}else{
    		    $this->send($asset_type, 'wanted_type');
    		    $this->setTitle("File type not recognized");
    		    $form_include = "add.default.tpl";
    		}

    		$this->send($form_include, 'form_include'); 
            
        }else{
            
            if($this->getRequestParameter('group_id') && $group->getId() && !$this->getRequestParameter('asset_type')){
	            $types = $group->getTypes();
	            if(count($types) == 1){
	                $this->send($types[0]['id'], 'type_code');
	                $this->send($types[0], 'new_asset_type_info');
	                $this->send(false, 'require_type_selection');
	                
	                // Can a new file be saved?
                    if($types[0]['storage']['type'] != 'database' && $types[0]['storage']['type'] != 'external_translated'){
        	            $path = SM_ROOT_DIR.$types[0]['storage']['location'];
        	            $allow_save = is_writable($path);
        	            $this->send($allow_save, 'allow_save');
        	            $this->send($path, 'path');
                    }else{
                        $this->send(true, 'allow_save');
                    }
                    
	            }else{
	                $types = $group->getTypes();
	                $this->send(true, 'require_type_selection');
	            }
            }else{
                $types = $alh->getTypes(array('templates'));
                $this->send(true, 'require_type_selection');
                $this->setTitle("Choose file type");
            }
            
        }
        
        if($this->getRequestParameter('for') == 'placeholder'){
            
            if($this->getRequestParameter('placeholder_id') && $this->getRequestParameter('page_id')){
            
                $page = new SmartestPage;
            
                if($page->find($this->getRequestParameter('page_id'))){
            
                    $placeholder = new SmartestPlaceholder;
            
                    if($placeholder->find($this->getRequestParameter('placeholder_id'))){
                        
                        if(!$this->getRequestParameter('asset_type')){
                            $types = $placeholder->getPossibleFileTypes();
                            if(count($types) == 1){
                                $fwd = '/smartest/file/new?for=placeholder&asset_type='.$types[0]['id'].'&placeholder_id='.$placeholder->getId().'&page_id='.$page->getId();
                                if($this->getRequestParameter('item_id')) $fwd .= '&item_id='.$this->getRequestParameter('item_id');
                                $this->redirect($fwd);
                            }
                        }
                        
                        $this->send($placeholder, 'placeholder');
                        $this->send($page, 'page');
                        $this->send('placeholder', 'for');
                        
                        if($this->getRequestParameter('item_id')){
                            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                                $this->send($item, 'item');
                            }
                        }
                        
                        if($placeholder->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_ASSETGROUP'){
	                        // add file to placeholder's group
	                        $group = new SmartestAssetGroup;

                            if($group->find($placeholder->getFilterValue())){
                                $this->send($group->getId(), 'group_id');
                                $this->send(true, 'lock_group_dropdown');
                            }
                            
	                    }
                    
                    }else{
                        $this->addUserMessageToNextRequest("The placeholder ID was not recognised.", SmartestUserMessage::ERROR);
                        $this->redirect('/smartest/files');
                    }
            
                }else{
                
                    $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
                    $this->redirect('/smartest/pages');
                
                }
        
            }else{
            
                $this->addUserMessageToNextRequest("Both page and placeholder IDs must be provided.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/pages');
            
            }
            
        }else if($this->getRequestParameter('for') == 'ipv'){
            
            if($this->getRequestParameter('property_id')){

                $property = new SmartestItemProperty;

                if($property->find($this->getRequestParameter('property_id'))){
                    
                    if($property->usesAssets()){
                        if(!$this->getRequestParameter('asset_type')){
                            $types = $property->getPossibleFileTypes();
                            if(count($types) == 1){
                                $fwd = '/smartest/file/new?for=ipv&asset_type='.$types[0]['id'].'&property_id='.$property->getId();
                                if($this->getRequestParameter('item_id')) $fwd .= '&item_id='.$this->getRequestParameter('item_id');
                                $this->redirect($fwd);
                            }
                        }
                    }
                    
                    $this->send('ipv', 'for');
                    $this->send($property, 'property');
                    
                    if($property->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_ASSETGROUP'){
                        
                        $group = new SmartestAssetGroup;
                        
                        if($group->find($property->getOptionSetId())){
                            $this->send($group->getId(), 'group_id');
                            $this->send(true, 'lock_group_dropdown');
                        }else{
                            // Log: property specifies group that does not exist
                        }
                    }
                    
                    if($this->getRequestParameter('item_id')){
                        if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                            $this->send($item, 'item');
                            $this->send($property->getName().' - '.$item->getName(), 'suggested_name');
                        }else{
                            $this->addUserMessageToNextRequest("The ".strtolower($property->getModel()->getName())." ID was not recognised.", SmartestUserMessage::ERROR);
                            $this->redirect('/datamanager/getItemclassMembers?class_id='.$property->getItemclassId());
                        }
                    }else{
                        $this->send('Name this '.strtolower($property->getModel()->getName()), 'name_instruction');
                    }

                }else{
                    $this->addUserMessageToNextRequest("The property ID was not recognised.", SmartestUserMessage::ERROR);
                    $this->redirect('/smartest/files');
                }

            }else{

                $this->addUserMessageToNextRequest("A property ID was not provided.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/models');

            }
            
        }
        
        $this->send($types, 'types');
		
	}
	
	public function startNewFileCreationForPlaceholderDefinition(){
	    
	    if($this->getRequestParameter('placeholder_id') && $this->getRequestParameter('page_id')){
            
            $page = new SmartestPage;
            
            if($page->find($this->getRequestParameter('page_id'))){
            
                $placeholder = new SmartestPlaceholder;
            
                if($placeholder->find($this->getRequestParameter('placeholder_id'))){
                    
                    if(!$this->getRequestParameter('asset_type')){
                        
                        $types = $placeholder->getPossibleFileTypes();
                        $this->send($types, 'types');
                        
                        if(count($types) == 1){
                            $url = '/smartest/file/new?for=placeholder&asset_type='.$types[0]['id'].'&placeholder_id='.$placeholder->getId().'&page_id='.$page->getId();
                        }else{
                            $url = '/smartest/file/new?for=placeholder&placeholder_id='.$placeholder->getId().'&page_id='.$page->getId();
                        }
                        
                        if($this->getRequestParameter('item_id')) $url .= '&item_id='.$this->getRequestParameter('item_id');
                        
                        $this->redirect($url);
                        
                    }
                    
                }else{
                    $this->addUserMessageToNextRequest("The placeholder ID was not recognised.", SmartestUserMessage::ERROR);
                    $this->redirect('/smartest/files');
                }
            
            }else{
                
                $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/pages');
                
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("Both page and placeholder IDs must be provided.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/pages');
            
        }
	    
	}
	
	public function startNewFileCreationForItemPropertyValue(){
	    
	    if($this->getRequestParameter('property_id')){
	    
            $property = new SmartestItemProperty;
    
            if($property->find($this->getRequestParameter('property_id')) && $property->usesAssets()){
            
                $types = $property->getPossibleFileTypes();
                $this->send($types, 'types');
                //print_r($types);
            
                if(count($types) == 1){
                    $url = '/smartest/file/new?for=ipv&asset_type='.$types[0]['id'].'&property_id='.$property->getId();
                }else{
                    $url = '/smartest/file/new?for=ipv&property_id='.$property->getId();
                }
                
                if($this->getRequestParameter('item_id')){
                    $url .= '&item_id='.$this->getRequestParameter('item_id');
                }
                
                if($this->getRequestParameter('page_id')){
                    $url .= '&page_id='.$this->getRequestParameter('page_id');
                }
                
                $this->redirect($url);
            
            }else{
                $this->addUserMessageToNextRequest("The property ID was not recognised or was the wrong type.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/files');
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("A property ID was not provided.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/models');
            
        }
	    
	}
	
	public function saveNewAsset($get, $post){
	    
	    $this->requireOpenProject();
	    
	    if(!strlen($this->getRequestParameter('asset_label'))){
	        $this->addUserMessage("You must give the file a name", SmartestUserMessage::WARNING);
	        $this->setRequestParameter('asset_type', $this->getRequestParameter('asset_type'));
	        $this->forward('assets', 'addAsset');
	    }
	    
	    if($this->getUser()->hasToken('create_assets')){
	    
	        $asset_type = $this->getRequestParameter('asset_type');
	    
    	    $everything_ok = true;
	        
	        $alh = new SmartestAssetsLibraryHelper;
	        $types_array = $alh->getTypes();
		    
		    if(in_array($asset_type, array_keys($types_array))){
		    
    		    $type = $types_array[$asset_type];
		    
    		    /* $asset = new SmartestAsset;
    		    $asset->setType($asset_type);
    		    $asset->setSiteId($this->getSite()->getId());
    		    $shared = $this->getRequestParameter('asset_shared') ? 1 : 0;
		        $asset->setLabel($this->getRequestParameter('string_id'));
    		    $asset->setShared($shared);
    		    $asset->setUserId($this->getUser()->getId());
    		    $asset->setCreated(time());
    		    $asset->setLanguage(strtolower(substr($this->getRequestParameter('asset_language'), 0, 3))); // ISO-6639-3 language codes are only ever three letters long */
		    
    		    $suffixes = array();
		    
    		    if(is_array($type['suffix'])){
    		        foreach($type['suffix'] as $s){
    		            $suffixes[] = $s['_content'];
    		        }
    		    }
    		    
    		    try{
    		        
    		        $ach = new SmartestAssetCreationHelper($asset_type);
    		        
        		    switch($this->getRequestParameter('input_method')){
    		        
        		        // indicate FTP'ed file
        		        case 'SM_ASSETINPUTTYPE_FTP_UPLOAD':
        		        $ach->createNewAssetFromUnImportedFile($this->getRequestParameter('chosen_file'), $this->getRequestParameter('asset_label'));
        		        $asset = $ach->finish();
        		        break;
    		        
        		        // insert URL only
        		        case 'SM_ASSETINPUTTYPE_URL_INPUT':
        		        $url = new SmartestExternalUrl($this->getRequestParameter('asset_url'));
        		        $ach->createNewAssetFromUrl($url, $this->getRequestParameter('asset_label'));
        		        $asset = $ach->finish();
        		        break;
    		        
        		        // Upload file via html form
        		        case 'SM_ASSETINPUTTYPE_BROWSER_UPLOAD':
        		        if(SmartestUploadHelper::uploadExists('new_file')){
        		            $upload = new SmartestUploadHelper('new_file');
                            $upload->setUploadDirectory(SM_ROOT_DIR.'System/Temporary/');
                            // creates a new unsaved asset from the file upload
                            $ach->createNewAssetFromFileUpload($upload, $this->getRequestParameter('asset_label'));
                            $asset = $ach->finish();
                        }else{
                            $this->addUserMessage('You didn\'t attach a file to upload.', SmartestUserMessage::WARNING);
                            $this->forward('assets', 'addAsset');
                        }
    		            break;
    		        
        		        // Insert file contents via textarea
        		        case 'SM_ASSETINPUTTYPE_DIRECT_INPUT':
        		        $ach->createNewAssetFromTextArea($this->getRequestParameter('content'), $this->getRequestParameter('asset_label'));
        		        $asset = $ach->finish();
        		        break;
        		        
        		        default:
        		        $this->addUserMessage("The file creation type was not recognized.", SmartestUserMessage::ERROR);
        		        $this->setRequestParameter('asset_type', $this->getRequestParameter('asset_type'));
        		        $this->forward('assets', 'addAsset');
        		        break;
    		        
    		        }
		        
		        }catch(SmartestAssetCreationException $e){
	                // deal with any issues here
	                $this->addUserMessage($e->getMessage(), SmartestUserMessage::ERROR);
    		        $this->setRequestParameter('asset_type', $this->getRequestParameter('asset_type'));
    		        SmartestLog::getInstance('site')->log($e->getMessage(), SmartestLog::ERROR);
    		        $this->forward('assets', 'addAsset');
	            }
    		    
    		    header("HTTP/1.1 201 Created");
    		    
    		    if($this->getRequestParameter('params') && is_array($this->getRequestParameter('params'))){
    		        $param_values = serialize($this->getRequestParameter('params'));
    		        $asset->setParameterDefaults($param_values);
    	        }
		        
		        // if($everything_ok){
    		    
    		    // Add a bit more information about the asset    
    		    $asset->setShared($this->getRequestParameter('asset_shared') ? 1 : 0);
    		    $asset->setSiteId($this->getSite()->getId());
    		    $asset->setLanguage(strtolower(substr($this->getRequestParameter('asset_language'), 0, 3))); // ISO-639-3 language codes are only ever three letters long
    		    $this->getUser()->addRecentlyEditedAssetById($asset->getId(), $this->getSite()->getId());
    		    $asset->save();
		        
		        if(strlen($this->getRequestParameter('initial_group_id')) && is_numeric($this->getRequestParameter('initial_group_id'))){
		            
		            $group = new SmartestAssetGroup;
		            
		            if($group->find($this->getRequestParameter('initial_group_id'))){
		                $asset->addToGroupById($this->getRequestParameter('initial_group_id'), true);
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
		        
		        $this->getUser()->addRecentlyEditedAssetById($asset->getId(), $this->getSite()->getId());
		        $this->addUserMessageToNextRequest($message, $status);
		        SmartestLog::getInstance('site')->log($this->getUser().' created file: '.$asset->getUrl(), SmartestLog::USER_ACTION);
		        
		        // If the file was being added for a particular usage
		        if($this->getRequestParameter('for')){
		            if($this->getRequestParameter('for') == 'placeholder'){
		                if($this->getRequestParameter('placeholder_id') && $this->getRequestParameter('page_id')){

                            $page = new SmartestPage;

                            if($page->find($this->getRequestParameter('page_id'))){

                                $placeholder = new SmartestPlaceholder;

                                if($placeholder->find($this->getRequestParameter('placeholder_id'))){
                                    
                                    // check type
                                    $types = $placeholder->getPossibleFileTypeCodes();
                                    if(in_array($asset->getTYpe(), $types)){
                                    
                                        // load or create placeholder definition
                                        $definition = new SmartestPlaceholderDefinition;
                                    
                                        if($definition->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){

                    	                    // update placeholder
                    	                    $definition->setDraftAssetId($asset->getId());
                    	                    $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on page '".$page->getTitle(true)."' to use asset ID ".$asset->getId().".";

                    	                }else{

                    	                    // wasn't already defined
                    	                    $definition->setDraftAssetId($asset->getId());
                    	                    $definition->setAssetclassId($placeholder->getId());
                    	                    $definition->setInstanceName('default');
                    	                    $definition->setPageId($page->getId());
                	                    
                    	                    if($this->getRequestParameter('item_id')){
                    	                        $definition->setItemId($this->getRequestParameter('item_id'));
                    	                    }
                	                    
                    	                    $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on page '".$page->getTitle(true)."' with asset ID ".$asset->getId().".";

                    	                }
                	                
                    	                $definition->save();
                    	                
                    	                if($placeholder->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_ASSETGROUP'){
                	                        // add file to placeholder's group
                	                        $group = new SmartestAssetGroup;

                                            if($group->find($placeholder->getFilterValue())){
                                                $asset->addToGroupById($group->getId());
                                            }
                                            
                	                    }
                                    
                                    }else{
                                        $this->addUserMessageToNextRequest("The new file was not the right type to use with this placeholder.", SmartestUserMessage::ERROR);   
                                    }
                                    // forward back to placeholder def screen
                                    
                                    $url = '/websitemanager/definePlaceholder?assetclass_id='.$placeholder->getName().'&page_id='.$page->getWebid();
                                    if($this->getRequestParameter('item_id')) $url .= '&item_id='.$this->getRequestParameter('item_id');
                                    
                                    $this->redirect($url);
                                }else{
                                    $this->addUserMessageToNextRequest("The placeholder ID was not recognised.", SmartestUserMessage::ERROR);
                                }

                            }else{
                                $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
                            }

                        }else{
                            $this->addUserMessageToNextRequest("Both page and placeholder IDs must be provided in order to add a new file as a placeholder definition.", SmartestUserMessage::ERROR);
                        }
                        
		            }else if($this->getRequestParameter('for') == 'ipv'){
		                
		                if($this->getRequestParameter('property_id')){
		                    
		                    $property = new SmartestItemProperty;

                            if($property->find($this->getRequestParameter('property_id'))){

                                if($property->usesAssets()){
                                    
                                    if($this->getRequestParameter('item_id')){
                                        if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                                            $item->setPropertyValueByNumericKey($property->getId(), $asset->getId());
                                            $item->save();
                                        }else{
                                            $this->addUserMessageToNextRequest("The item ID was not recognised.", SmartestUserMessage::ERROR);
                                        }
                                    }else{
                                        $item = SmartestCmsItem::createNewByModelId($property->getItemclassId());
                                        $item->setName($asset->getLabel());
                                        $item->addAuthorById($this->getUser()->getId());
                                        $item->getItem()->setCreateByAuthorId($this->getUser()->getId());
                                        $item->getItem()->setCreated(time());
                                        $item->getItem()->setLanguage($asset->getLanguage());
                                        $item->getItem()->setSiteId($this->getSite()->getId());
                                        $item->save();
                                        $item->setPropertyValueByNumericKey($property->getId(), $asset->getId());
                                        $item->save();
                                    }
                                    
                                    if($property->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_ASSETGROUP'){
                                        
                                        $group = new SmartestAssetGroup;
                                        
                                        if($group->find($property->getOptionSetId())){
                                            $asset->addToGroupById($group->getId());
                                        }else{
                                            // Log: property specifies group that does not exist
                                        }
                                    }
                                    
                                    // forward back to item edit screen
                                    $this->redirect('/datamanager/openItem?item_id='.$item->getId());
                                    
                                }else{
                                    $this->addUserMessageToNextRequest("The property does not store assets.", SmartestUserMessage::ERROR);
                                }
                                
                            }else{
                                $this->addUserMessageToNextRequest("The property ID was not recognised.", SmartestUserMessage::ERROR);
                            }
		                    
		                }else{
                            $this->addUserMessageToNextRequest("A property ID was not provided.", SmartestUserMessage::ERROR);
                        }
		                
		            }
		            
		        }else{
		            $this->formForward();
		        }
    		        
    		    /* }else{
    		        $this->addUserMessage("There was an error creating the new file.", SmartestUserMessage::ERROR);
    		        $this->setRequestParameter('asset_type', $this->getRequestParameter('asset_type'));
    		        SmartestLog::getInstance('site')->log("There was an error creating the new file.", SmartestLog::ERROR);
    		        $this->forward('assets', 'addAsset');
    		    } */
		    
    		}else{
		    
    		    $this->addUserMessage("The asset type was not recognized.", SmartestUserMessage::ERROR);
    		    SmartestLog::getInstance('site')->log("The asset type was not recognized.", SmartestLog::ERROR);
    		    $this->setRequestParameter('asset_type', null);
    		    $this->forward('assets', 'addAsset');
		    
    		}
		    
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't currently have permission to add new files.", SmartestUserMessage::ACCESS_DENIED);
	        SmartestLog::getInstance('site')->log("You don't currently have permission to add new files.", SmartestLog::ACCESS_DENIED);
	        $this->formForward();
	        
	    }
	    
	}
	
	/** Start Asset Group Stuff **/
	
	public function assetGroups(){
	    
	    $this->requireOpenProject();
	    
	    $this->setApplicationPreference('startpage_view', 'groups');
	    $this->setTitle("File groups");
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    $groups = $alh->getAssetGroups($this->getSite()->getId());
	    $locations = $alh->getUnWritableStorageLocations();
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('file groups');
	    
	    $this->send($groups, 'groups');
	    $this->send($locations, 'locations');
	    
	    $recent = $this->getUser()->getRecentlyEditedAssets($this->getSite()->getId());
        $this->send($recent, 'recent_assets');
	    
	}
	
	public function assetGroupsByType($get){
	    
	    $this->requireOpenProject();
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    
	    $code = $this->getRequestParameter('asset_type');
	    
	    $types_array = SmartestDataUtility::getAssetTypes();
		
		if(in_array($code, array_keys($types_array))){
		    
		    $groups = $alh->getAssetGroupsThatAcceptType($this->getRequestParameter('asset_type'), $this->getSite()->getId());

    	    $this->send($groups, 'groups');
    	    $this->send($this->getRequestParameter('asset_type'), 'type_code');
    	    $this->send($types_array[$code], 'type');
		    
		}else{
		    $this->addUserMessageToNextRequest('The file type was not recognized.', SmartestUserMessage::ERROR);
		}
	    
	}
	
	public function newAssetGroup($get){
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    $asset_types = $alh->getTypes(array('templates'));
	    $placeholder_types = SmartestDataUtility::getAssetClassTypes(true);
	    
	    if($this->getRequestParameter('filter_type')){
	        $this->send($this->getRequestParameter('filter_type'), 'filter_type');
	    }else if($this->getRequestParameter('asset_type')){
	        $this->send($this->getRequestParameter('asset_type'), 'filter_type');
	    }
	    
	    if($this->getRequestParameter('group_label')){
	        $start_name = strip_tags($this->getRequestParameter('group_label'));
	    }else{
	        if($this->getRequestParameter('is_gallery')){
	            $start_name = 'Unnamed file gallery';
            }else{
                $start_name = 'Unnamed file group';
            }
	    }
	    
	    $this->send($start_name, 'start_name');
	    $this->send($alh->getGalleryPlaceholderTypes(), 'gallery_placeholder_types');
	    $this->send($alh->getGalleryAssetTypes(), 'gallery_asset_types');
	    $this->send($alh->getGalleryAssetGroups($this->getSite()->getId()), 'gallery_groups');
	    
	    if($this->getRequestParameter('is_gallery')){
	        $this->send(true, 'gallery_checked');
	    }
	    
	    $this->send($asset_types, 'asset_types');
	    $this->send($placeholder_types, 'placeholder_types');
	    
	}
	
	public function newAssetGroupFromPlaceholder($get){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $mode = ($this->getRequestParameter('mode') && $this->getRequestParameter('mode') == 'live') ? "live" : "draft";
	        
	        $draft_mode = ($mode == "draft");
	        
	        $definitions = $placeholder->getDefinitions($draft_mode, $this->getSite()->getId());
	        
	        $this->send($placeholder, 'placeholder');
	        $this->send($definitions, 'definitions');
	        $this->send($mode, 'mode');
	    
	    }
	    
	}
	
	public function createAssetGroup($get, $post){
	    
	    $this->requireOpenProject();
	    
	    if(strlen($this->getRequestParameter('asset_group_label'))){
	    
    	    $set = new SmartestAssetGroup;
    	    $set->setLabel($this->getRequestParameter('asset_group_label'));
    	    $set->setName(SmartestStringHelper::toVarName($this->getRequestParameter('asset_group_label')));
	    
    	    if($this->getRequestParameter('asset_group_mode') == 'SM_SET_ASSETGALLERY'){
    	        $set->setIsGallery(true);
    	        $type_var = $this->getRequestParameter('asset_gallery_type');
    	        $message = 'The gallery has been created.';
    	    }else{
    	        $set->setIsGallery(false);
    	        $type_var = $this->getRequestParameter('asset_group_type');
    	        $message = 'The file group has been created.';
    	    }
	    
    	    if($type_var == 'ALL'){
    	        $set->setFilterType('SM_SET_FILTERTYPE_NONE');
    	    }else{
    	        switch(substr($type_var, 0, 1)){
    	            case 'A':
    	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETTYPE');
    	            break;
    	            case 'P':
    	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
    	            break;
    	            case 'G':
    	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETGROUP');
    	            break;
    	        }
    	    }
	    
    	    $set->setFilterValue(($type_var == 'ALL') ? null : substr($type_var, 2));
    	    $set->setSiteId($this->getSite()->getId());
    	    $set->setShared(0);
    	    $set->save();
	    
    	    header("HTTP/1.1 201 Created");
    	    $this->redirect('/assets/editAssetGroupContents?group_id='.$set->getId());
    	    $this->addUserMessageToNextRequest($message, SmartestUserMessage::SUCCESS);
	    
        }else{
            
            $this->forward('assets', 'newAssetGroup');
            
        }
        
	    
	    
	}
	
	public function createNewAssetGroupFromPlaceholder($get, $post){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        if($this->getRequestParameter('asset_ids') && is_array($this->getRequestParameter('asset_ids'))){
	        
	            $set = new SmartestAssetGroup;
    	        $set->setLabel($this->getRequestParameter('asset_group_label'));
    	        $set->setName(SmartestStringHelper::toVarName($this->getRequestParameter('asset_group_label')));
    	        $set->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
    	        $set->setSiteId($this->getSite()->getId());
    	        $set->setShared(0);
    	        $set->setFilterValue($placeholder->getType());
    	        $set->save();
    	        header("HTTP/1.1 201 Created");
	        
	            foreach($this->getRequestParameter('asset_ids') as $asset_id){
	                $set->addAssetById($asset_id, false);
	            }
	            
	            $this->addUserMessageToNextRequest("A group was successfully created and ".count($this->getRequestParameter('asset_ids'))." files were added to it.", SmartestUserMessage::SUCCESS);
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
	
	public function deleteAssetGroup(){
	    
	    if($this->getUser()->hasToken('delete_asset_groups')){
	        
	        $group = new SmartestAssetGroup;
	        
	        if($group->find($this->getRequestParameter('group_id'))){
	            if($group->getIsSystem()){
	                $this->addUserMessageToNextRequest("The file group is part of system functioning and could not be deleted.", SmartestUserMessage::INFO);
	            }else{
	                $group->delete();
	                $this->addUserMessageToNextRequest("The file group was deleted", SmartestUserMessage::SUCCESS);
                }
	        }else{
	            $this->addUserMessageToNextRequest("The group ID wasn't recognized.", SmartestUserMessage::ERROR);
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have permission to delete file groups", SmartestUserMessage::ACCESS_DENIED);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function deleteAssetGroupConfirm(){
	    
	    if($this->getUser()->hasToken('delete_asset_groups')){
	        
	        $group = new SmartestAssetGroup;
	        
	        if($group->find($this->getRequestParameter('group_id'))){
	            $this->send($group, 'group');
	        }else{
	            $this->addUserMessageToNextRequest("The group ID wasn't recognized.", SmartestUserMessage::ERROR);
	            $this->formForward();
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have permission to delete file groups", SmartestUserMessage::ACCESS_DENIED);
	        $this->formForward();
	        
	    }
	    
	    
	    
	}
	
	public function browseAssetGroup($get){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    $mode = $this->getRequestParameter("mode", 1);
	    $this->send($this->getApplicationPreference('asset_list_style', 'grid'), 'list_view');
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('file group');
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        if(in_array($group->getFilterValue(), array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE', 'SM_ASSETCLASS_STATIC_IMAGE'))){
	            $this->send(true, 'contact_sheet_view');
	        }else{
	            $this->send(false, 'contact_sheet_view');
	        }
	        
	        $this->send($group->getMembers($mode, $this->getSite()->getId()), 'assets');
	        $this->send($group, 'group');
	        $this->send($mode, 'mode');
	        $this->send(count($group->getMembers($mode, $this->getSite()->getId(), false)), 'num_assets');
	        
	    }
	    
	}
	
	public function editAssetGroup($get){
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($this->getRequestParameter('group_id'))){
	        
	        $this->send($group, 'group');
	        $this->send($this->getUser()->hasToken('edit_file_group_names'), 'allow_name_edit');
	        
	        $is_empty = count($group->getMemberships()) == 0;
	        
	        $this->send($is_empty, 'allow_type_change');
	        
	        if($is_empty){
	        
    	        if($group->getIsGallery()){
    	            $alh = new SmartestAssetsLibraryHelper;
        	        $this->send($alh->getGalleryPlaceholderTypes(), 'placeholder_types');
        	        $this->send($alh->getGalleryAssetTypes(), 'asset_types');
        	    }else{
        	        $asset_types = SmartestDataUtility::getAssetTypes();
            	    $placeholder_types = SmartestDataUtility::getAssetClassTypes(true);
        	        
        	        $this->send($asset_types, 'asset_types');
            	    $this->send($placeholder_types, 'placeholder_types');
        	    }
    	    
	        }
	        
	        if($group->isUsableForPlaceholders()){
	            if($group->isUsedForPlaceholders()){
	                $this->send(false, 'allow_shared_toggle');
	            }else{
	                $this->send(true, 'allow_shared_toggle');
	            }
	        }else{
	            $this->send(true, 'allow_shared_toggle');
	        }
	        
	        // $group->allowNonShared();
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function updateAssetGroup($get, $post){
	    
	    $group = new SmartestAssetGroup;
	    $group_id = (int) $this->getRequestParameter('group_id');
	    
	    if($group->find($group_id)){
	        
	        $group->setLabel($this->getRequestParameter('group_label'));
	        
	        if($this->getUser()->hasToken('edit_file_group_names')){
	            $group->setName(SmartestStringHelper::toVarName($this->getRequestParameter('group_name')));
	        }
	        
	        if($group->isUsedForPlaceholders()){
                $group->setShared(1);
            }else{
                $shared = ($this->getRequestParameter('group_shared') && $this->getRequestParameter('group_shared')) ? 1 : 0;
                $group->setShared($shared);
            }
            
            if(count($group->getMemberships()) == 0){
                
                if($this->getRequestParameter('asset_group_type') == 'ALL'){
        	        $group->setFilterType('SM_SET_FILTERTYPE_NONE');
        	    }else{
        	        switch(substr($this->getRequestParameter('asset_group_type'), 0, 1)){
        	            case 'A':
        	            $group->setFilterType('SM_SET_FILTERTYPE_ASSETTYPE');
        	            break;
        	            case 'P':
        	            $group->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
        	            break;
        	        }
        	    }
        	    
        	    $group->setFilterValue(substr($this->getRequestParameter('asset_group_type'), 2));
                
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
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $this->setFormReturnUri();
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        $this->send($group->getOptions($this->getSite()->getId()), 'non_members');
	        $this->send($group->getMembers(0, $this->getSite()->getId(), false), 'members');
	        $this->send($group, 'group');
	        
	    }
	    
	}
	
	public function transferSingleAsset($get, $post){
	    
	    /* if($this->getRequestParameter('group_id')){
	        $request = $post;
	    }else{
	        $request = $get;
	    } */
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        $asset_id = (int) $this->getRequestParameter('asset_id');
	        $asset = new SmartestAsset;
	        
	        if($asset->find($asset_id)){
	            // TODO: Check that the asset is the right type for this group
	            if($this->getRequestParameter('transferAction') == 'add'){
	                $group->addAssetById($asset_id);
                }else{
                    $group->removeAssetById($asset_id);
                }
	        }
	        
	        if($this->getRequestParameter('from') == 'edit'){
                $this->redirect('/assets/editAsset?asset_id='.$asset->getId());
    	    }else{
    	        $this->formForward();
    	    }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	}
	
	public function transferAssets($get, $post){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        if($this->getRequestParameter('transferAction') == 'add'){
	            
	            $asset_ids = ($this->getRequestParameter('available_assets') && is_array($this->getRequestParameter('available_assets'))) ? $this->getRequestParameter('available_assets') : array();
	            
	            foreach($asset_ids as $aid){
	                $group->addAssetById($aid);
	            }
	            
	        }else{
	            
	            $asset_ids = ($this->getRequestParameter('used_assets') && is_array($this->getRequestParameter('used_assets'))) ? $this->getRequestParameter('used_assets') : array();
	            
	            foreach($asset_ids as $aid){
	                $group->removeAssetById($aid);
	            }
	            
	            if($group->getIsGallery()){
	                $group->fixOrderIndices();
	            }
	            
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function removeAssetFromGroup(){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        $asset_id = (int) $this->getRequestParameter('asset_id');
	        $asset = new SmartestAsset;
	        
	        if($asset->find($asset_id)){
	            // TODO: Check that the asset is the right type for this group
	            /* if($this->getRequestParameter('transferAction') == 'add'){
	                $group->addAssetById($asset_id);
                }else{ */
                    $group->removeAssetById($asset_id);
                    $group->fixOrderIndices();
                // }
	        }
	        
	        /* if($this->getRequestParameter('from') == 'edit'){
                $this->redirect('/assets/editAsset?asset_id='.$asset->getId());
    	    }else{ */
    	        $this->formForward();
    	    // }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function editAssetGalleryMembership(){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    $group = new SmartestAssetGroup;
	    
	    if($group->find($group_id)){
	        
	        if($group->getIsGallery()){
	            
	            if($membership = $group->getMembershipByAssetId($this->getRequestParameter('asset_id'))){
	                
	                $this->send($membership, 'membership');
	                $this->send($group, 'gallery');
	                
	                $this->send($group->getThumbnailOptions(), 'thumbnails');
	                $this->setTItle('Edit file gallery membership');
	                
	            }else{
	                
	                $this->addUserMessageToNextRequest('This gallery does not include that file', SmartestUserMessage::WARNING);
	                $this->formForward();
	                
	            }
	            
	        }else{
	            $this->addUserMessageToNextRequest('This file group is not a gallery', SmartestUserMessage::WARNING);
	            $this->formForward();
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The specified gallery was not found', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function updateAssetGalleryMembership(){
	    
	    $membership = new SmartestAssetGalleryMembership;
	    if($membership->find($this->getRequestParameter('membership_id'))){
	        $membership->setCaption($this->getRequestParameter('membership_caption'));
	        $membership->setThumbnailAssetId($this->getRequestParameter('membership_thumbnail_image_id'));
	        $membership->save();
	    }else{
	        $this->addUserMessageToNextRequest('The membership ID was not found');
	    }
	    $this->formForward();
	}
	
	/** End Asset Group Stuff **/
	
	public function assetInfo($get){
	    
	    $asset_id = $this->getRequestParameter('asset_id');
	    
	    $asset = new SmartestAsset;

		if($asset->find($asset_id)){
		    
		    // $this->setFormReturnUri();
		    
		    $data = $asset;
		    
		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		    $this->send($asset->getGroups(), 'groups');
		    $this->send($asset->getPossibleGroups(), 'possible_groups');
		    $this->send(new SmartestArray($asset->getOtherPointers()), 'pointers');
		    
		    if(isset($data['type_info']['source_editable']) && SmartestStringHelper::toRealBool($data['type_info']['source_editable'])){
		        $this->send(true, 'allow_source_edit');
		    }else{
		        $this->send(false, 'allow_source_edit');
		    }
		    
		    // var_dump(SmartestStringHelper::toRealBool($data['type_info']['supports_exif']));
		    
		    if(isset($data['type_info']['supports_exif']) && SmartestStringHelper::toRealBool($data['type_info']['supports_exif'])){
		        // echo "hello";
		        if($exif_data = $asset->getImage()->getExifData()){
		            // var_dump($exif_data);
		            $this->send(true, 'show_exif_panel');
	            }else{
	                $this->send(false, 'show_exif_panel');
	            }
		    }else{
		        $this->send(false, 'show_exif_panel');
		    }
		    
		    if(isset($data['type_info']['parsable']) && SmartestStringHelper::toRealBool($data['type_info']['parsable'])){
		        $this->send(true, 'show_publish');
		        $this->send(true, 'show_attachments');
		    }else{
		        $this->send(false, 'show_publish');
		        $this->send(false, 'show_attachments');
		    }
		    
		    $this->send($data, 'asset'); 
		    $this->send($asset->getPossibleOwners(), 'potential_owners');
		    
		}
	    
	}
	
	public function updateAssetInfo(){
	    
	    $asset = new SmartestAsset;
	    // echo $this->getRequestParameter('asset_id');
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        $asset->setLabel($this->getRequestParameter('asset_label'));
	        $asset->setUserId($this->getRequestParameter('asset_user_id'));
	        $asset->setShared($this->getRequestParameter('asset_shared') ? 1 : 0);
	        $asset->save();
	        
	        $this->addUserMessageToNextRequest("The asset has been updated.", SmartestUserMessage::SUCCESS);
	        
	        if($this->getRequestParameter('_submit_action') == "continue"){
    	        $this->redirect("/assets/assetInfo?asset_id=".$asset->getId());
    	    }else{
    	        // $this->addUserMessageToNextRequest($message, $message_type);
    	        $this->formForward();
    	    }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The asset ID was not recognized", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function resizeImageAsset(){
	    
	}
	
	public function assetCommentStream(){
        
        $asset = new SmartestAsset;
        $asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id) || $asset->findBy('stringid', $asset_id)){
		    
		    $this->send($asset, 'asset');
		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		
		}
        
    }
    
    public function editAsset($get, $post){
        
        if($this->getUser()->hasToken('modify_assets')){
        
            $this->requireOpenProject();
        
    		$asset_id = $this->getRequestParameter('asset_id');

    		if($this->getRequestParameter('from') == 'item_edit' && is_numeric($this->getRequestParameter('item_id'))){
    		    
    		    $ruri = '/datamanager/editItem?item_id='.$this->getRequestParameter('item_id');
    		    
    		    if($this->getRequestParameter('page_id')){
    		        $ruri .= '&page_id='.$this->getRequestParameter('page_id');
    		    }
		    
    		    $this->setTemporaryFormReturnUri($ruri);
		    
    		    if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    	            $this->setTemporaryFormReturnDescription(strtolower($item->getModel()->getName()));
    	        }
    		}

    		$asset = new SmartestAsset;
		
    		if($asset->find($asset_id)){
                
                if($asset->isTemplate()){
                    // The search can yield templates, so if that happens, just forward the user to the template editor without even needing a redirect
                    $this->setRequestParameter('template', $asset_id);
                    $this->forward('templates', 'editTemplate');
                }
                
                $assettype_code = $asset->getType();
    			$types_data = SmartestDataUtility::getAssetTypes();
    			$default_params = $asset->getDefaultParams();
    			$this->send(new SmartestArray($asset->getOtherPointers()), 'pointers');

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
        			$this->send($asset, 'asset');
    			
        			$this->send($asset->getGroups(), 'groups');
        		    $this->send($asset->getPossibleGroups(), 'possible_groups');
    		    
        		    $recent = $this->getUser()->getRecentlyEditedAssets($this->getSite()->getId(), $assettype_code);
          	        $this->send($recent, 'recent_assets');


    		    }else{
    		        // asset type is not supported
    		        $this->addUserMessageToNextRequest('This file type currently isn\'t supported.', SmartestUserMessage::WARNING);
        	        $this->formForward();
    		    }

    		}else{
    		    // asset ID was not recognised
    		    $this->addUserMessageToNextRequest('The file ID wasn\'t recognized.', SmartestUserMessage::ERROR);
    	        $this->formForward();
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have permission to edit files.", SmartestUserMessage::ACCESS_DENIED);
	        $this->formForward();
	        
	    }
		
	}
	
	public function editTextFragmentSource($get, $post){

		$asset_id = $this->getRequestParameter('asset_id');

		/* if(!$this->getRequestParameter('from')){
		    // $this->setFormReturnUri();
		} */

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
    			$this->send($asset, 'asset');

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
	    
	    $asset_id = $this->getRequestParameter('asset_id');
	    
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
	    
	    $asset_id = $this->getRequestParameter('asset_id');
        
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
	    
	    $asset_id = (int) $this->getRequestParameter('asset_id');
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
	    
	    $asset_id = (int) $this->getRequestParameter('asset_id');
	    
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrate($asset_id)){
	        
	        $user = new SmartestUser;
	        $user_id = (int) $this->getRequestParameter('todoitem_receiving_user_id');
	        
	        if($user->hydrate($user_id)){
	            
	            // $user->assignTodo('SM_TODOITEMTYPE_EDIT_ITEM', $item_id, $this->getUser()->getId(), SmartestStringHelper::sanitize())
	            
	            $todo_type = SmartestStringHelper::sanitize($this->getRequestParameter('todoitem_type'));
	            
	            $type = SmartestTodoListHelper::getType($todo_type);
                
                $message = SmartestStringHelper::sanitize($this->getRequestParameter('todoitem_description'));
                
        	    if(isset($message{1})){
        	        $input_message = SmartestStringHelper::sanitize($message);
        	    }else{
        	        $input_message = $type->getDescription();
        	    }
        	    
        	    $priority = (int) $this->getRequestParameter('todoitem_priority');
        	    $size     = (int) $this->getRequestParameter('todoitem_size');
	            
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
        	        
        	        $message = 'Hi '.$user.",\n\n".$this->getUser()." has added a new task to your to-do list. Please visit ".$this->getRequest()->getDomain()."smartest/todo for more information.\n\nYours truly,\nThe Smartest Web Content Management Platform";
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
	    $asset_id = (int) $this->getRequestParameter('asset_id');
	    
	    if(!defined('SM_CMS_PAGE_SITE_ID')){
	        define('SM_CMS_PAGE_SITE_ID', $this->getSite()->getId());
        }
	    
	    if($asset->find($asset_id)){
	        
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
		    
		    // for html reusability
    	    $this->send($asset['type_info'], 'asset_type');
    	    $html = $asset->renderPreview();
    	    
    	    $this->send($html, 'html');
    	    $this->send($asset, 'asset');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The asset ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function showPreviewedAssetContent(){
	    
	    $this->getRequest()->setMeta('template', '_blank.tpl');
	    // print_r($this->getRequest());
	    
	}
	
	public function assetTags(){
	    
	    if(!$this->getRequestParameter('from')){
	        $this->setFormReturnUri();
        }
	    
	    $asset_id = $this->getRequestParameter('asset_id');
	    $asset = new SmartestAsset;
	    
	    if($asset->find($asset_id)){
	        
	        $this->setTitle($asset->getLabel().' | Tags');
	        
	        $du  = new SmartestDataUtility;
	        $tags = $du->getTags();
	        
	        $asset_tags = array();
	        $i = 0;
	        
	        foreach($tags as $t){
	            
	            $asset_tags[$i] = $t;
	            
	            if($t->hasAsset($asset->getId())){
	                $asset_tags[$i]['attached'] = true;
	                // echo $t['label'];
	            }else{
	                $asset_tags[$i]['attached'] = false;
	            }
	            
	            $i++;
	        }
	        
	        $this->send($asset_tags, 'tags');
	        $this->send($asset, 'asset');
	        
	    }else{
	        $this->addUserMessage('The item ID has not been recognized.', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function textFragmentElements($get){
	    
	    $asset_id = $this->getRequestParameter('asset_id');

		/* if(!$this->getRequestParameter('from')){
		    $this->setFormReturnUri();
		} */
		
		// if($this->getRequestParameter('from') == 'item_edit' && is_numeric($this->getRequestParameter('item_id'))){
		    
		    // $ruri = '/datamanager/editItem?item_id='.$this->getRequestParameter('item_id');
		    
		    /* if($this->getRequestParameter('page_id')){
		        $ruri .= '&page_id='.$this->getRequestParameter('page_id');
		    } */
		    
		    /* $this->setTemporaryFormReturnUri();
		    $this->setTemporaryFormReturnDescription('attachments');
		    
		    if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
	            $this->setTemporaryFormReturnDescription(strtolower($item->getModel()->getName()));
	        } */
		// }

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
        		        // $this->setTemporaryFormReturnUri();
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
    			$this->send($asset, 'asset');

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
        
        $asset_id = $this->getRequestParameter('asset_id');

		$asset = new SmartestAsset;

		if($asset->find($asset_id)){
            
            if($asset->getUserId() == $this->getUser()->getId() || $this->getUser()->hasToken('modify_assets')){
            
		        $param_values = serialize($this->getRequestParameter('params'));
    		    $asset->setParameterDefaults($param_values);

    		    $content = $this->getRequestParameter('asset_content');
    		    $content = SmartestStringHelper::unProtectSmartestTags($content);
    		    $content = SmartestTextFragmentCleaner::convertDoubleLineBreaks($content);
    		    $asset->setContent($content);
    	        $asset->setLanguage(strtolower(substr($this->getRequestParameter('asset_language'), 0, 3)));
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
	    
	    /* if($this->getRequestParameter('_submit_action') == "continue" && $success){
	        if($this->getRequestParameter('editor') && $this->getRequestParameter('editor') == 'source'){
	            $this->redirect("/assets/editTextFragmentSource?asset_id=".$asset->getId());
	        }else{
	            $this->redirect("/assets/editAsset?asset_type=".$asset->getType()."&asset_id=".$asset->getId());
		    }
	    }else{
	        // $this->addUserMessageToNextRequest($message, $message_type);
	        $this->formForward();
	    } */
	    
	    $this->handleSaveAction();

	}
	
	public function defineAttachment($get){
	    
	    $asset_id = $this->getRequestParameter('asset_id');
        $attachment_name = SmartestStringHelper::toVarName($this->getRequestParameter('attachment'));
        $this->send($attachment_name, 'attachment_name');
        
		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){
            
           if($asset->getUserId() == $this->getUser()->getId() || $this->getUser()->hasToken('modify_assets')){
            
			    $assettype_code = $asset->getType();
    			$types_data = SmartestDataUtility::getAssetTypes();
			
    			if(array_key_exists($assettype_code, $types_data)){
                
                    // $attachable_files = $this->manager->getAttachableFiles($this->getSite()->getId());
                    $helper = new SmartestAssetsLibraryHelper;
            	    $attachable_files = $helper->getAttachableFiles($this->getSite()->getId());
            	    
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
                        
                        if($this->getRequestParameter('from') != 'pagePreviewDirectEdit'){
                		    
                		    $ruri = '/assets/textFragmentElements?asset_id='.$asset->getId();
                		    
                		    if($this->getRequestParameter('from')){
                		        $ruri .= '&from='.$this->getRequestParameter('from');
                		    }
                		    
                		    if($this->getRequestParameter('item_id')){
                		        $ruri .= '&item_id='.$this->getRequestParameter('item_id');
                		    }
                		    
                		    if($this->getRequestParameter('page_id')){
                		        $ruri .= '&page_id='.$this->getRequestParameter('page_id');
                		    }
                		    
                		    if($this->getRequestParameter('author_id')){
                		        $ruri .= '&author_id='.$this->getRequestParameter('author_id');
                		    }
                		    
                		    if($this->getRequestParameter('search_query')){
                		        $ruri .= '&search_query='.$this->getRequestParameter('search_query');
                		    }
                		    
                		    if($this->getRequestParameter('tag')){
                		        $ruri .= '&tag='.$this->getRequestParameter('tag');
                		    }

                		    $this->setTemporaryFormReturnUri($ruri);
                            
                            // echo "Set temporary form return URI as ".$uri;
                            
                		    /* if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                	            $this->setTemporaryFormReturnDescription(strtolower($item->getModel()->getName()));
                	        } */
                		}
                
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
	    
	    $textfragment_id = $this->getRequestParameter('textfragment_id');
	    $attachment_name = SmartestStringHelper::toVarName($this->getRequestParameter('attachment_name'));
	    
	    $tf = new SmartestTextFragment;
	    
	    if($tf->find($textfragment_id)){
	        
	        $current_def = $tf->getAttachmentCurrentDefinition($attachment_name);
	        
	        if(!$current_def->getTextFragmentId()){
	            $current_def->setTextFragmentId($textfragment_id);
	        }
	        
	        $current_def->setAttachedAssetId((int) $this->getRequestParameter('attached_file_id'));
	        $current_def->setAttachmentName($attachment_name);
	        $current_def->setZoomFromThumbnail($this->getRequestParameter('attached_file_zoom'));
	        $current_def->setThumbnailRelativeSize((int) $this->getRequestParameter('thumbnail_relative_size'));
	        $current_def->setCaption(htmlentities($this->getRequestParameter('attached_file_caption')));
	        $current_def->setAlignment(SmartestStringHelper::toVarName($this->getRequestParameter('attached_file_alignment')));
	        $current_def->setCaptionAlignment(SmartestStringHelper::toVarName($this->getRequestParameter('attached_file_caption_alignment')));
	        $current_def->setFloat($this->getRequestParameter('attached_file_float'));
	        $current_def->setBorder($this->getRequestParameter('attached_file_border'));
	        
	        $current_def->save();
	        
	    }else{
	        $this->addUserMessage('The textfragment ID was not recognized.', SmartestUserMessage::ERROR);
	    }
	    
	    
	    $this->formForward();
	    
	}

	public function deleteAssetConfirm($get){

		$asset_id = $this->getRequestParameter('asset_id');

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

		$asset_id = $this->getRequestParameter('asset_id');

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

	public function duplicateAsset(){

		$asset_id = $this->getRequestParameter('asset_id');
		
		$asset = new SmartestAsset;

		if($asset->find($asset_id)){
		    
		    $dup = $asset->duplicate($this->getRequestParameter('duplicate_asset_name', $asset->getLabel().' copy'));
		    $dup->setUserId($this->getUser()->getId()); // Connect the new asset to the current user, rather than the user who created the original
		    $dup->save();
		    $this->addUserMessageToNextRequest("The file has been duplicated as ".$dup->getLabel().".", SmartestUserMessage::SUCCESS);
		    
		}else{
		    
		    $this->addUserMessageToNextRequest("The asset ID was not recognized.", SmartestUserMessage::ERROR);
		    
		}

		$this->formForward();
	}

	public function downloadAsset($get){

		$asset_id = $this->getRequestParameter('asset_id');

		$asset = new SmartestAsset;

		if($asset->find($asset_id)){
            
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
	    
	    $asset_id = $this->getRequestParameter('asset_id');

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
	
	/* public function attachCommentToAsset($get, $post){
	    
	    $asset_id = $this->getRequestParameter('asset_id');

		// echo $asset_id;
		$asset = new SmartestAsset;

		if($asset->hydrate($asset_id)){

		    $asset->addComment($this->getRequestParameter('comment_content'), $this->getUser()->getId());
		    $this->formForward();
		    // $this->redirect('/assets/comments?asset_id='.$asset->getId());

		}else{
		    $this->addUserMessageToNextRequest("The asset ID was not recognized.", SmartestUserMessage::ERROR);
		    $this->redirect('/smartest/assets');
		}
	    
	} */
	
	public function useAsset(){
	    
	    
	    
	}
		
}
