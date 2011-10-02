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
	
	public function deletePageProperty($get,$post){
		
		$field_id = $get['field_id'];
		
		$property = new SmartestPageField;
		
		if($property->hydrate($field_id)){
		    $property->clearAllDefinitions();
		    $property->delete();
		    $this->addUserMessageToNextRequest('The property has been deleted.', SmartestUserMessage::SUCCESS);
		}else{
		    $this->addUserMessageToNextRequest('The property ID was not recognised.', SmartestUserMessage::ERROR);
		    
		}
		
		$this->formForward();
		
	}
	
	public function viewPageFieldDefinitions($get){
	    
	    $field = new SmartestPageField;
	    
	    if($field->hydrate($get['field_id'])){
	        // $definitions = $field->getDefinitionsAsArrays();
	        // print_r($definitions);
	        $this->setTitle('Page Field Definitions | '.$field->getLabel());
	        $definitions =  $this->manager->getAllFieldDefinitions($get['field_id']);
	        // $definition = new SmartestPageFieldDefinition;
	        // $definition->
	        $this->send($field->__toArray(), 'field');
	        $this->send($definitions, 'definitions');
	    }else{
	        $this->addUserMessageToNextRequest("The field ID was not recognised.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function clearFieldOnAllPages($get){
	    
	    $field_id = $get['field_id'];
		
		$property = new SmartestPageField;
		
		if($property->hydrate($field_id)){
		    $property->clearAllDefinitions();
		    $this->addUserMessageToNextRequest('All values of the property have been deleted.', SmartestUserMessage::SUCCESS);
		}else{
		    $this->addUserMessageToNextRequest('The property ID was not recognised.', SmartestUserMessage::ERROR);
		    
		}
		
		$this->formForward();
	}
	
	public function defineFieldOnPage($get){
		
		$page_webid = $get['page_id'];
		
		$page = new SmartestPage;
		
		if($page->hydrate($page_webid)){
		
		    $field = new SmartestPageField;
		
    		if(!empty($get['assetclass_id'])){
    			// $pageproperty_name = $get['assetclass_id'];
    			// $pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $get['assetclass_id'], "PageProperties");
    			// $field->hydrateBy();
    			$lookup_field = 'pageproperty_name';
    			$value = $get['assetclass_id'];
    		}else{
    			// $pageproperty_id = $get['pageproperty_id'];
    			// $pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
    			$lookup_field = 'pageproperty_id';
    			$value = $get['pageproperty_id'];
    		}
		    
		    $db = SmartestPersistentObject::get('db:main');
		    $sql = "SELECT * FROM PageProperties WHERE ".$lookup_field."='".$value."' AND pageproperty_site_id='".$page->getSiteId()."'";
		    $result = $db->queryToArray($sql);
		    
    		if(count($result)){
		        
		        // $field->hydrateBy($lookup_field, $value)
		        $field->hydrate($result[0]);
		        
    		    // print_r($field);
		
        		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
        		// $sql = "SELECT pagepropertyvalue_draft_value FROM PagePropertyValues WHERE pagepropertyvalue_page_id ='$page_id' AND pagepropertyvalue_pageproperty_id='$pageproperty_id'";
		
        		// $result = $this->manager->database->queryToArray($sql);
        		// $pageproperty_value = $result[0];
    		
        		$def = new SmartestPageFieldDefinition;
        		$def->loadForUpdate($field, $page);
        		
        		// print_r($def);
		
    		    /* return array(
        			"page_id" => $page_webid,
        			"pageproperty_id" => $pageproperty_id,
        			"pageproperty_name" => $pageproperty_name,
        			"pageproperty_value" => $pageproperty_value,
    			    "pagepropertyvalue_id" => $pagepropertyvalue_id
    		    ); */
    		    
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
    		    $this->send($page->getId(), 'page_id');
		
    	    }else{
    	        
    	        $this->addUserMessageToNextRequest('The specified field doesn\'t exist', SmartestUserMessage::INFO);
    	        $this->formForward();
    	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The page ID wasn\'t recognized.', SmartestUserMessage::ERROR);
	        $this->formForward();
            
        }
		
	}
	
	public function setLiveProperty($get){
		
		$page_webid = $get['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
		if(!empty($get['assetclass_id'])){
			$pageproperty_name = $get['assetclass_id'];
			$pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $get['assetclass_id'], "PageProperties");
		}else{
			$pageproperty_id = $get['pageproperty_id'];
			$pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
		}
		
		$pagepropertyvalue_id = $this->manager->getPropertyValueId($page_id, $pageproperty_id);
		
		$this->manager->setLiveProperty($pagepropertyvalue_id);
		$this->formForward();
		
	}
	
	public function updatePagePropertyValue($get, $post){
		
		$page_webid = $post['page_id'];
		
		// $page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
		$page = new SmartestPage;
		
		if($page->hydrate($page_webid)){
		
		    $field = new SmartestPageField;
		
    		if(!empty($post['field_id'])){
    			// $pageproperty_name = $get['assetclass_id'];
    			// $pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $get['assetclass_id'], "PageProperties");
    			// $field->hydrateBy();
    			$lookup_field = 'id';
    			$value = $post['field_id'];
    		}
    		
    		//else{
    			// $pageproperty_id = $get['pageproperty_id'];
    			// $pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
    		//	$lookup_field = 'id';
    		//	$value = $post['pageproperty_id'];
    		//}
		
    		if($field->findBy($lookup_field, $value)){
		
    		    // print_r($field);
		
        		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
        		// $sql = "SELECT pagepropertyvalue_draft_value FROM PagePropertyValues WHERE pagepropertyvalue_page_id ='$page_id' AND pagepropertyvalue_pageproperty_id='$pageproperty_id'";
		
        		// $result = $this->manager->database->queryToArray($sql);
        		// $pageproperty_value = $result[0];
    		
        		$def = new SmartestPageFieldDefinition;
        		$def->loadForUpdate($field, $page);
        		$def->setDraftValue($post['field_content']);
        		$def->setPageId($page->getId());
        		$def->setPagepropertyId($field->getId());
        		$def->save();
        		
        		$this->addUserMessageToNextRequest('The page field has been updated', SmartestUserMessage::SUCCESS);
    	        $this->formForward();
        		
        		// print_r($def);
		
    		    /* return array(
        			"page_id" => $page_webid,
        			"pageproperty_id" => $pageproperty_id,
        			"pageproperty_name" => $pageproperty_name,
        			"pageproperty_value" => $pageproperty_value,
    			    "pagepropertyvalue_id" => $pagepropertyvalue_id
    		    ); */
    		    
    		    // $this->send($def->getDraftValue(), 'value');
    		    // $this->send($field->getName(), 'field_name');
    		    // $this->send($page->getId(), 'page_id');
		
    	    }else{
    	        
    	        
    	        $this->addUserMessageToNextRequest('The specified field doesn\'t exist', SmartestUserMessage::INFO);
    	        // $this->formForward();
    	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The page ID wasn\'t recognized.', SmartestUserMessage::ERROR);
	        // $this->formForward();
            
        }
		
		/* $page_webid = $post['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $post['pageproperty_id'];
		$propertyValue = $post['page_property'];
		$pagepropertyvalue_id = $post['pagepropertyvalue_id'];
		
		$this->manager->updatePropertyValue($page_id,$pageproperty_id,$propertyValue,$pagepropertyvalue_id);
		$this->formForward(); */
		
	}
	
	public function savePagePropertyValue($get, $post){
		
		$page_webid = $post['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $post['pageproperty_id'];
		$propertyValue = $post['page_property'];
		
		if($this->manager->getPropertyDefinitionExists($pageproperty_id, $page_id)){
			// definition already exists
			$this->manager->updatePropertyValue($page_id, $pageproperty_id, $propertyValue);
		}else{
			// insert new definition
			$this->manager->insertPropertyValue($page_id, $pageproperty_id, $propertyValue);
		}
		
		$this->formForward();
		
	}
	
	public function addPageProperty($get){
	    
		$site_id = $get['site_id'];
		$propertyTypes = $this->manager->getPropertyTypes();
		
		$types = SmartestDataUtility::getDataTypes('field');
		$this->send($types, 'property_types');
		
		$acceptable_types = array_keys($types);
		
		if(isset($get['name'])){
		    $this->send(SmartestStringHelper::toVarName($get['name']), 'field_name');
	    }
	    
	    if(isset($get['type'])){
		    
		    if(in_array($get['type'], $acceptable_types)){
		        
		        $data_type_code = $get['type'];
		        $data_type = $types[$data_type_code];
		        $this->send($data_type_code, 'selected_type');
		        
		        if($data_type['valuetype'] == 'foreignkey' && isset($data_type['filter']['typesource'])){
	                
	                if(is_file($data_type['filter']['typesource']['template'])){
	                    $this->send(SmartestDataUtility::getForeignKeyFilterOptions($data_type_code), 'foreign_key_filter_options');
	                    $this->send(SM_ROOT_DIR.$data_type['filter']['typesource']['template'], 'filter_select_template');
	                }else{
	                    $this->send($data_type['filter']['typesource']['template'], 'intended_file');
	                    $this->send(SM_ROOT_DIR.'System/Applications/Items/Presentation/FKFilterSelectors/filtertype.unknown.tpl', 'filter_select_template');
	                }
	                
	                $this->send(true, 'foreign_key_filter_select');
	            }
		        
		        $this->send(true, 'show_full_form');
		        $this->send($this->getSite()->getId(), 'site_id');
		    }
		    
	    }
	    
		// return array("site_id"=>$site_id, "propertytypes"=>$propertyTypes, "name"=>$name);
		
	}
	
	public function insertPageProperty($get, $post){
		
		$property = new SmartestPageField;
		$property_name = $post['property_name'];
		$property->setLabel($property_name);
		$property->setSiteId($this->getSite()->getId());
		$property->setType($post['pageproperty_type']);
		$property->setName(SmartestStringHelper::toVarName($property_name));
		
		if(isset($post['foreign_key_filter'])){
		    $property->setForeignKeyFilter($post['foreign_key_filter']);
		}
		
		$property->save();
		
		$this->formForward();
	}
	
	public function addPagePropertyValue($get,$post){
		
		$page_webid = $post['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $post['pageproperty_id'];
		$propertyValue = $post['page_property'];
		
		$this->manager->insertPropertyValue($page_id, $pageproperty_id, $propertyValue);
		$this->formForward();
		
	}
	
	public function undefinePageProperty($get){
		
		$page_webid =$get['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
		$field = new SmartestPageField;
		
		if(!empty($get['assetclass_id'])){
		    $field_name = SmartestStringHelper::toVarName($get['assetclass_id']);
			// $pageproperty_name = $get['assetclass_id'];
			// $pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $get['assetclass_id'], "PageProperties");
			$field->hydrateBy('name', $field_name);
		}else{
		    $field_id = $get['pageproperty_id'];
		    $field->hydrate($field_id);
			// $pageproperty_id = $get['pageproperty_id'];
			// $pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
		}
		
		// $pageproperty_id = $get['pageproperty_id'];
		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
		
		$pagepropertyvalue_id = $this->manager->getPropertyValueId($page_id, $field->getId());
		
		// $pageproperty_id = $get['pageproperty_id'];
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
	        }
	    }
	    
	    if(is_numeric($this->getRequestParameter('page_id'))){
	        $page = new SmartestPage;
	        if($item->find($this->getRequestParameter('page_id'))){
	            $this->send($page, 'page');
	        }
	    }
	    
	}
	
	public function insertTag($get, $post){
	    
	    $proposed_tags = SmartestStringHelper::fromCommaSeparatedList($this->getRequestParameter('tag_label'));
	    
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
	    
	    foreach($proposed_tags as $tag_label){
	        
	        if(strlen($tag_label)){
	        
        	    $tag_name = SmartestStringHelper::toSlug($tag_label);
	    
        	    $tag = new SmartestTag;
        	    $existing_tags = array();
	    
        	    if($tag->hydrateBy('name', $tag_name)){
        	        // $this->addUserMessageToNextRequest("A tag with that name already exists.", SmartestUserMessage::WARNING);
        	        $existing_tags[] = "'".$tag_label."'";
        	    }else{
        	        $tag->setName($tag_name);
        	        $tag->setLabel($tag_label);
        	        $tag->save();
        	        if($tag_item){
        	            $item->tag($tag->getId());
        	        }
        	        if($tag_page){
        	            $page->tag($tag->getId());
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
        $this->formForward();
	    
	}
	
	public function getTaggedObjects($get){
	    
	    $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag'));
	    $tag = new SmartestTag;
	    
	    if($tag->findBy('name', $tag_identifier)){
	        $this->send($tag, 'tag');
	        $objects = $tag->getObjectsOnSite($this->getSite()->getId(), true);
	    }else{
	        $objects = array();
	        $this->addUserMessage("This tag does not exist.", SmartestUserMessage::WARNING);
	    }
	    
	    $this->send(new SmartestArray($objects), 'objects');
	    
	    
	}
	
	public function linkShorteningOptions(){
	    
	}
	
	public function updateLinkShorteningOptions(){
	    
	}

}