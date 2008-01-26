<?php

class DataQueryInit(){
 	
 	function getModels(){
 		$sql = "SELECT itemclass_id, itemclass_name, itemclass_plural_name FROM ItemClasses";
 		$results = $this->database->queryToArray($sql);
 		if(is_array($results)){;
 			foreach($results as $item_class){echo $item_class['itemclass_id'];
 				@define($this->modelHelper->getConstantName($item_class["itemclass_name"]), $item_class["itemclass_id"], true);
 			}
 		}
 	}
}

?>