<?php

class MetaData extends SmartestSystemApplication{

	public function startPage(){
	    
	}
	
	public function listTags(){
	    
	    $this->setFormReturnUri();
	    $du = new SmartestDataUtility;
	    $tags = $du->getTagsAsArrays();
	    $this->send($tags, 'tags');
	    
	}
	
    public function listFields(){
	    
		$this->setFormReturnUri();
		
		$site_id = $this->getSite()->getId();
		
		$sql = "SELECT * FROM PageProperties WHERE pageproperty_site_id ='$site_id'";
		$database = SmartestDatabase::getInstance('SMARTEST');
		$fields = $database->queryToArray($sql);
		
		$this->send($fields, 'fields');
		$this->send($site_id, 'site_id');
		
		// return array("fields"=>$fields, "site_id"=>$site_id);
		
	}
	
	public function deletePageProperty(){
		
		if($this->getUser()->hasToken('delete_page_property')){
		
    		$field_id = $this->getRequestParameter('field_id');
		
    		$property = new SmartestPageField;
		
    		if($property->find($field_id)){
    		    $property->clearAllDefinitions();
    		    $property->delete();
    		    $this->addUserMessageToNextRequest('The property has been deleted.', SmartestUserMessage::SUCCESS);
    		}else{
    		    $this->addUserMessageToNextRequest('The property ID was not recognised.', SmartestUserMessage::ERROR);
		    
    		}
		
	    }else{
	        $this->addUserMessageToNextRequest('You do not have permission to delete fields', SmartestUserMessage::ACCESS_DENIED);
	    }
		
		$this->formForward();
		
	}
	
	public function viewPageFieldDefinitions(){
	    
	    $field = new SmartestPageField;
	    
	    if($field->find($this->getRequestParameter('field_id'))){
	        // $definitions = $field->getDefinitionsAsArrays();
	        // print_r($definitions);
	        $this->setTitle('Page Field Definitions | '.$field->getLabel());
	        $definitions =  $this->manager->getAllFieldDefinitions($this->getRequestParameter('field_id'));
	        // $definition = new SmartestPageFieldDefinition;
	        // $definition->
	        $this->send($field, 'field');
	        $this->send($definitions, 'definitions');
	    }else{
	        $this->addUserMessageToNextRequest("The field ID was not recognised.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function clearFieldOnAllPages(){
	    
	    if($this->getUser()->hasToken('clear_pageproperty_all_definitions')){
	    
    	    $field_id = $this->getRequestParameter('field_id');
		
    		$property = new SmartestPageField;
		
    		if($property->find($field_id)){
    		    $property->clearAllDefinitions();
    		    $this->addUserMessageToNextRequest('All values of the property have been deleted.', SmartestUserMessage::SUCCESS);
    		}else{
    		    $this->addUserMessageToNextRequest('The property ID was not recognised.', SmartestUserMessage::ERROR);
		    
    		}
		
	    }else{
	        $this->addUserMessageToNextRequest('You do not have permission to clear all the definitions of a field', SmartestUserMessage::ACCESS_DENIED);
	    }
		
		$this->formForward();
	}
	
	public function defineFieldOnPage(){
		
		if($this->getUser()->hasToken('modify_page_properties')){
		
    		$page_webid = $this->getRequestParameter('page_id');
		
    		$page = new SmartestPage;
		
    		if($page->hydrate($page_webid)){
		
    		    $field = new SmartestPageField;
		
        		if($this->requestParameterIsSet('assetclass_id')){
        			// $pageproperty_name = $this->getRequestParameter('assetclass_id');
        			// $pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $this->getRequestParameter('assetclass_id'), "PageProperties");
        			// $field->hydrateBy();
        			$lookup_field = 'pageproperty_name';
        			$value = $this->getRequestParameter('assetclass_id');
        		}else{
        			// $pageproperty_id = $this->getRequestParameter('pageproperty_id');
        			// $pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
        			$lookup_field = 'pageproperty_id';
        			$value = $this->getRequestParameter('pageproperty_id');
        		}
		    
    		    $db = SmartestPersistentObject::get('db:main');
    		    $sql = "SELECT * FROM PageProperties WHERE ".$lookup_field."='".$value."' AND pageproperty_site_id='".$page->getSiteId()."'";
    		    $result = $db->queryToArray($sql);
		    
        		if(count($result)){
		        
    		        $field->hydrate($result[0]);
		        
        		    $def = new SmartestPageFieldDefinition;
            		$def->loadForUpdate($field, $page);
        		
            		if($field->getType() == 'SM_DATATYPE_DROPDOWN_MENU'){
        		        $dropdown_id = $field->getForeignKeyFilter();
        		        $dropdown = new SmartestDropdown;
        		        $dropdown->find($dropdown_id);
        		        $options = $dropdown->getOptions();
        		        $this->send($options, 'options');
        		    }
    		    
        		    $this->send($def->getDraftValue(), 'value');
        		    $this->send($field->getName(), 'field_name');
        		    $this->send($field->getType(), 'field_type');
        		    $this->send($field->getId(), 'field_id');
        		    $this->send($field, 'field');
        		    $this->send($page->getId(), 'page_id');
		
        	    }else{
    	        
        	        $this->addUserMessageToNextRequest('The specified field doesn\'t exist', SmartestUserMessage::INFO);
        	        $this->formForward();
    	        
        	    }
	    
            }else{
            
                $this->addUserMessageToNextRequest('The page ID wasn\'t recognized.', SmartestUserMessage::ERROR);
    	        $this->formForward();
            
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to edit the properties of pages. This includes page fields.", SmartestUserMessage::ACCESS_DENIED);
	        $this->formForward();
            
        }
		
	}
	
	/* public function setLiveProperty(){
		
		$page_webid = $this->getRequestParameter('page_id');
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
		if($this->requestParameterIsSet('assetclass_id')){
			$pageproperty_name = $this->getRequestParameter('assetclass_id');
			$pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $this->getRequestParameter('assetclass_id'), "PageProperties");
		}else{
			$pageproperty_id = $this->getRequestParameter('pageproperty_id');
			$pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
		}
		
		$pagepropertyvalue_id = $this->manager->getPropertyValueId($page_id, $pageproperty_id);
		
		$this->manager->setLiveProperty($pagepropertyvalue_id);
		$this->formForward();
		
	} */
	
	public function updatePagePropertyValue(){
		
		$page_webid = $this->getRequestParameter('page_id');
		$page = new SmartestPage;
		
		if($page->hydrate($page_webid)){
		
		    $field = new SmartestPageField;
		
    		if($this->requestParameterIsSet('field_id')){
    			$lookup_field = 'id';
    			$value = $this->getRequestParameter('field_id');
    		}
    		
    		if($field->findBy($lookup_field, $value)){
		
    		    $def = new SmartestPageFieldDefinition;
        		$def->loadForUpdate($field, $page);
        		$def->setDraftValue($this->getRequestParameter('field_content'));
        		
        		if($field->getIsSitewide()){
        		    $def->setSiteId($page->getSiteId());
        		}else{
        		    $def->setPageId($page->getId());
    		    }
    		    
        		$def->setPagepropertyId($field->getId());
        		$def->save();
        		
        		$this->addUserMessageToNextRequest('The page field has been updated', SmartestUserMessage::SUCCESS);
    	        $this->formForward();
		
    	    }else{
    	        
    	        $this->addUserMessageToNextRequest('The specified field doesn\'t exist', SmartestUserMessage::INFO);
    	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The page ID wasn\'t recognized.', SmartestUserMessage::ERROR);
	        // $this->formForward();
            
        }
		
		/* $page_webid = $this->getRequestParameter('page_id');
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $this->getRequestParameter('pageproperty_id');
		$propertyValue = $this->getRequestParameter('page_property');
		$pagepropertyvalue_id = $this->getRequestParameter('pagepropertyvalue_id');
		
		$this->manager->updatePropertyValue($page_id,$pageproperty_id,$propertyValue,$pagepropertyvalue_id);
		$this->formForward(); */
		
	}
	
	/* public function savePagePropertyValue(){
		
		$page_webid = $this->getRequestParameter('page_id');
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $this->getRequestParameter('pageproperty_id');
		$propertyValue = $this->getRequestParameter('page_property');
		
		if($this->manager->getPropertyDefinitionExists($pageproperty_id, $page_id)){
			// definition already exists
			$this->manager->updatePropertyValue($page_id, $pageproperty_id, $propertyValue);
		}else{
			// insert new definition
			$this->manager->insertPropertyValue($page_id, $pageproperty_id, $propertyValue);
		}
		
		$this->formForward();
		
	} */
	
	public function addPageProperty(){
	    
		$site_id = $this->getRequestParameter('site_id');
		$propertyTypes = $this->manager->getPropertyTypes();
		
		$types = SmartestDataUtility::getDataTypes('field');
		$this->send($types, 'property_types');
		
		$acceptable_types = array_keys($types);
		
		if($this->requestParameterIsSet('name')){
		    $this->send(SmartestStringHelper::toVarName($this->getRequestParameter('name')), 'field_name');
	    }
	    
	    $this->send($this->getSite(), 'site');
		
	}
	
	public function insertPageProperty(){
		
		$property = new SmartestPageField;
		$property_name = $this->getRequestParameter('property_name');
		$property->setLabel($property_name);
		$property->setSiteId($this->getSite()->getId());
		$property->setType($this->getRequestParameter('pageproperty_type'));
		$property->setName(SmartestStringHelper::toVarName($property_name));
		$property->setIsSitewide($this->getRequestParameter('property_sitewide', '0'));
		
		if($this->requestParameterIsSet('foreign_key_filter')){
		    $property->setForeignKeyFilter($this->getRequestParameter('foreign_key_filter'));
		}
		
		$property->save();
		
		$this->formForward();
	}
	
	public function addPagePropertyValue(){
		
		$page_webid = $this->getRequestParameter('page_id');
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $this->getRequestParameter('pageproperty_id');
		$propertyValue = $this->getRequestParameter('page_property');
		
		$this->manager->insertPropertyValue($page_id, $pageproperty_id, $propertyValue);
		$this->formForward();
		
	}
	
	public function undefinePageProperty(){
		
		$page_webid =$this->getRequestParameter('page_id');
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
		$field = new SmartestPageField;
		
		if($this->requestParameterIsSet('assetclass_id')){
		    $field_name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
			// $pageproperty_name = $this->getRequestParameter('assetclass_id');
			// $pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $this->getRequestParameter('assetclass_id'), "PageProperties");
			$field->hydrateBy('name', $field_name);
		}else{
		    $field_id = $this->getRequestParameter('pageproperty_id');
		    $field->hydrate($field_id);
			// $pageproperty_id = $this->getRequestParameter('pageproperty_id');
			// $pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
		}
		
		// $pageproperty_id = $this->getRequestParameter('pageproperty_id');
		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
		
		$pagepropertyvalue_id = $this->manager->getPropertyValueId($page_id, $field->getId());
		
		// $pageproperty_id = $this->getRequestParameter('pageproperty_id');
		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
		
		$this->manager->undefinePageProperty($pagepropertyvalue_id);
		$this->formForward();
		
	}
	
	public function tagsFront(){
	    
	}
	
	public function addTag(){
	    
	    if(is_numeric($this->getRequestParameter('item_id'))){
	        $item = new SmartestItem;
	        if($item->find($this->getRequestParameter('item_id'))){
	            $this->send($item, 'item');
	            if($this->getRequestParameter('page_webid')){
	                $this->send($this->getRequestParameter('page_webid'), 'page_webid');
	            }
	        }
	    }
	    
	    if(is_numeric($this->getRequestParameter('page_id'))){
	        $page = new SmartestPage;
	        if($page->find($this->getRequestParameter('page_id'))){
	            $this->send($page, 'page');
	        }
	    }
	    
	    if(is_numeric($this->getRequestParameter('asset_id'))){
	        $asset = new SmartestAsset;
	        if($asset->find($this->getRequestParameter('asset_id'))){
	            $this->send($asset, 'asset');
	        }
	    }
	    
	}
	
	public function insertTag(){
	    
	    $proposed_tags = SmartestStringHelper::fromSeparatedStringList($this->getRequestParameter('tag_label')); // Separates by commas or semicolons
	    
	    $num_new_tags = 0;
	    $tag_item = false;
	    
	    if($this->getRequestParameter('tag_item') && is_numeric($this->getRequestParameter('item_id'))){
	        $item = new SmartestItem;
	        if($item->find($this->getRequestParameter('item_id'))){
	            $tag_item = true;
	        }
	    }
	    
	    if($this->getRequestParameter('tag_page') && is_numeric($this->getRequestParameter('page_id'))){
	        $page = new SmartestPage;
	        if($page->find($this->getRequestParameter('page_id'))){
	            $tag_page = true;
	        }
	    }
	    
	    if($this->getRequestParameter('tag_asset') && is_numeric($this->getRequestParameter('asset_id'))){
	        $asset = new SmartestAsset;
	        if($asset->find($this->getRequestParameter('asset_id'))){
	            $tag_asset = true;
	        }
	    }
	    
	    foreach($proposed_tags as $tag_label){
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_label, true);
	        
	        if(strlen($tag_label) && strlen($tag_name)){
	        
        	    $tag = new SmartestTag;
        	    $existing_tags = array();
	    
        	    if($tag->hydrateBy('name', $tag_name)){
        	        // $this->addUserMessageToNextRequest("A tag with that name already exists.", SmartestUserMessage::WARNING);
        	        $existing_tags[] = "'".$tag_label."'";
        	    }else{
        	        $tag->setName($tag_name);
        	        $tag->setLabel(SmartestStringHelper::toTitleCase($tag_label)); // Capitalises first letter of words for neatness
        	        $tag->save();
        	        
        	        if($tag_item){
        	            $item->tag($tag->getId());
        	        }
        	        if($tag_page){
        	            $page->tag($tag->getId());
        	        }
        	        if($tag_asset){
        	            $asset->tag($tag->getId());
        	        }
        	        $num_new_tags++;
        	    }
    	    
	        }
	    
        }
        
        $message = $num_new_tags.' tag successfully added.';
        
        if(count($existing_tags)){
            $message .= ' Tags '.SmartestStringHelper::toCommaSeparatedList($existing_tags).' already existed.';
            $type = SmartestUserMessage::INFO;
        }else{
            $type = SmartestUserMessage::SUCCESS;
        }
        
        $this->addUserMessageToNextRequest($message, $type);
        
        if($tag_item){
            $url = '/datamanager/itemTags?item_id='.$item->getId();
            if($this->getRequestParameter('page_webid')){
                $url .= '&page_id='.$this->getRequestParameter('page_webid');
            }
            $this->redirect($url);
        }
        
        if($tag_page){
            // $page->tag($tag->getId());
            $this->redirect('/websitemanager/pageTags?page_id='.$page->getWebId());
        }
        
        if($tag_asset){
            // $asset->tag($tag->getId());
            $this->redirect('/assets/assetTags?asset_id='.$asset->getId());
        }
        
        $this->formForward();
	    
	}
	
	public function getTaggedObjects(){
	    
	    $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag'));
	    $tag = new SmartestTag;
	    
	    if($tag->findBy('name', $tag_identifier)){
	        $this->send($tag, 'tag');
	        // $objects = $tag->getObjectsOnSite($this->getSite()->getId(), true);
	        $this->send(new SmartestArray($tag->getSimpleItems($this->getSite()->getId(), true)), 'items');
	        $this->send(new SmartestArray($tag->getPages($this->getSite()->getId())), 'pages');
	        $this->send(new SmartestArray($tag->getAssets($this->getSite()->getId())), 'assets');
	    }else{
	        $objects = array();
	        $this->addUserMessage("This tag does not exist.", SmartestUserMessage::WARNING);
	    }
	    
	    
	    
	    
	}
	
	public function linkShorteningOptions(){
	    
	}
	
	public function updateLinkShorteningOptions(){
	    
	}

}