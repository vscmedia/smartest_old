<?php

class DropdownsManager{
    
    protected $database;	
    
    public function __construct(){
		$this->database = SmartestDatabase::getInstance('SMARTEST');			
	}
	
	public function getDropdowns(){
		$sql="SELECT * FROM DropDowns";
		return $this->database->queryToArray($sql);
	}
	
	public function getDropdownDetails($dropdown_id){
		$sql="SELECT * FROM DropDowns WHERE dropdown_id=$dropdown_id";
		$details= $this->database->queryToArray($sql);
		return $details[0];
	}
	
	public function getDropdownValues($dropdown_id){
		$sql="SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id=$dropdown_id ORDER BY dropdownvalue_order,dropdownvalue_label";
		$details= $this->database->queryToArray($sql);
		return $details;
	}
	
	public function getDropdownValueDetails($drop_down_value_id){
		$details= $this->database->queryToArray("SELECT * FROM DropDownValues WHERE dropdownvalue_id=$drop_down_value_id");
		return $details[0];
	}
	
	public function insertDropDown($dropdown){ 
	    $sql="INSERT INTO DropDowns(dropdown_label) VALUES ('$dropdown')";
	    return $this->database->rawQuery($sql);	
	}
	
	public function insertDropDownValue($dropdown_id,$drop_down_value,$drop_down_order){ 
	    $sql="INSERT INTO DropDownValues(dropdownvalue_dropdown_id,dropdownvalue_order,dropdownvalue_label) VALUES ('$dropdown_id','$drop_down_order','$drop_down_value')";
	    return $this->database->rawQuery($sql);	
	}
	
	public function updateDropDown($dropdown,$drop_down_id){ 
	    $sql="UPDATE DropDowns SET dropdown_label='$dropdown' WHERE dropdown_id=$drop_down_id";
	    return $this->database->rawQuery($sql);	
	}
	
	public function updateDropDownValue($dropdown_id,$drop_down_value_id,$drop_down_value,$drop_down_order){ 
	    $sql="UPDATE DropDownValues SET dropdownvalue_label='$drop_down_value',dropdownvalue_order=$drop_down_order WHERE dropdownvalue_id=$drop_down_value_id AND dropdownvalue_dropdown_id=$dropdown_id";
	    return $this->database->rawQuery($sql);	
	}
	
	public function updateDropDownOrder($dropdown_id,$order){ 
	    foreach($order as $key=>$value){
	        $id=$value;
	        $order=$key+1;
	        $sql="UPDATE DropDownValues SET dropdownvalue_order=$order WHERE dropdownvalue_id=$id AND dropdownvalue_dropdown_id=$dropdown_id";
	        $this->database->rawQuery($sql);	
	    }	
	}
	
	public function deleteDropDown($drop_down_id){ 
	    $sql = "DELETE FROM DropDownValues WHERE dropdownvalue_dropdown_id=$drop_down_id";
	    $this->database->query($sql);
	    $sql = "DELETE FROM DropDowns WHERE dropdown_id=$drop_down_id";
	    $this->database->query($sql);
	}
	
	public function deleteDropDownValue($dropdown_id,$drop_down_value_id){ 
	    $sql = "DELETE FROM DropDownValues WHERE dropdownvalue_id=$drop_down_value_id  AND dropdownvalue_dropdown_id=$dropdown_id ";
	    $this->database->query($sql);
	}
}