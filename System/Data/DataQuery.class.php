<?php

include_once("System/Data/DataSet.class.php");
include_once("System/Data/Condition.class.php");
include_once("System/Data/DataQueryHelper.class.php");

class DataQuery{
	
	// which model is being worked with?
	var $model = null;
	
	// model is valid
	var $modelIsValid = false;
	
	// the helper object
	var $dataSetHelper;
	
	// an array of conditions
	var $conditions;
	
	// an array of items that are in the current data set
	var $items;
	
	// data access object
	var $database;
	
	const EQUAL = 0;
	const EQUALS = 0;
	const EQUALTO = 0;
	const EQUAL_TO = 0;
	
	const NOT_EQUAL = 1;
	const NOTEQUAL = 1;
	
	const CONTAINS = 2;
	
	const NOTCONTAINS = 3;
	const NOT_CONTAINS = 3;
	const DOESNOTCONTAIN = 3;
	const DOES_NOT_CONTAIN = 3;
	
	const STARTSWITH = 4;
	const STARTS_WITH = 4;
	
	const ENDSWITH = 5;
	const ENDS_WITH = 5;
	
	// constructor
	function DataQuery($model_id){
		
		$this->database = $_SESSION["database"];
		$this->helper = new DataQueryHelper($this->database);
		$this->conditions = array(); 
		
		if(is_numeric($model_id) && $this->helper->getIsValidItemClass($model_id)){
			$this->model = $model_id;	
			$this->helper->defineModelProperties($model_id);

		}	
	}
	
	// tell the data manager which model you are using
	function setModel($model_id){
		
		if(!$this->model  && $this->helper->getIsValidItemClass($model_id)){
			$this->model = $model_id;
		}
		
		$this->helper->defineModelProperties($model_id);
	}
	
	// set a condition for inclusion in the dataset
	function addCondition($property_id, $operator, $value){
		if($this->model && ($this->helper->getPropertyLabel($property_id))){	
			$this->conditions[] = new Condition($property_id,$operator,$value);			
		}	
	}
	
	function getDataArray($dataset){
		return $dataset->arraySet;	
	
	}
	
	// retrieve the current dataset with property name as key value
	function select(){
		$setArray = $this->helper->getModelProperties($this->conditions,$this->model,'id');
		$datasetObject = new DataSet($setArray);
		return $datasetObject;
	}

	// retrieve the current dataset with property id as key value
	function selectAsIds(){
		return $this->helper->getModelProperties($this->conditions,$this->model,'id');
	}

	// retrieve the current data as array
	function selectToArray(){
		return $this->helper->getModelProperties($this->conditions,$this->model,'name');
	}

	// get an item with a particular id
	function retrieveByPk($id){
		$this->helper->getItemDetails($id);
	}
	
	// return the number of items in the current dataset
	function count($dataset){
		$array=$this->getDataArray($dataset);
		return count($array);
	}
	
	// set the sort order of the dataset(type =ASC or DESC)
	function setSortOrder($property_array, $type){
		if($type=="asc"){
			return array_multisort($property_array, SORT_ASC);
		}else{
			return array_multisort($property_array, SORT_DESC);
		}
	}
	
	function intersect(){
		func_get_args();
	}
	
	function merge(){
		func_get_args();
	}
}

?>