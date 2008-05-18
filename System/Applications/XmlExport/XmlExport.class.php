<?php
include_once ("Managers/SchemasManager.class.php");
include_once ("System/Helpers/ItemsHelper.class.php");
include_once ("Managers/SetsManager.class.php");
require_once 'XML/Serializer.php';

class XmlExport extends SmartestApplication{

	var $filter;
	
	function __moduleConstruct(){	
		$this->SchemasManager = new SchemasManager();	
		$this->itemsManager = new ItemsHelper();
		$this->setsManager = new SetsManager();
	}
	
	function exportData($get){

		$set_varname=$get["set"];
		$schema_varname=$get["schema"];
		$export_varname=$get["dataexport"];//print_r($export_varname);
		$schema_details=$this->SchemasManager->getSchemaDetail($schema_varname);//print_r($schema_details);
		$schema=$schema_details['schema_id'];

		$set=$this->setsManager->getSetDetail($set_varname);
		$set_id=$set['set_id'];
		$set_type=$set['set_type'];
		$pairing=$this->setsManager->getExportParing($set_id,$schema,$export_varname); //print_r($pairing);
		$paring_id=$pairing['paring_id'];
		$class_id=$pairing['paring_model_id'];
//print_r($paring_id);

//getItems
		$getItems = $this->setsManager->previewSet($set_id,$set_type,$class_id);
//pairing_details
		$paring_details=$this->setsManager->getPairingDetail($paring_id);

//schema definision
		$definitionUnordered = $this->SchemasManager->getSchemaDefinition($schema);    
		$grouped = null;
		$maxDepth = 0;
		foreach($definitionUnordered as $value){
			if($value['schemadefinition_level'] > $maxDepth){
				$maxDepth = $value['schemadefinition_level'];
			}
			if($value['vocabulary_iterates'] ==1){
				$default_tag = $value['vocabulary_name'];
			}
			if($value['schemadefinition_required'] =="TRUE"){
				$reqdefinitionUnordered[] = $value;
			}
		}
		
		$schema_definition = array();
		$this->createNestedArray($reqdefinitionUnordered, $schema_definition, 0, 0, $maxDepth+1,$getItems,$paring_details,$paring_id);
		//print_r($schema_definition);
// 		$outer_tag=$schema_definition[0]['vocabulary_name'];
// 		$children=$schema_definition[0]['children'];//print_r($children);	
		
		
// 		foreach($children as $child){//print_r($child);echo '<br><br>';
// 			$key= $child['vocabulary_name'];
// 			if($child['vocabulary_iterates']==0){
// 			if($child['children']==''){
// 				$structure[$key]=$child['vocabulary_default_content'];	
// 				}
// 			else{
// 				$children=$child['children'];
// 			}
// 			
// 			}
// 		}

// $structure=$schema_definition;
// $schema_tag=null; 
// $schema_tag[$outer_tag]=$structure; 
$schemaDetails = $this->SchemasManager->getSchema($schema);
	$serializer_options = array ( 
			'addDecl' => TRUE, 
			'encoding' => $schemaDetails['schema_encoding'], 
			'indent' => '  ', 
			'defaultTagName' => $default_tag,
			'rootName' => $schemaDetails['schema_root_tag'], 
      			'attributesArray' => '_attributes',
			'rootAttributes' => array (
			'xmlns' => $schemaDetails['schema_namespace'],
				'lang' => $schemaDetails['schema_lang'],
				'xml:lang' => $schemaDetails['schema_lang']
			)
    		); 

		$serializer = &new XML_serializer($serializer_options); 
		$status = $serializer->serialize($schema_definition);
		// var_dump($resource);
		if (PEAR::isError($status)) { 
			$this->_error($status->getMessage());
		}  
		header('Content-type: text/xml'); die( $serializer->getSerializedData());
	}

   function filterMethod($row){
      return $row['schemadefinition_parent_id'] == $this->filter;
   } 
   function checkchild(&$data,$parent,$startDepth){
      $this->filter = $parent;
      $children = array_filter($data, array($this, "filterMethod"));
	if(count($children)>1){return true;}
	else{return false;}
   } 
   function createNestedArray(&$data, &$arr, $parent, $startDepth, $maxDepth,$getItems,$paring_details,$paring_id){
      if ($maxDepth-- == 0) return;
      $index = 0;
      $startDepth++;
      $this->filter = $parent;
      $children = array_filter($data, array($this, "filterMethod"));
    foreach ($children as $child)
      {
	$key_name=$child['vocabulary_name'];
	$val=$child['vocabulary_default_content'];
	$v_id=$child['vocabulary_id'];
	$itrate=$child['vocabulary_iterates'];

	if($child['vocabulary_definition']=='FALSE'){
		$attributes_array[$key_name]=$val;
	}
	elseif($itrate==0){
	
        	if($this->checkchild(&$data,$child['vocabulary_id'],$startDepth)){
       		 $this->createNestedArray($data, $arr[$key_name], $child['vocabulary_id'], $startDepth, $maxDepth,$getItems,$paring_details,$paring_id);
		}
		else {
//echo 'hi';
	$value=$this->setsManager->getSingleParingSetting($paring_id,$v_id);print_r($kk);
		if($value){$val=$value;}//print_r($value);
		$arr[$key_name]=$val;
		}	
	
	}
	elseif($itrate==1){
		if($this->checkchild(&$data,$child['vocabulary_id'],$startDepth)){
       		 $this->createNestedArray($data, $repeat, $child['vocabulary_id'], $startDepth, $maxDepth,$getItems,$paring_details,$paring_id);
		}
		else {$repeat=$val;}	
	
		for($i=0; $i<count($getItems); $i++){	
			$item_id=$getItems[$i]["item_id"];//echo '<br>'. $item_id.'<br>';
			if(is_array($paring_details)){
			foreach($paring_details as $key=>$value){
			$property_id=$value['property_id'];
			$vocabulary_id=$value['vocabulary_id'];
			$vocabulary_name=$this->SchemasManager->getVocabulary_name($vocabulary_id);
			$value=$this->itemsManager->getSingleItemPropertyValue($item_id,$property_id);
			foreach($repeat as $r_key=>$r_value){
			if($vocabulary_name==$r_key ){$repeat[$r_key] = $value;}
// 			elseif($r_value){$repeat[$r_key] = '';}
			}
			}//print_r($repeat) ;
			$arr[] = $repeat;
				}
			}

//======================================================
// 		for($i=0; $i<count($getItems); $i++){	
// 			$item_id=$getItems[$i]["item_id"];//echo '<br>'. $item_id.'<br>';
// 			if(is_array($paring_details)){
// 			foreach($paring_details as $key=>$value){
// 			$property_id=$value['property_id'];
// 			$vocabulary_id=$value['vocabulary_id'];
// 			$vocabulary_name=$this->SchemasManager->getVocabulary_name($vocabulary_id);
// 			$value=$this->itemsManager->getSingleItemPropertyValue($item_id,$property_id);
// 			if($vocabulary_name){
// 					$resource[$vocabulary_name] = $value;
// 				}				
// 			}
// 			
// 			$arr[] = $resource;
// 			}
// 		}
//=======================================================	
		}
	if($attributes_array){
	$arr['_attributes']=$attributes_array;
	}
         $index++;
      }
   }

}
	
	
?>
