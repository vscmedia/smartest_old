<?php

class MetaData extends SmartestApplication{

	function __moduleConstruct(){
		
	}
	
	function startPage(){
	    $this->setFormReturnUri();
	    $du = new SmartestDataUtility;
	    $tags = $du->getTagsAsArrays();
	    // print_r($tags);
	    $this->send($tags, 'tags');
	    
	}
	
	function listFields(){
	    
		$this->setFormReturnUri();
		
		$site_id = $this->getSite()->getId();
		
		$sql = "SELECT * FROM PageProperties WHERE pageproperty_site_id ='$site_id' ";
		$fields = $this->database->queryToArray($sql);
		return array("fields"=>$fields, "site_id"=>$site_id);
		
	}
	
	function deletePageProperty($get,$post){
		
		$field_id = $get['field_id'];
		
		$property = new SmartestPageField;
		
		if($property->hydrate($field_id)){
		    $property->clearAllDefinitions();
		    $property->delete();
		    $this->addUserMessageToNextRequest('The property has been deleted.');
		}else{
		    $this->addUserMessageToNextRequest('The property ID was not recognised.');
		    
		}
		
		// print_r($property);
		
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
	        $this->addUserMessageToNextRequest("The field ID was not recognised.");
	        $this->formForward();
	    }
	    
	}
	
	public function clearFieldOnAllPages($get){
	    
	    $field_id = $get['field_id'];
		
		$property = new SmartestPageField;
		
		if($property->hydrate($field_id)){
		    $property->clearAllDefinitions();
		    $this->addUserMessageToNextRequest('All values of the property have been deleted.');
		}else{
		    $this->addUserMessageToNextRequest('The property ID was not recognised.');
		    
		}
		
		$this->formForward();
	}
	
	/*function defineFieldOnPage($get){
		$page_webid =$get['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $get['pageproperty_id'];
		$pageproperty_label = $this->manager->database->specificQuery("pageproperty_label", "pageproperty_id", $pageproperty_id, "PageProperties");
// 		$propertyTypes=$this->manager->getPropertyTypes();"Types"=>$propertyTypes
		return array("page_id"=>$page_webid, "pageproperty_id"=>$pageproperty_id ,"pageproperty_label"=>$pageproperty_label );
	}*/
	
	function defineFieldOnPage($get){
		
		$page_webid = $get['page_id'];
		
		// $page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
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
    		        $dropdown->hydrate($dropdown_id);
    		        $options = $dropdown->getOptionsAsArrays();
    		        $this->send($options, 'options');
    		    }
    		    
    		    $this->send($def->getDraftValue(), 'value');
    		    $this->send($field->getName(), 'field_name');
    		    $this->send($field->getType(), 'field_type');
    		    $this->send($field->getId(), 'field_id');
    		    $this->send($page->getId(), 'page_id');
		
    	    }else{
    	        
    	        $this->addUserMessageToNextRequest('The specified field doesn\'t exist');
    	        $this->formForward();
    	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The page ID wasn\'t recognized.');
	        $this->formForward();
            
        }
		
	}
	
	function setLiveProperty($get){
		
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
	
	function updatePagePropertyValue($get, $post){
		
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
		
    		if($field->hydrateBy($lookup_field, $value)){
		
    		    print_r($field);
		
        		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
        		// $sql = "SELECT pagepropertyvalue_draft_value FROM PagePropertyValues WHERE pagepropertyvalue_page_id ='$page_id' AND pagepropertyvalue_pageproperty_id='$pageproperty_id'";
		
        		// $result = $this->manager->database->queryToArray($sql);
        		// $pageproperty_value = $result[0];
    		
        		$def = new SmartestPageFieldDefinition;
        		$def->loadForUpdate($field, $page);
        		$def->setDraftValue(addslashes($post['field_content']));
        		$def->setPageId($page->getId());
        		$def->setPagepropertyId($field->getId());
        		$def->save();
        		
        		$this->addUserMessageToNextRequest('The page field has been updated');
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
    	        
    	        
    	        $this->addUserMessageToNextRequest('The specified field doesn\'t exist');
    	        // $this->formForward();
    	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The page ID wasn\'t recognized.');
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
	
	function savePagePropertyValue($get, $post){
		
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
		
		// print_r();
		// print_r($this->manager->database->getDebugInfo());
		
		$this->formForward();
		
	}
	
	function addPageProperty($get){
		$site_id = $get['site_id'];
		$propertyTypes = $this->manager->getPropertyTypes();
		$name = $get['name'];
		return array("site_id"=>$site_id, "propertytypes"=>$propertyTypes, "name"=>$name);
	}
	
	function insertPageProperty($get, $post){
		
		// $site_id = $post['site_id'];
		
		$property = new SmartestPageField;
		$property_name = $post['property_name'];
		$property->setLabel($property_name);
		$property->setSiteId($this->getSite()->getId());
		$property->setType('SM_DATATYPE_SL_TEXT');
		$property->setName(SmartestStringHelper::toVarName($property_name));
		$property->save();
        
        /*
		// $propertyType = "single_line_text_field";
		$property_label = $post['property_name'];
		// $property_name = $post['property_name'];
		$property_name = SmartestStringHelper::toVarName($property_label);
		
		$field = new SmartestPageField;
		$field->setName($property_name);
		$field->setLabel($property_label);
		$field->save();
		
		// $this->manager->insertPageProperty($site_id, $propertyName, $propertyLabel, $propertyType);
		
		// print_r($this->manager->database->getDebugInfo()); */
		
		$this->formForward();
	}
	
	function addPagePropertyValue($get,$post){
		
		$page_webid = $post['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		$pageproperty_id = $post['pageproperty_id'];
		$propertyValue = $post['page_property'];
		
		$this->manager->insertPropertyValue($page_id, $pageproperty_id, $propertyValue);
		$this->formForward();
		
	}
	
	function undefinePageProperty($get){
		
		$page_webid =$get['page_id'];
		$page_id = $this->manager->getPageIdFromPageWebId($page_webid);
		
		if(!empty($get['assetclass_id'])){
			$pageproperty_name = $get['assetclass_id'];
			$pageproperty_id = $this->manager->database->specificQuery("pageproperty_id", "pageproperty_name", $get['assetclass_id'], "PageProperties");
		}else{
			$pageproperty_id = $get['pageproperty_id'];
			$pageproperty_name = $this->manager->database->specificQuery("pageproperty_name", "pageproperty_id", $pageproperty_id, "PageProperties");
		}
		
		// $pageproperty_id = $get['pageproperty_id'];
		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
		
		$pagepropertyvalue_id = $this->manager->getPropertyValueId($page_id, $pageproperty_id);
		
		// $pageproperty_id = $get['pageproperty_id'];
		// $pagepropertyvalue_id = $this->manager->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $pageproperty_id, "PagePropertyValues");
		
		$this->manager->undefinePageProperty($pagepropertyvalue_id);
		$this->formForward();
		
	}
	
	public function tagsFront(){
	    
	}
	
	public function addTag(){
	    
	}
	
	public function insertTag($get, $post){
	    
	    $tag_label = $post['tag_label'];
	    $tag_name = SmartestStringHelper::toSlug($tag_label);
	    
	    $tag = new SmartestTag;
	    
	    if($tag->hydrateBy('name', $tag_name)){
	        $this->addUserMessageToNextRequest("A tag with that name already exists.");
	        $this->formForward();
	    }else{
	        $tag->setName($tag_name);
	        $tag->setLabel(addslashes($tag_label));
	        $tag->save();
	        // print_r($tag);
	        $this->addUserMessageToNextRequest("Tag was successfully added.");
	        $this->formForward();
	    }
	    
	}
	
	public function getTaggedObjects($get){
	    
	    $tag_identifier = SmartestStringHelper::toSlug($get['tag']);
	    $tag = new SmartestTag;
	    
	    if($tag->hydrateBy('name', $tag_identifier)){
	        $objects = $tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), true);
	        // print_r($objects);
	    }else{
	        $objects = array();
	        $this->addUserMessage("This tag does not exist.");
	    }
	    
	    $this->send($objects, 'objects');
	    $this->send($tag_identifier, 'tag_name');
	    
	}

}