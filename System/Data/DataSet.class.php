<?php 

include_once("System/Data/DataSetHelper.class.php");

class DataSet{

	var $arraySet;
	
	function DataSet($array){
		$this->arraySet = $array;	
		$this->database =& $_SESSION["database"];
		$this->helper = new DataSetHelper();
	}
	
	
	function setProperty($property_id, $value){
		
		$data = $this->getData();
		
		for($i=0;$i<count($data);$i++){
			if($data[$i]['itempropertyvalue_property_id'] == $property_id){
				$data[$i]['itempropertyvalue_content'] = $value;
			}
		}
		
		$this->arraySet=$data;
	} 
	
	
	function getData(){
		return $this->arraySet;
	}


	///////// THESE TWO METHODS APPLY THE CHANGES BACK TO DATABASE //
	
	// delete objects in the current dataset from the data
	function delete(){
		//loop through all the items in the array and delete them from the database
		$data = $this->getData();
		$this->helper->deleteData($data);

	}
	
	// called after insert and update operations
	function save(){
		//apply the change to db made by setProperty(int $property_id, string $value);	
		$data = $this->getData();
		$this->helper->modifyData($data);		
	}

}

?>