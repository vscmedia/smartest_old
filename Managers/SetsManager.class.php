<?php

include_once "System/Helpers/ItemsHelper.class.php";

/**
 * Implements the Schema manager
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

class SetsManager{

	private $database;	
	private $itemsManager;
	
 /**
  * description
  * @access public
  */
	function __construct(){
		$this->database = SmartestDatabase::getInstance('SMARTEST');
		$this->itemsManager = new ItemsHelper();
	}
	
	function getSets($site_id=''){
	    
	    $sql = "SELECT * FROM Sets";
	    
		return $this->database->queryToArray($sql);
	}

	function getSetNames(){
		$sets = $this->database->queryToArray("SELECT set_name FROM Sets");
		$names = null;
		foreach($sets as $key=>$value){
			$names[] = $value['set_name'];
		}
		return $names;
	}

	function getSet($set_id){
		$sql = "SELECT * FROM Sets, ItemClasses WHERE Sets.set_id='$set_id' AND Sets.set_itemclass_id=ItemClasses.itemclass_id";
		//echo $sql;
		$set = $this->database->queryToArray($sql);
		return $set[0];
	}

	function getSetRuleNames($set_id){
		if(is_numeric($set_id)){
			return false;
		}
		
		$sets = $this->database->queryToArray("SELECT * FROM SetRules WHERE  setrule_set_id=$set_id");
		$names = null;
		foreach($sets as $key=>$value){
			$names[] = $value['set_name'];
		}
		return $names;
	}
	
	function getSetRules($set_id){
		if(is_numeric($set_id)){
			//return false;
		}
		$sql = "SELECT * FROM SetRules WHERE  setrule_set_id=$set_id";
		//echo $sql;
		$sets = $this->database->queryToArray($sql);
		return $sets;
	}
	
	function deleteSetRule($setrule_id){
	$sql = "DELETE FROM SetRules WHERE setrule_id='$setrule_id' LIMIT 1";
	$this->database->query($sql);	
	}

	function addSetRule($set_id, $label, $itemproperty_id, $rule, $value){
		$sql="INSERT INTO SetRules(setrule_set_id,setrule_label,setrule_itemproperty_id,setrule_rule,setrule_value) VALUES ('$set_id','$label','$itemproperty_id','$rule','$value')";//echo $sql;
		return $this->database->rawQuery($sql);								
	}
	function deleteSet($set_id){
	$type = $this->database->specificQuery("set_type", "set_id", $set_id, "Sets");
	if($type=='DYNAMIC'){
	$sql_rule = "DELETE FROM SetRules WHERE setrule_set_id='$set_id'";
	$this->database->query($sql_rule);
	}
	if($type=='STATIC'){
	$sql_rule = "DELETE FROM SetsItemsLookup WHERE setlookup_set_id='$set_id'";/*echo $sql_rule;*/
	$this->database->query($sql_rule);
	}
	$sql = "DELETE FROM Sets WHERE set_id='$set_id'";
	$this->database->query($sql);
	}
	function insertSet($set_name,$model_id,$set_type,$set_varname){
	$sql="INSERT INTO Sets(set_name,set_label,set_itemclass_id,set_type,set_varname) VALUES ('$set_name','$set_name','$model_id','$set_type','$set_varname')";/*echo $sql;*/
	$this->database->rawQuery($sql);
	return $this->database->getInsertId();
	}
	function previewSet($set_id,$set_type,$model_id){
	if($set_type=='DYNAMIC'){
		$rule = $this->getSetRules($set_id);
		$sql="SELECT * FROM Items,ItemPropertyValues WHERE Items.item_itemclass_id='$model_id' AND Items.item_public='TRUE' AND Items.item_id=ItemPropertyValues.itempropertyvalue_item_id ";
		$result=$this->database->query($sql);
		$i=0;
		foreach($result as $key=>$value){

			$item_id=$value['item_id'];		
			$item_propertyid=$value['itempropertyvalue_property_id'];
			$item_property_value=$value['itempropertyvalue_content'];
			$name=$value['item_name'];
			foreach($rule as $key=>$val){
			$property_id=$val['setrule_itemproperty_id'];
			$condition=$val['setrule_rule'];
			$content_value=$val['setrule_value'];
			if($property_id=="_name"){
			$item_property_value=$name;$item_propertyid="_name";}
			if($property_id=="_id"){$item_property_value=$item_id;$item_propertyid="_id";}


				switch($condition){
				case CONTAINS:	
				if($item_propertyid==$property_id && substr_count($item_property_value, $content_value))
				{$i=$i+1;}
				break;
				case EQUALS:
				if($item_propertyid==$property_id && $item_property_value==$content_value)
				{$i=$i+1;}
				break;
				case GREATER:
				if($item_propertyid==$property_id && $item_property_value>$content_value)
				{$i=$i+1;}
				break;
				case LESS:
				if($item_propertyid==$property_id && $item_property_value>$content_value)
				{$i=$i+1;}
				break;
				}
			}
			if($i==count($rule)){$items[]=$value;}$i=0;
		}
	}
	if($set_type=='STATIC'){
		$sql="SELECT * FROM SetsItemsLookup,Items WHERE SetsItemsLookup.setlookup_set_id='$set_id' AND SetsItemsLookup.setlookup_item_id=Items.item_id ORDER BY SetsItemsLookup.setlookup_order,Items.item_name ASC";
		$result=$this->database->query($sql);/*print_r($results);*/
		$items=$result;
	}
	return $items;
	}
	function getUniqueSetName($name){	    		
		$name_temp=$name;
		$sql="SELECT * from Sets where set_name = '$name'";
		$count=$this->database->howMany($sql);
		$i=1;
		
		while($count>0){
		
			$i=$i+1;
			$name=$name_temp.$i;
			$sql="SELECT * from Sets where set_name = '$name'";
			$v1=$this->database->howMany($sql);
			
			if($v1==0){
				break;
			}else{
				$count=$v1;
			}
		}
		return $name;
	}
	function getDataSetItemProperties($set_id,$set_type,$model_id){
	$result=$this->previewSet($set_id,$set_type,$model_id); 
	if(is_array($result)){
	foreach($result as $key=>$value){
	$item_id=$value['item_id'];
	$class_id=$value['item_itemclass_id'];
	$itemspropertyvalues = $this->itemsManager->getItemPropertyValues($item_id,$class_id);
// print_r($itemspropertyvalues);
	if(is_array($itemspropertyvalues)){
		foreach($itemspropertyvalues as $key2=>$Propertydetails){
		$p_name=$Propertydetails['itemproperty_varname'];
		if($Propertydetails['itempropertyvalue_content']==''){
		$p_value=$Propertydetails['itemproperty_defaultvalue'];
		}
		else{
		$p_value=$Propertydetails['itempropertyvalue_content'];
		}			
		$property_details[$p_name]=$p_value;
		}
	}

		$setitemproperties[$key]['item_id']=$item_id;
		$setitemproperties[$key]['item_name']=$value['item_name'];
		$setitemproperties[$key]['property_details']=$property_details;
	}
	}
        return $setitemproperties;
	}
	function getStaticSetItems($set_id){

		$sql = "SELECT * FROM SetsItemsLookup WHERE  setlookup_set_id=$set_id";
		//echo $sql;
		$sets = $this->database->queryToArray($sql);
		return $sets;
	}
	function addStaticSetItems($set_id, $setlookup_item_id, $setlookup_order){
		$sql="INSERT INTO SetsItemsLookup (setlookup_set_id,setlookup_item_id,setlookup_order) VALUES ('$set_id','$setlookup_item_id','$setlookup_order')";
		return $this->database->rawQuery($sql);								
	}
	function getschemasettings($schema_id){
	$sql = "SELECT * FROM SchemaDefinitions, Vocabulary
              WHERE Vocabulary.vocabulary_id = SchemaDefinitions.schemadefinition_vocabulary_id
              AND SchemaDefinitions.schemadefinition_schema_id ='".$schema_id."' ORDER BY Vocabulary.vocabulary_definition,SchemaDefinitions.schemadefinition_vocabulary_id";
	$schemas = $this->database->queryToArray($sql); 
	$repeatingDefinition=$this->getRepeatingDefinition($schema_id);
	foreach($schemas as $value){
	if($value['vocabulary_type']!='NODE' && $value['vocabulary_iterates'] !=1 && $value['schemadefinition_required'] == "TRUE"){
	if(!in_array($value,$repeatingDefinition)){$array[]=$value;}	
	}
	}
	return $array;
	}
	function getRepeatingDefinition($schema_id){
		$sql = "SELECT * FROM SchemaDefinitions, Vocabulary WHERE SchemaDefinitions.schemadefinition_schema_id='$schema_id' AND SchemaDefinitions.schemadefinition_required='TRUE' AND SchemaDefinitions.schemadefinition_vocabulary_id=Vocabulary.vocabulary_id AND Vocabulary.vocabulary_iterates ='1' ";//echo $sql;
		$result= $this->database->queryToArray($sql);
		$parent_id=$result[0]['vocabulary_id'];
		$sql_definision="SELECT * FROM SchemaDefinitions, Vocabulary WHERE SchemaDefinitions.schemadefinition_parent_id='$parent_id' AND SchemaDefinitions.schemadefinition_required='TRUE' AND SchemaDefinitions.schemadefinition_vocabulary_id=Vocabulary.vocabulary_id ORDER BY Vocabulary.vocabulary_definition,SchemaDefinitions.schemadefinition_vocabulary_id";//echo $sql_definision;
		return $this->database->queryToArray($sql_definision);
	}
	function setDataExport($schema_id,$class_id,$property_id,$vocabulary_id){
		$sql = "INSERT INTO DataExports(dataexport_schema_id,dataexport_itemclass_id, dataexport_property_id,dataexport_vocabulary_id) VALUES ('".$schema_id."', '".$class_id."', '".$property_id."','".$vocabulary_id."')";//echo $sql;
		return $this->database->rawQuery($sql);
	}
	function insertParing($schema,$class_id){
		$sql = "INSERT INTO Pairings(paring_schema_id,paring_model_id) VALUES ('".$schema."', '".$class_id."')";//echo $sql;
		$this->database->rawQuery($sql);
		return $this->database->getInsertId();
	}
	function insertDataExport($name,$set_id,$paring_id,$varname){
		$sql = "INSERT INTO DataExports(dataexport_name,dataexport_set_id,dataexport_pairing_id,dataexport_varname) VALUES ('".$name."', '".$set_id."', '".$paring_id."', '".$varname."')";//echo $sql;
		$this->database->rawQuery($sql);
		return $this->database->getInsertId();
	}
	function insertParingDetail($paring_id,$property_id,$vocabulary_id){
		$sql = "INSERT INTO PairingDetails(paring_id,property_id,vocabulary_id) VALUES ('".$paring_id."', '".$property_id."', '".$vocabulary_id."')";//echo $sql;
		return $this->database->rawQuery($sql);		
	}
	function insertSettingDetail($paring_id,$vocabulary_id,$vocabulary_value){
		$sql = "INSERT INTO PairingDetails(paring_id,setting_id,setting_value) VALUES ('".$paring_id."', '".$vocabulary_id."', '".$vocabulary_value."')";//echo $sql;
		return $this->database->rawQuery($sql);		
	}
	function getPairingDetail($paring_id){
		$sql = "SELECT * FROM PairingDetails WHERE paring_id=$paring_id";//echo $sql;
		$pairing=$this->database->queryToArray($sql);	return $pairing;	
	}
	function getparing($set_id,$schema_id){
		$sql = "SELECT * FROM DataExports,Pairings WHERE DataExports.dataexport_set_id=$set_id AND DataExports.dataexport_pairing_id = Pairings.paring_id AND Pairings.paring_schema_id=$schema_id ";//echo $sql;
		$pairing=$this->database->queryToArray($sql);	return $pairing;	
	}
	function getDataExports(){
		$sql = "SELECT * FROM DataExports ";//echo $sql;
		$pairing=$this->database->queryToArray($sql);	return $pairing;	
	}
	function getSetName($id){
		$set = $this->database->specificQuery("set_name", "set_id", $id, "Sets");
		return $set;	
	}

	function getSetDetail($varname){
		$sql = "SELECT * FROM Sets WHERE set_varname='$varname' ";//echo $sql;
		$set=$this->database->queryToArray($sql);	return $set[0];			
	}
	function checkExportName($name,$set_id){
		$sql = "SELECT * FROM DataExports WHERE dataexport_name='$name' AND dataexport_set_id=$set_id";//echo $sql;
		$set=$this->database->queryToArray($sql);	
		$count=	count($set);	return $count;	
	}
	function getExportParing($set_id,$schema,$varname){
		$sql = "SELECT * FROM DataExports,Pairings WHERE DataExports.dataexport_varname='$varname' AND DataExports.dataexport_set_id = $set_id AND DataExports.dataexport_pairing_id = Pairings.paring_id AND Pairings.paring_schema_id=$schema";//echo $sql;
		$pairing=$this->database->queryToArray($sql);	return $pairing[0];	
	}
	function getSinglePairingVocabulary($pairing_id,$property_id){
		$sql = "SELECT * FROM PairingDetails WHERE paring_id=$pairing_id AND property_id=$property_id";//echo $sql;
		$pairing=$this->database->queryToArray($sql);	return $pairing[0]['vocabulary_id'];	
	}
	function getSingleParingSetting($pair_id,$setting_id){
		$sql = "SELECT * FROM PairingDetails WHERE paring_id=$pair_id AND setting_id=$setting_id";//echo $sql;
		$pairing=$this->database->queryToArray($sql);	//print_r($pairing[0]['setting_value']);
		return $pairing[0]['setting_value'];	
	}
	function updateDataExport($name,$paring_id){
		$sql ="UPDATE DataExports SET dataexport_name='".$name."' WHERE dataexport_pairing_id='".$paring_id."' LIMIT 1";//echo $sql;
		return $this->database->rawQuery($sql);	
	}
	function updateParingDetail($paring_id,$property_id,$vocabulary_id){
		$sql ="UPDATE PairingDetails SET vocabulary_id='".$vocabulary_id."' WHERE paring_id='".$paring_id."' AND property_id= '".$property_id."' LIMIT 1";//echo $sql;
		return $this->database->rawQuery($sql);	
	}
	function updateSettingDetail($paring_id,$vocabulary_id,$vocabulary_value){
		$sql ="UPDATE PairingDetails SET setting_value='".$vocabulary_value."' WHERE paring_id='".$paring_id."' AND setting_id= '".$vocabulary_id."' LIMIT 1";//echo $sql;
		return $this->database->rawQuery($sql);	
	}
	
	function addItemToStaticSet($items,$set_id){
		
		foreach($items as $key=>$value){
		    
		    $count_query="SELECT * FROM SetsItemsLookup WHERE setlookup_set_id=$set_id";
		    $count_result=$this->database->howMany($count_query);
		    $count=$count_result+1;
		    $sql="INSERT INTO SetsItemsLookup (setlookup_set_id,setlookup_item_id,setlookup_order) VALUES ('$set_id','$value','$count')";
		    $this->database->rawQuery($sql);
		    
		}
	}
	
	function removeItemFromStaticSet($items,$set_id){
		foreach($items as $key=>$value){
	$sql="DELETE FROM SetsItemsLookup WHERE setlookup_set_id = '$set_id' AND setlookup_item_id='$value' ";
	$this->database->query($sql);
		}
	}
// 	function updateSet($set_id,$items){
// 		$sql = "DELETE FROM SetsItemsLookup WHERE setlookup_set_id = '$set_id'";
//     		$this->database->query($sql);
// 		foreach($items as $key=>$value){
// 		$sql="INSERT INTO SetsItemsLookup (setlookup_set_id,setlookup_item_id,setlookup_order) VALUES ('$set_id','$value','$key')";//echo $sql;
// 		$this->database->rawQuery($sql);	
// 		}
// 	}

	function chooseSetForDataExport($export_id){
		$set_id=$this->database->specificQuery("dataexport_set_id", "dataexport_id", $export_id, "DataExports");
		$model=$this->database->specificQuery("set_itemclass_id", "set_id", $set_id, "Sets");
		$sql="SELECT * FROM Sets WHERE set_itemclass_id =$model ";
		return $this->database->queryToArray($sql);
	}
	function choosePairingForDataExport($export_id){
		$pair_id=$this->database->specificQuery("dataexport_pairing_id", "dataexport_id", $export_id, "DataExports");
		$model=$this->database->specificQuery("paring_model_id", "paring_id", $pair_id, "Pairings");
		$schema=$this->database->specificQuery("paring_schema_id", "paring_id", $pair_id, "Pairings");
		$sql="SELECT * FROM DataExports,Pairings WHERE Pairings.paring_schema_id =$schema AND  Pairings.paring_model_id=$model AND Pairings.paring_id=DataExports.dataexport_pairing_id";
		return $this->database->queryToArray($sql);
	}
	function updateDataExportFeed($export_id,$set,$pair){
		$sql ="UPDATE DataExports SET dataexport_set_id=$set,dataexport_pairing_id=$pair WHERE dataexport_id=$export_id LIMIT 1";//echo $sql;
		return $this->database->rawQuery($sql);	
	}
}
?>
