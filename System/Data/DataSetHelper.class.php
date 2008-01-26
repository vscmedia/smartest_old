<?php

class DataSetHelper{
	var $database;	
	
	function dataSetHelper(){
		$this->database =& $_SESSION["database"];
	}
	
	//function for updating
	function modifyData($datas){
		
		for($i=0;$i<count($datas);$i++){
			$p = $datas[$i]['itempropertyvalue_property_id']; 
			$item = $datas[$i]['itempropertyvalue_item_id'];
			$val = ($datas[$i]['itempropertyvalue_content']);
			$sql = "UPDATE ItemPropertyValues SET  itempropertyvalue_content='$val' WHERE itempropertyvalue_item_id='$item' AND itempropertyvalue_property_id='$p'";
			$update = $this->database->rawQuery($sql);
		}		
	}
	
	//function for deletion
	function deleteData($datas){
		
		for($i=0;$i<count($datas);$i++){		
			$item=$datas[$i]['itempropertyvalue_id'];
			$sql = "DELETE FROM ItemPropertyValues WHERE itempropertyvalue_id='$item'";
			$update=$this->database->rawQuery($sql);
		}			
	}

	// get the corresponding value for a specific property and a specific item
	function getPropertyValue($item_id, $property_id){
		$sql = "SELECT itempropertyvalue_content FROM ItemPropertyValues WHERE itempropertyvalue_item_id='$item_id' AND itempropertyvalue_property_id='$property_id'";
		$result = $this->database->queryToArray($sql);
		return $result[0]["itempropertyvalue_content"];
	}
	
	function getPropertyLabel($property_id){
		return $this->database->specificQuery("itemproperty_name", "itemproperty_id", $property_id, "ItemProperties");
	}
	
	function getPropertyVarName($property_id){
		return $this->database->specificQuery("itemproperty_varname", "itemproperty_id", $property_id, "ItemProperties");
	}
	
}

?>