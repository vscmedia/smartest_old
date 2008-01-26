<?php

class DataQueryHelper{

	var $database;
	var $string;

	function DataQueryHelper($database){
		$this->database = $database;
//		$this->string = new StringHelper();
// 		$this->defineOperators();
	}
	function getIsValidItemClass($model_id){
		$sql = "SELECT * FROM ItemClasses WHERE itemclass_id=$model_id";
		$num = $this->database->howMany($sql);
		return $num;
	}

	function defineModelProperties($model_id){
		$sql = "SELECT itemproperty_id, itemproperty_varname FROM ItemProperties WHERE  itemproperty_itemclass_id= $model_id";
		$results = $this->database->queryToArray($sql);
		
		if(is_array($results)){;
			foreach($results as $item_property){
				$property=$this->getConstantName($item_property["itemproperty_varname"]);/*echo $property.'<br>';*/
				if(!defined($property)){
					@define($property, $item_property["itemproperty_id"], true);
				}
			}
		}
	}
	
	function getPropertyLabel($property_id){
		if($property_id=="_name"){$property_name='item_name';}
		elseif($property_id=="_id"){$property_name='item_id';}
		else{$property_name=$this->database->specificQuery("itemproperty_name", "itemproperty_id", $property_id, "ItemProperties");}
		return $property_name;		
	}
	
	function getConstantName($string){
		
    	$constant_name = trim($string, " ?!%$#&*|/\\");
    	$constant_name = preg_replace("/[\"'\.]+/", "", $constant_name);
    	$constant_name = preg_replace("/[^\w-_]+/", "_", $constant_name);
    	$constant_name = strtoupper($constant_name);
    	
    	return $constant_name;
	}
	
	function getModelProperties($conditions, $model_id, $key){
		$resultArray = array();
		//retrives the properties and values in the db
		$item_sql="SELECT *  FROM Items,ItemPropertyValues,ItemProperties WHERE Items.item_itemclass_id=$model_id and Items.item_id=ItemPropertyValues.itempropertyvalue_item_id and ItemPropertyValues.itempropertyvalue_property_id=ItemProperties.itemproperty_id";
		$item_results = $this->database->queryToArray($item_sql);

		//comparing with the applied condition
		foreach($item_results as $items){
			foreach($conditions as $item_c){
			
			$item_p=$items['itempropertyvalue_property_id'];
			$item_v=$items['itempropertyvalue_content'];$name=$value['item_name'];
			$items_id=$items['itempropertyvalue_item_id'];
			if($key=='name'){$items['itempropertyvalue_property_id']=$this->getPropertyLabel($item_p);}
				
				$cond_p=$item_c->property;
				$cond_operator=$item_c->operator;
				$cond_val=$item_c->value;

				if($cond_p=="_name"){
					$item_p="_name";
					$item_v=$name;
				}
				
				if($cond_p=="_id"){
					$cond_p="_id";
					$item_v=$items_id;
				}

				if($item_p==$cond_p){
				
					switch($cond_operator){
					case 0 : 
						if($item_v==$cond_val){
							array_push($resultArray, $items);	
						}
						break;
					case 1 : 
						if($item_v!=$cond_val){
							array_push($resultArray, $items);	
						}
						break;
					case 2 : 
						if(strstr($item_v,$cond_val)){
							array_push($resultArray, $items);				}
						break;
					case 3 : 
						if(strstr($item_v,$cond_val)==FALSE){
							array_push($resultArray, $items);	
						}
						break;
					case 4 : 
 						if(preg_match("/".$cond_val."$/i", $item_v)){
							array_push($resultArray, $items);	
						}
						break;
					case 5 : 
 						if(preg_match("/^".$cond_val."/i", $item_v)){
							array_push($resultArray, $items);	
						}
						break;
					}
				}
			}
		}
		
		return $resultArray;
	}

	function getItemDetails($id){
	$sql="SELECT *  FROM Items WHERE item_id=$id";
	$results = $this->database->queryToArray($sql);
	return $results;
	}
	function getItemClass($class_id){
		$result = $this->database->queryToArray("SELECT * FROM ItemClasses WHERE itemclass_id='$class_id'");
		return $result;
	}

}

?>