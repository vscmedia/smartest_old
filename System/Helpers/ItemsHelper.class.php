<?php
/**
 * Implements the Items manager
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    Visudo License
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

/**
  * @access 
  * @param 
  */
class ItemsHelper{

	private $database;

 /**
  * description
  * @access public
  */
	function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
				
	}
  	
 /**
  * description
  * @access public
  */
	function getItemClass($class_id){
		$result = $this->database->queryToArray("SELECT * FROM ItemClasses WHERE itemclass_id='$class_id'");
		return $result[0];
	}
 /**
  * description
  * @access public
  */
	function getItemClasses($exclude=""){
		$query = "SELECT * FROM ItemClasses ORDER BY itemclass_id ASC";		
		if(is_numeric($exclude)){
			$query .= " WHERE itemclass_id !='$exclude'";
		}
		
		$itemClasses = $this->database->queryToArray($query);
		
		for($i=0;$i<count($itemClasses);$i++){
			$itemClasses[$i]["number_in_class"] = $this->countItemClassMembers($itemClasses[$i]["itemclass_id"]);
			$itemClasses[$i]["number_properties"] = $this->countItemClassProperties($itemClasses[$i]["itemclass_id"]);
		}
		
		return $itemClasses;
	}
  	
 /**
  * description count how
  * @access public
  */
	function countItemClassMembers($class_id){
		if(strlen($class_id) < 16 && is_numeric($class_id)){
			$id = $class_id;
		}
		else{
			$id = $this->getClassidFromWebId($class_id);
		}
		$query = "SELECT item_id FROM Items WHERE item_itemclass_id='$id'";
		$count = $this->database->howMany($query);
		return $count;
	}
	
 /**
  * description
  * @access public
  */
	function getClassidFromWebId($class_webid){
		$query = "SELECT itemclass_id FROM ItemClasses WHERE itemclass_webid='$class_webid'";
		$result = $this->database->queryToArray($query);
		return $result[0]["itemclass_id"];
	}
  
 /**
  * description
  * @access public
  */
	function countItemClassProperties($class_id){
		if(strlen($class_id) < 16 && is_numeric($class_id)){
			$id = $class_id;
		}else{
			$id = $this->getClassidFromWebId($class_id);
		}
		$query = "SELECT itemproperty_id FROM ItemProperties WHERE itemproperty_itemclass_id='$id'";
		$count = $this->database->howMany($query);
		return $count;
	}
  
 /**
  * description
  * @access public
  */
	function getItemClassProperties($class_id){
		
		$id = $class_id;
		$sql = "SELECT ItemProperties.*, PropertyTypes.propertytype_name FROM ItemProperties, PropertyTypes WHERE ItemProperties.itemproperty_datatype=PropertyTypes.propertytype_id AND itemproperty_itemclass_id='$id' ORDER BY itemproperty_varname";		
		$properties = $this->database->queryToArray($sql);

		return $properties;
	}
	
 /**
  * description
  * @access public
  */
	function getItemsInClass($class_id, $limit=0, $order='ASC'){
				
		$id = $class_id;
		
		$sql = "SELECT Items.*, ItemClasses.itemclass_plural_name, ItemClasses.itemclass_name FROM Items, ItemClasses WHERE ItemClasses.itemclass_id='$id' AND ItemClasses.itemclass_id=Items.item_itemclass_id ORDER BY Items.item_id";
		$sql .= " ".$order;
		//echo $sql;
		if(is_numeric($limit) && $limit > 0){$sql .= " LIMIT ".$limit;}
		
		$getItems = $this->database->queryToArray($sql);
		$items = null;
  
		for($i=0; $i<count($getItems); $i++){
			
			$item_id_val= mysql_real_escape_string($getItems[$i]["item_id"]);
			$class_id_val=mysql_real_escape_string($class_id);
			$info = null;
			
			// $propertyValues = $this->getItemPropertyValues($item_id_val,$class_id_val);
			/* $content = null;        
     		
     		if (is_array($propertyValues)){  
			    foreach($propertyValues as $key=>$value){
				    $info[$key] = $value;
			    }
      		} */
      		
			$items[$i]['item'] = $getItems[$i];
			$items[$i]['item']['itemclass_id'] = $class_id;
			// $items[$i]['properties'] = $info;
		}
		
		return $items;
	}


	function getAvailableItemsInClass($class_id,$set_id){
	$id = $class_id;		
	$sql = "SELECT Items.item_id,Items.item_name FROM Items, ItemClasses WHERE ItemClasses.itemclass_id='$id' AND ItemClasses.itemclass_id=Items.item_itemclass_id  ORDER BY Items.item_id";
	$getItems = $this->database->queryToArray($sql);
	foreach($getItems as $key=>$value){
		$id=$value['item_id'];
		$count_query="SELECT * FROM SetsItemsLookup WHERE setlookup_set_id=$set_id AND setlookup_item_id=$id";
		$count=$this->database->howMany($count_query);
		if($count<= 0){$items[]=$value;}
	}
	return $items;
	}
 /**
  * description
  * @access public
  */
	function getSettingItemsInClass($class_id, $limit=0, $order='ASC'){
		
		$id = $class_id;
		$sql = "SELECT * FROM ItemProperties WHERE ItemProperties.itemproperty_setting = 1 AND ItemProperties.itemproperty_itemclass_id='$id'";		
		$properties = $this->database->queryToArray($sql);
		//echo $sql;

		for($i=0; $i<count($properties); $i++){        	    
			$item = $properties[$i];
			$item['itemclass_id'] = $itemclassid;
			
			$items[$i] = $item;
		}
		return $items;
	}

	function getItemDetails($id){	
		$sql = "SELECT * FROM ItemPropertyValues, ItemProperties WHERE ItemPropertyValues.itempropertyvalue_property_id = ItemProperties.itemproperty_id AND ItemPropertyValues.itempropertyvalue_item_id ='$id' ";
		$itemproperties = $this->database->queryToArray($sql);
		return $itemproperties;
	}
	
 /**
  * takes array $options as its only argument
  * $options *must* have: name, public_name, datatype, 
  * $options can also contain: required, default_value
  * @access public
  */


	function insertNewItemClassProperty($options){		
// 		$sql = "INSERT INTO `ItemProperties` (`itemproperty_webid` ,`itemproperty_name`, `itemproperty_varname`,  `itemproperty_required`,`itemproperty_setting`, `itemproperty_datatype`,  `itemproperty_itemclass_id`, `itemproperty_defaultvalue`,`itemproperty_setting_value`,`itemproperty_dropdown_id`,`itemproperty_model_id`) 
// 		VALUES ('".$options["web_id"]."', '".$options["name"]."', '".$options["var_name"]."', '".$options["is_required"]."', '".$options["setting"]."','".$options["datatype"]."','".$options["class_id"]."','".$options["default_val"]."','".$options["setting"]."','".$options["dropdown"]."','".$options["model"]."')";      
		$sql = "INSERT INTO `ItemProperties` (`itemproperty_webid` ,`itemproperty_name`, `itemproperty_varname`,  `itemproperty_required`, `itemproperty_datatype`,  `itemproperty_itemclass_id`, `itemproperty_defaultvalue`,`itemproperty_dropdown_id`,`itemproperty_model_id`) 
		VALUES ('".$options["web_id"]."', '".$options["name"]."', '".$options["var_name"]."', '".$options["is_required"]."','".$options["datatype"]."','".$options["class_id"]."','".$options["default_val"]."','".$options["dropdown"]."','".$options["model"]."')";      

		$result = $this->database->rawQuery($sql);
		$property_id = $this->database->getInsertId();
		return $property_id;
	}
	
	
 /**
  * description
  * @access public
  */
	function addNewItemClass($name, $plural_name){
		$query = "INSERT INTO ItemClasses (itemclass_name, itemclass_plural_name) VALUES ('$name', '$plural_name')";
		$result = $this->database->query($query);
		return $result;
	}



	
 /**
  * description
  * @access public
  */
	function getItemClassBaseValues($class_id){
	
		if(strlen($class_id) < 16 && is_numeric($class_id)){
			$id = $class_id;
		}else{
			$id = $this->getClassidFromWebId($class_id);
		}
		
		$itemclass_parent_id = $this->database->specificQuery("itemclass_parent_id", "itemclass_id", $id, "ItemClasses");
		
		$result = $this->database->queryToArray("SELECT * FROM ItemClasses WHERE itemclass_id='$id'");
		$result = $result[0];
		return $result;
	}
		
 /**
  * description
  * @access public
  */
	function updateItemClassProperty($property_id, $options){
		$query = "UPDATE ItemProperties SET" ;
		$query .= "WHERE itemproperty_id='$property_id' LIMIT 1";
		if($this->database->query($query)){
			return true;
		}else{
			return false;
		}
	}
	
 /**
  * description
  * @access public
  */
	function getItemBaseValues($item_id){
		$item_name = $this->database->specificQuery("item_name", "item_id", $item_id, "Items");
		$item_class_id = $this->database->specificQuery("item_itemclass_id", "item_id", $item_id, "Items");
		$item_class_name = $this->database->specificQuery("itemclass_name", "itemclass_id", $item_class_id, "ItemClasses");
		return array("item_id"=>$item_id, "item_name"=>$item_name, "item_class_name"=>$item_class_name, "item_class_id"=>$item_class_id);
	}
	function getItemValues($item_id){
		$sql = "SELECT * FROM Items WHERE item_id='$item_id'";
		$itemvalues = $this->database->queryToArray($sql);
		return $itemvalues;
	}
	
 /**
  * description
  * @access public
  */
// 	function getItemPropertyValues($item_id){
// 		$item_class_id = $this->database->specificQuery("item_itemclass_id", "item_id", $item_id, "Items");
// 
// 		$getProperties = $this->getItemClassProperties($item_class_id);
// 
// 		for ($i=0; $i < count($getProperties); $i++){
// 
// 			$getValue = $this->getSingleItemPropertyValue($item_id, $getProperties[$i]["itemproperty_id"]);
// 			$getProperties[$i]  = @array_merge($getProperties[$i], $getValue);
// 		}
// 
// 		return $getProperties;
// 	}
	function getItemPropertyValues($item_id, $class_id){
	
		$getProperties = $this->getItemClassProperties($class_id);
		foreach($getProperties as $p){

			if($p['itemproperty_setting'] == 1 || $p['itemproperty_datatype'] == 'NODE' ){
			
			}else{
				$getValue = $this->getSingleItemPropertyValue($item_id, $p["itemproperty_id"]);
				$p['itempropertyvalue_content']=$getValue;
				$getPropertyvalues[]  = $p;		
			}
		}

		return $getPropertyvalues;
	}
	
 /**
  * description
  * @access public
  */
	function getSingleItemPropertyValue($item_id, $property_id){
		$sql = "SELECT itempropertyvalue_content FROM ItemPropertyValues WHERE itempropertyvalue_item_id='$item_id' AND itempropertyvalue_property_id='$property_id'";
		//echo $sql."<br>";
		$values = $this->database->queryToArray($sql);
		
		if(!empty($values[0]['itempropertyvalue_content'])){
			return $values[0]['itempropertyvalue_content'];
		}else{
			return null;
		}
	}

	function getSingleItemProperty( $property_id){
		$sql = "SELECT * FROM ItemProperties WHERE itemproperty_id=$property_id";
		$properties = $this->database->queryToArray($sql);
		return ( $properties ); 
	}
	
 /**
  * description
  * @access public
  */
	function getItemPropertyValuesLabeled($item_id){
		//$sql = "SELECT * FROM ItemProperties, ItemPropertyValues, Vocabulary WHERE Vocabulary.vocabulary_id=ItemProperties.itemproperty_vocabulary_id AND ItemPropertyValues.itempropertyvalue_item_id='$item_id' AND ItemPropertyValues.itempropertyvalue_property_id=ItemProperties.itemproperty_id";
		/*$sql = "
		SELECT * FROM ItemPropertyValues, ItemProperties,
		WHERE ItemPropertyValues.itempropertyvalue_item_id='$item_id'
		AND ItemPropertyValues.itempropertyvalue_property_id = ItemProperties.itemproperty_id";*/
		//echo $sql;

		$getPropertyValues = $this->database->queryToArray($sql);
		
		$labledPropertyValues = array();
		
		foreach($getPropertyValues as $property){
			
			if(strlen($property["itemproperty_varname"]) > 0){
				$labledPropertyValues[$property["itemproperty_varname"]] = $property;
				$labledPropertyValues[$property["itemproperty_varname"]]["value"] = $property["itempropertyvalue_content"];
			}else{
				$labledPropertyValues[$property["vocabulary_name"]] = $property;
				$labledPropertyValues[$property["vocabulary_name"]]["value"] = $property["itempropertyvalue_content"];
			}
		}
		
    	return $labledPropertyValues;
	}
	
  
 /**
  * description
  * @access public
  */
  function getSchemas(){
    $sql = "SELECT * FROM Schemas";
    $schemas = $this->database->queryToArray($sql); 
    return $schemas;
  }
	
 /**
  * description
  * @access public
  */
  function getSchemaName($id){
    $sql = "SELECT schema_name FROM Schemas WHERE schema_id='$id' LIMIT 1";
    $schema = $this->database->queryToArray($sql); 
    return $schema[0];
  }

 /**
  * description
  * @access public
  */
  function createSchemaAction($schema){
    $sql = "INSERT INTO `Schemas` (`schema_name`, `schema_namespace`, `schema_description`, `schema_parent_id`, `schema_locked`) 
						VALUES ('".$schema['schema_name']."', 'NAMESPACE', 'DESCRIPTION', '".$schema['schema_parent_id']."', '0')";
    $schemas = $this->database->rawQuery($sql);
		//echo $sql;
    $id = $this->database->getInsertId();

		
		
		$vocabulary = null;
		if((int)$schema['schema_parent_id'] > 0){
			$vocabulary = $this->getSchemaVocabulary($schema['schema_parent_id']);			
		}
		//should this be an extended insert?
		if(count($vocabulary)){
			foreach($vocabulary as $v){
				$schemadefinition_schema_id=$v['schemadefinition_schema_id'];
				$schemadefinition_vocabulary_id=$v['schemadefinition_vocabulary_id'];
				$schemadefinition_parent_id=$v['schemadefinition_parent_id'];
				$schemadefinition_level=$v['schemadefinition_level'];
								  
				$sql = "INSERT INTO `SchemaDefinitions` (`schemadefinition_schema_id`, `schemadefinition_vocabulary_id`, `schemadefinition_parent_id`, `schemadefinition_level`) VALUES ('".$id."', '".$schemadefinition_vocabulary_id."', '".$schemadefinition_parent_id."', '".$schemadefinition_level."')";
				$this->database->rawQuery($sql);
			}
		}
    return true;
  }
	
	function getSchemaVocabulary($schema_id){
		$sql = "SELECT * FROM SchemaDefinitions WHERE schemadefinition_schema_id='$schema_id' ORDER BY schemadefinition_id DESC";
    $schemas = $this->database->queryToArray($sql); 
		return $schemas;
	}
	function getItemProperties($itemclass_id,$itemproperty_varname){
		$sql = "SELECT itemproperty_id,itemproperty_datatype FROM ItemProperties WHERE  itemproperty_itemclass_id='$itemclass_id' AND itemproperty_id=$itemproperty_varname";
			//echo $sql."<br>";
			$itemproperty = $this->database->queryToArray($sql);
			return $itemproperty;
	}
	function getItemproperty_datatype($itemclass_id,$itemproperty_varname){
		return $this->database->queryToArray("SELECT itemproperty_id, itemproperty_datatype FROM ItemProperties WHERE itemproperty_varname='$itemproperty_varname' AND itemproperty_itemclass_id='$itemclass_id'");
	}
	function getSchemaDefinitions($itemclass_schema_id){
		$sql = "SELECT * FROM SchemaDefinitions, Vocabulary WHERE SchemaDefinitions.schemadefinition_schema_id='".$itemclass_schema_id."' AND SchemaDefinitions.schemadefinition_vocabulary_id=Vocabulary.vocabulary_id ORDER BY SchemaDefinitions.schemadefinition_vocabulary_id";
			return $this->database->queryToArray($sql);
	}
	function getItemid($itemproperty_itemclass_id){
	$sql = "SELECT item_id FROM Items WHERE item_itemclass_id='$itemproperty_itemclass_id'";
		return $this->database->queryToArray($sql);
	}
	function getVocabulary_prefix(){
	$sql = "SELECT DISTINCT(vocabulary_prefix) FROM Vocabulary";
		return $this->database->queryToArray($sql);
	}
	function getVocabularydetails($prefix){
	$sql = "SELECT * FROM Vocabulary WHERE vocabulary_prefix='$prefix'";
		return $this->database->queryToArray($sql);
	}
	function getVocabulary_id($id){
	$sql = "SELECT * FROM Vocabulary WHERE vocabulary_id='$id'";
		return $this->database->queryToArray($sql);
	}
	//////// DELETE ///////////

	function deleteItem($item_id){
		$query = "DELETE FROM Items WHERE item_id='$item_id' LIMIT 1";
		$this->database->query($query);
		$query = "DELETE FROM ItemPropertyValues WHERE itempropertyvalue_item_id='$item_id'";
		$this->database->query($query);
	}

	function deleteSchema($schema_id){
		$query = "DELETE FROM Schemas WHERE schema_id='$schema_id' LIMIT 1";
		$nameSuccess = $this->database->query($query);
		$query = "DELETE FROM SchemaDefinitions WHERE schemadefinition_schema_id='$schema_id'";
		$definitionSuccess = $this->database->query($query);
		if($nameSuccess && $definitionSuccess){
			return true;
		}
	}

	function deleteItemClass($item_class_id){
		$sql = "DELETE FROM ItemClasses WHERE itemclass_id='$item_class_id'";
		$this->database->query($sql);
		$sql = "DELETE FROM ItemProperties WHERE itemproperty_itemclass_id='$item_class_id'";
		$this->database->query($sql);
		$sql = "SELECT * FROM Items WHERE item_itemclass_id='$item_class_id'";
		$items=$this->database->query($sql);
		if(is_array($items)){	
		foreach($items as $v){
		$id=$v['item_id'];
		$sql = "DELETE FROM Items WHERE item_id='$id'";
		$this->database->query($sql);	
		$sql = "DELETE FROM ItemPropertyValues WHERE   	itempropertyvalue_item_id='$id'";
		$this->database->query($sql);
		}
		}
	}

	function deleteItemClassProperty($property_id){
		$sql = "DELETE FROM ItemProperties WHERE itemproperty_id='$property_id'";
		$this->database->query($sql);
		$sql = "DELETE FROM ItemPropertyValues WHERE itempropertyvalue_property_id='$property_id'";
		$this->database->query($sql);
	}
	
	function updateItemBasic($slung,$name,$id,$itemIsPublic){
		$sql ="UPDATE Items SET item_slug='".$slung."' ,item_name='".$name."' , item_public='".$itemIsPublic."' WHERE item_id=".$id."";
		$itemproperties = $this->database->rawQuery($sql);
	}
	
	function updateItemcontent($content, $id){
		$sql ="UPDATE ItemPropertyValues SET itempropertyvalue_content='".$content."' WHERE itempropertyvalue_id='".$id."' LIMIT 1";
		return $this->database->rawQuery($sql);
	}
	
	function updateItemall($itemproperty_setting,$itemproperty_setting_value,$itemproperty_varname,$itemproperty_name,$itemproperty_required,$itemproperty_datatype,$itemproperty_id,$itemproperty_dropdown,$itemproperty_modelid){
		$sql = "UPDATE `ItemProperties` SET `itemproperty_setting`='".$itemproperty_setting."',`itemproperty_setting_value`='".$itemproperty_setting_value."', `itemproperty_varname`='".$itemproperty_varname."', `itemproperty_name` = '".$itemproperty_name."', `itemproperty_required` = '".$itemproperty_required."', itemproperty_datatype='".$itemproperty_datatype."',itemproperty_dropdown_id='".$itemproperty_dropdown."',itemproperty_model_id='".$itemproperty_modelid."' WHERE itemproperty_id=$itemproperty_id LIMIT 1";
		$this->database->rawQuery($sql);
	}
	
	function setItemname($webid,$slung,$itemclass_id,$item_name,$item_public,$item_userid){
		$sql = "INSERT INTO `Items` (`item_webid`,`item_slug`,`item_itemclass_id`, `item_name`, `item_public`, `item_userid`) VALUES ('".$webid."','".$slung."','".$itemclass_id."', '".$item_name."', '".$item_public."', '".$item_userid."')";
		$this->database->rawQuery($sql);
		return $this->database->getInsertId();
	}
	
	function setItemPropertyValues($item_id,$itemproperty_id,$itempropertyvalue_content){
			$sql = "INSERT INTO ItemPropertyValues (itempropertyvalue_item_id, itempropertyvalue_property_id, itempropertyvalue_content) VALUES ('".$item_id."', '".$itemproperty_id."', '".$itempropertyvalue_content."')";
			//echo $sql;
			return $this->database->rawQuery($sql);
	}
	
	function setItemClasses($itemclass_name,$itemclass_plural_name,$itemclass_webid,$itemclass_schema_id,$itemclass_varname,$itemclass_userid){
			$sql = "INSERT INTO `ItemClasses` ( `itemclass_name`, `itemclass_plural_name`, `itemclass_webid`, `itemclass_schema_id`,`itemclass_varname`,`itemclass_userid`) VALUES ( '".$itemclass_name."', '".$itemclass_plural_name."', '".$itemclass_webid."', '".$itemclass_schema_id."', '".$itemclass_varname."', '".$itemclass_userid."')";
			$this->database->query($sql); 
			return $this->database->getInsertId();   
	}
	
	function setItemProperties($webid,$itemproperty_name,$itemproperty_vocabulary_id,$itemproperty_varname,$itemproperty_required,$setting,$itemproperty_datatype,$itemproperty_itemclass_id,$parent,$level){
			$sql = "INSERT INTO `ItemProperties` ( `itemproperty_webid` , `itemproperty_name` , 
                               `itemproperty_varname` , `itemproperty_required` , `itemproperty_setting`,         `itemproperty_datatype` ,`itemproperty_itemclass_id`,) 
				VALUES ( '".$webid."', '".$itemproperty_name."', 
                                '".$itemproperty_varname."',  '".$itemproperty_required."',   '".$setting."', 
                               '".$itemproperty_datatype."',  '".$itemproperty_itemclass_id."')";
			return $this->database->query($sql);  
	}
	
	function updateItemSettings($id,$value){
		$sql = "UPDATE ItemProperties SET itemproperty_setting_value='$value' WHERE itemproperty_id='$id' LIMIT 1";
		$this->database->rawQuery($sql);
	}
	
	function insertVocabulary($vocabulary_name,$vocabulary_datatype,$vocabulary_htmlform,$default_value,$setting){
		$sql="INSERT INTO Vocabulary (vocabulary_name,vocabulary_type,vocabulary_htmlform,vocabulary_default_content,vocabulary_setting) VALUES ('$vocabulary_name', '$vocabulary_datatype', '$vocabulary_htmlform', '$default_value','$setting')";/*echo $sql;*/
		$this->database->rawQuery($sql);
		return $this->database->getInsertId();
	}
	
	function getItemClasslevel($id){
		$itemclass_parent_id = $this->database->specificQuery("itemproperty_level", "itemproperty_id", $id, "ItemProperties");
		return $itemclass_parent_id;
	
	}
	
// 	function getparent($id){
// 		$itemclass_parent_id = $this->database->specificQuery("itemproperty_vocabulary_id", "itemproperty_id", $id, "ItemProperties");
// 		return $itemclass_parent_id;
// 	
// 	}
	
	function getdefault($id){
		$vocabulary_default_content = $this->database->specificQuery("vocabulary_default_content", "vocabulary_id", $id, "Vocabulary");
		return $vocabulary_default_content;
	
	}
	
	function updatedefault($default_value,$id){		
		$sql = "UPDATE ItemProperties SET itemproperty_defaultvalue='$default_value' WHERE itemproperty_id='$id' LIMIT 1";
		$this->database->rawQuery($sql);
	}
	
	function updateItemPropertyValues($id,$property,$content){
		$sql ="UPDATE ItemPropertyValues SET itempropertyvalue_content='$content' WHERE itempropertyvalue_item_id='$id' AND itempropertyvalue_property_id='$property' LIMIT 1";
		$this->database->rawQuery($sql);
	}
	
	function getUniqueItemName($name){	    		
		$name_temp=$name;
		$sql="SELECT * from Items where item_name = '$name'";
		$count=$this->database->howMany($sql);
		$i=1;
		
		while($count>0){
		
			$i=$i+1;
			$name=$name_temp.' '.$i;
			$sql="SELECT * from Items where item_name = '$name'";
			$v1=$this->database->howMany($sql);
			
			if($v1==0){
				break;
			}else{
				$count=$v1;
			}
		}
		return $name;
	}
	
	function setDataExport($schema_id,$class_id,$property_id,$vocabulary_id){
		$sql = "INSERT INTO DataExports(dataexport_schema_id,dataexport_itemclass_id, dataexport_property_id,dataexport_vocabulary_id) VALUES ('".$schema_id."', '".$class_id."', '".$property_id."','".$vocabulary_id."')";//echo $sql;
		return $this->database->rawQuery($sql);
	}
	
	function getItemSchemaVocabularyName($itemproperty_id,$class_id,$schema_id){
		$sql = "SELECT dataexport_vocabulary_id FROM DataExports WHERE dataexport_schema_id=$schema_id AND dataexport_itemclass_id=$class_id  AND  dataexport_property_id=$itemproperty_id ";
    		$vocabulary_details = $this->database->queryToArray($sql); 		
		$vocabulary_id=$vocabulary_details[0]['dataexport_vocabulary_id'];
		$schemavocabulary_name=$this->database->specificQuery("vocabulary_name", "vocabulary_id", $vocabulary_id, "Vocabulary");
		return $schemavocabulary_name;
	}
	
	function getItemPropertyTypes(){
		// $sql = "SELECT * FROM PropertyTypes ";
    	// $propertyTypes = $this->database->queryToArray($sql);
    	$propertyTypes = SmartestCache::load('datatypes_xml_file_data', true);
		return $propertyTypes;
	}
	
	function getDropdownMenu(){
		$sql = "SELECT * FROM DropDowns ";
    		$dropdowns = $this->database->queryToArray($sql); 
		return $dropdowns;
	}
	
	function getDropdownMenuValues($sel_id){
		$sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='$sel_id' ORDER BY dropdownvalue_order";
    		$dropdowns = $this->database->queryToArray($sql); 
		return $dropdowns;
	}
	
	function getItemClassPropertyTypeName($type_id){
		$sql = "SELECT propertytype_name FROM PropertyTypes WHERE propertytype_id='$type_id' ";
    		$propertytype_name = $this->database->queryToArray($sql); 
		$name=$propertytype_name[0]['propertytype_name'];
		return $name;
	}
	
	function getItemClassName($id){
		$itemclass = $this->database->specificQuery("itemclass_name", "itemclass_id", $id, "ItemClasses");
		return $itemclass;
	
	}

	function getItemClassCount($name){
		$sql="SELECT * from ItemClasses where itemclass_name = '$name'";
		$count=$this->database->howMany($sql);		
		return $count;
	}

}


?>
