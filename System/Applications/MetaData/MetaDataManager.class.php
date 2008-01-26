<?php

class MetaDataManager{

	protected $database;
	
	function __construct(){
		$this->database =& SmartestPersistentObject::get('db:main');
	}
	
	function getPageIdFromPageWebId($page_webid){
		if(!is_numeric($page_webid)){
			return $this->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
		}else{
			return $page_webid;
		}
	}
	
	function getPropertyDefinitionExists($property_id, $page_id){
		$sql = "SELECT * FROM PagePropertyValues WHERE pagepropertyvalue_page_id='$page_id' AND pagepropertyvalue_pageproperty_id='$property_id'";
		return $this->database->howMany($sql) ? true : false;
	}
	
	function setLiveProperty($pagepropertyvalue_id){
		$sql = "UPDATE PagePropertyValues SET pagepropertyvalue_live_value = pagepropertyvalue_draft_value WHERE pagepropertyvalue_id = '$pagepropertyvalue_id'";
    	$this->database->rawQuery($sql);
		$this->touchPage($page_id);
	}
	
	function updatePropertyValue($page_id, $pageproperty_id, $propertyValue){
		$sql = "UPDATE PagePropertyValues SET pagepropertyvalue_draft_value='$propertyValue' WHERE pagepropertyvalue_page_id='$page_id' AND pagepropertyvalue_pageproperty_id='$pageproperty_id'";
    	$this->database->query($sql);
    	$this->touchPage($page_id);
	}
	
	function insertPropertyValue($page_id, $pageproperty_id, $propertyValue){
		$sql = "INSERT INTO PagePropertyValues ( pagepropertyvalue_page_id, pagepropertyvalue_pageproperty_id, pagepropertyvalue_draft_value ) VALUES ( '$page_id', '$pageproperty_id', '$propertyValue');";
    	$this->database->rawQuery($sql);
    	$this->touchPage($page_id);
	}
	
	function undefinePageProperty($pagepropertyvalue_id){
		$query = "DELETE FROM PagePropertyValues WHERE pagepropertyvalue_id='$pagepropertyvalue_id' LIMIT 1";
		$this->database->rawQuery($query);
		
	}
	
	function getAllFieldDefinitions($field_id){
	    $sql = "SELECT * FROM Pages, PageProperties, PagePropertyValues WHERE PageProperties.pageproperty_id='".$field_id."' AND PageProperties.pageproperty_id=PagePropertyValues.pagepropertyvalue_pageproperty_id AND PagePropertyValues.pagepropertyvalue_page_id=Pages.page_id";
	    $result = $this->database->queryToArray($sql);
	    // print_r($result);
	    return $result;
	}
	
	function insertPageProperty($site_id, $property_name, $property_label, $property_type){
		if(!$this->getPropertyExistsByName($property_name)){
			$query = "INSERT INTO PageProperties (pageproperty_site_id, pageproperty_name, pageproperty_label, pageproperty_type) VALUES ('$site_id', '$property_name', '$property_label', '$property_type')";
			$this->database->rawQuery($query);
		}
	}
	
	function getPropertyExistsByName($property_name){
		$sql = 'SELECT * FROM PageProperties WHERE pageproperty_name=\''.$property_name.'\'';
		return $this->database->howMany($sql) ? true : false;
	}
	
	function getPropertyTypes(){
		$sql = "SELECT * FROM PropertyTypes ";
    	$propertyTypes = $this->database->queryToArray($sql); 
		return $propertyTypes;
	}
	
	function getPropertyValueId($page_id, $property_id){
		$sql = "SELECT pagepropertyvalue_id FROM PagePropertyValues WHERE pagepropertyvalue_page_id='$page_id' AND pagepropertyvalue_pageproperty_id='$property_id'";
		$result = $this->database->queryToArray($sql);
		return $result[0]['pagepropertyvalue_id'];
	}
	
	function touchPage($page_id){
		
		if(is_numeric($page_id)){
			$idField = "page_id";
		}else{
			$idField = "page_webid";
		}
		
		$sql = "UPDATE Pages SET page_modified = '".time()."' WHERE $idField = '$page_id'";
    	$this->database->rawQuery($sql);
	}
	
}