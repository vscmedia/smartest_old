<?php
/**
 *
 * PHP versions 4/5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

require_once 'XML/Serializer.php'; 

class Schemas extends SmartestApplication{
  
	var $filter;

	function general(){
	
	}
	
 /**
  * description
  * @access public
  */
	function startPage(){	
		
		$schemas =  $this->manager->getSchemas();

		$sql = "SELECT itemclass_schema_id FROM ItemClasses";
		$itemClasses =  $this->database->queryToArray($sql);
		$itemClassIds[] = null;
		foreach($itemClasses as $key=>$item){
			$itemClassIds[] = $item['itemclass_schema_id'];
		}		

		$itemSchemas = null;
		if(is_array($schemas)){	
		foreach($schemas as $key=>$item){
			if(array_search( $item['schema_id'], $itemClassIds )){
				$item['locked'] = "true";
				$itemSchemas[] = $item;
			}
			else{
				$item['locked'] = "false";
				$itemSchemas[] = $item;			
			}
		}
		}
		return (array("schemas"=>$itemSchemas));
	}
  	
 /**
  * description
  * @access public
  */
	function findSchema($search=''){
		$name= $search['q'];
		$sql = "SELECT * FROM `Vocabulary` WHERE itemproperty_name LIKE '".$name."%'";
		$types = $this->database->queryToArray($sql);
    
		$resource = "<?xml version='1.0' encoding='utf-8' ?".">\n<ul class=\"LSRes\">";    
		
		for($i=0; $i < count($types); $i++){      
			$resource .= "<li class=\"LSRow\">".$types[$i]['itemproperty_name']."</li>";      
		}
		
		$resource .= "</ul>";
    
		header('Content-type: text/xml'); 
		die( $resource );
	}
	
 /**
  * description
  * @access public
  */
	function createSchema(){				
		return ( array( "schemas" => $this->manager->getSchemas() ) );
    
	}
	
 /**
  * description
  * @access public
  */
	function createSchemaAction($get, $post){
		$schema_name = $post['schema_name'];
		$schema_name_space = $post['schema_name_space'];
		$schema_des = $post['schema_des'];
		$schema_encode = $post['schema_encode'];
		$schema_parent_id = $post['schema_parent_id'];
		$schema_root_tag = $post['schema_root_tag'];
		$schema_default_tag = $post['schema_default_tag'];
		$schema_lang = $post['schema_lang'];
		$schema_varname = $this->_string->toVarName($schema_name);
		$schema_val=$this->manager->insertSchema($schema_name,$schema_name_space,$schema_des,$schema_encode,$schema_parent_id,$schema_root_tag,$schema_default_tag,$schema_lang,$schema_varname);
		$this->manager->insertSchemadefinitions($schema_parent_id,$schema_val,$schema_name,1);
	}
	
	
	
 /**
  * description
  * @access public
  */	


	function schemaDefinition($get){
		//http://www.phpinsider.com/smarty-forum/viewtopic.php?t=7967&sid=104de413c2dd9d563122326a0bc0af42
		$schema = $this->manager->getSchema($get['schema_id']);        
		$definitionUnordered = $this->manager->getSchemaDefinition($get['schema_id']);    

		$grouped = null;
		$maxDepth = 0;
		foreach($definitionUnordered as $key=>$value){
			if($value['schemadefinition_level'] > $maxDepth){
				$maxDepth = $value['schemadefinition_level'];
			}
		}
		
		$definition = array();
		$this->createNestedArray($definitionUnordered, $definition, 0, 0, $maxDepth+1);
			
		//print_r( array('schema'=>$schema, 'definition'=>$definition) );    

		return(array('schema'=>$schema, 'definition'=>$definition));    
	}

   function filterMethod($row){
      return $row['schemadefinition_parent_id'] == $this->filter;
   } 
	 
   function createNestedArray(&$data, &$arr, $parent, $startDepth, $maxDepth){
      if ($maxDepth-- == 0) return;
      $index = 0;
      $startDepth++;

			
      $this->filter = $parent;
      $children = array_filter($data, array($this, "filterMethod"));
      foreach ($children as $child)
      {
         $arr[$index] = $child;
         $arr[$index]['depth'] = $startDepth;
         //you need to replace $child['id'] by your name of column, which is holding the id of current entry!
         $this->createNestedArray($data, $arr[$index]['children'], $child['vocabulary_id'], $startDepth, $maxDepth);
         $index++;
      }
   }
   function checkchild(&$data,$parent,$startDepth){
      $this->filter = $parent;
      $children = array_filter($data, array($this, "filterMethod"));
	if(count($children)>1){return true;}
	else{return false;}
   } 
     function createNestedArrayXML(&$data, &$arr, $parent, $startDepth, $maxDepth){
      if ($maxDepth-- == 0) return;
      $index = 0;
      $startDepth++;

			
      $this->filter = $parent;
      $children = array_filter($data, array($this, "filterMethod"));
      foreach ($children as $key=>$child)
      {
	//print_r($child);echo '<br><br>';
        $attribute['name'] = strtolower($child['vocabulary_name']);
	 $attribute['type'] = strtolower($child['vocabulary_type']);
	if($attribute){
		
        if($this->checkchild(&$data,$child['vocabulary_id'],$startDepth)){
	$arr['complexType']['_attributes']=$attribute;
       		 $this->createNestedArrayXML($data, $arr['complexType']['sequence'], $child['vocabulary_id'], $startDepth, $maxDepth,$getItems,$paring_details,$paring_id);
		
		}
	else{$arr[]['_attributes']=$attribute;}
	}	
	//print_r($arr);echo '<br><br>';
         $index++;
      }
   }
	function exportXmlSchema($get){ 
	$definitionUnordered = $this->manager->getSchemaDefinition($get['schema_id']);    

		$grouped = null;
		$maxDepth = 0;
		foreach($definitionUnordered as $key=>$value){
			if($value['schemadefinition_level'] > $maxDepth){
				$maxDepth = $value['schemadefinition_level'];
			}
			if($value['schemadefinition_required'] =="TRUE"){
				$reqdefinitionUnordered[] = $value;
			}
		}
		
		$definition = array();
		$this->createNestedArrayXML($reqdefinitionUnordered, $definition, 0, 0, $maxDepth+1);
//print_r($definition);
 		
    	$serializer_options = array ( 
			'addDecl' => TRUE, 
			'encoding' => 'UTF-8', 
			'indent' => '  ', 
			'defaultTagName' => 'element',
			'rootName' => "schema", 
      "attributesArray" => "_attributes",
			'rootAttributes' => array (
			'xmlns' => 'http://www.w3.org/2001/XMLSchema',
				'lang' => 'en',
				'xml:lang' => 'en'
				), 
    	); 




		$serializer = &new XML_serializer($serializer_options); 
		$status = $serializer->serialize($definition); 
		
		if (PEAR::isError($status)) { 
			// die($status->getMessage()); 
			$this->_error($status->getMessage());
		}
  
		header('Content-type: text/xml'); 
 		die( $serializer->getSerializedData());  
	}
	
	function generateXmlRecursively($schemaChildrened, $schema, $parent, $startDepth, $maxDepth){
		for($i = 0; $i < count($schemaChildrened); $i++){
			$item = $schemaChildrened[$i];			
			if(is_array($schemaChildrened['childen'])){
				$resource[] = array( "complexType" =>
												array( "sequence" => 
													array( "element" => 
														array("_attributes" => 
															array( "name" => $item['vocabulary_name'], 
																			"type" => strtolower($item['vocabulary_type']))) )) ) ;
				//$this->generateXmlRecursively($schemaChildrened[$i], $schema, $schemaChildrened['schemadefinition_level'], 0, $maxDepth );
			}
			else{
				$resource[] = array("_attributes" => array( "name" => $item['vocabulary_name'], "type" => strtolower($item['vocabulary_type'])));
			}
		}
	}
	function editSchemaVocabularyDetails($get){
		$vocabulary_id = $get['vocabulary_id'];
		$schemadefinition_id = $get['schemadefinition_id'];
		$schema_id = $get['schema_id'];
		$type=$this->manager->getVocabulary_type($vocabulary_id);	
		if($type['vocabulary_definition']=='TRUE'){
		header("Location:".$this->domain.$this->module."/editSchemaVocabulary?schemadefinition_id=$schemadefinition_id");
		}
		else{
		header("Location:".$this->domain.$this->module."/editAttribute?schemadefinition_id=$schemadefinition_id&vocabulary_id=$vocabulary_id&schema_id=$schema_id");
		}			
	}
	function editSchemaVocabulary($get){
		$id = $get['schemadefinition_id'];
		$value=$this->manager->getSchemaVocabulary($id);
		return( array("vocabulary"=> $value[0]));					
	}
	function updateSchemaVocabulary($get,$post){
		$vocabulary_id = $post['vocabulary_id'];
		$schema_id = $post['schema_id'];
		$schema_definision_id = $post['schema_definision_id'];

		$vocabulary_name = $post['vocabulary_name'];
		$vocabulary_datatype = $post['vocabulary_datatype'];
		$vocabulary_prefix = $post['vocabulary_prefix'];		
		$schemadefinition_required = $post['schemadefinition_required'];
			switch($vocabulary_datatype){					
					case "NUMERIC":
						$default_value=$post['default_value']['NUMERIC'];	
						break;	
					case "BOOLEAN":
						$default_value=$post['default_value']['BOOLEAN'];	
						break;
					case "TEXT":
						$default_value=$post['default_value']['TEXT'];	
						break;
					case "NODE":
					case "STRING":
						$default_value=$post['default_value']['STRING'];	
						break;	
			}
		
		$level=$this->manager->getSchemaDefinisionLevel($schema_definision_id);
		$new_level=$level+1;

		$this->manager->updateVocabulary($vocabulary_id,$vocabulary_name,$vocabulary_prefix,$vocabulary_datatype,$default_value);
		$this->manager->updateSchemaDefinition($schemadefinition_required,$vocabulary_id,$schema_id);


	}

	function addSchemaVocabulary($get){
		$schemadefinition_id = $get['schemadefinition_id'];
		$schema_id = $get['schema_id'];$type_node = $get['type_node'];
		$vocabulary_id = $get['vocabulary_id'];
		$schema_name=$this->manager->getSchemaName($schema_id);
		return array("schema_id" => $schema_id,"vocabulary_id"=>$vocabulary_id,"schemadefinition_id"=>$schemadefinition_id,"schema_name"=>$schema_name,"type_node"=>$type_node);					
	}
	function insertSchemaVocabulary($get,$post){
		$schema_definision_id = $post['schema_definision_id'];
		$parent_vocabulary_id = $post['vocabulary_id'];
		$schema_id = $post['schema_id'];

		$vocabulary_name = $post['vocabulary_name'];
		$vocabulary_datatype = $post['vocabulary_datatype'];
		$prefix = $post['prefix'];
		$schemadefinition_required = $post['schemadefinition_required'];
			switch($vocabulary_datatype){					
					case "NUMERIC":
						$default_value=$post['default_value']['NUMERIC'];	
						break;	
					case "BOOLEAN":
						$default_value=$post['default_value']['BOOLEAN'];	
						break;
					case "TEXT":
						$default_value=$post['default_value']['TEXT'];	
						break;
					case "NODE":
					case "STRING":
						$default_value=$post['default_value']['STRING'];	
						break;	
			}

		$level=$this->manager->getSchemaDefinisionLevel($schema_definision_id);
		$new_level=$level+1;
		
		$vocabulary_id=$this->manager->insertVocabulary($vocabulary_name,$prefix,$vocabulary_datatype,$default_value);
		$this->manager->insertSchemaDefinition($schemadefinition_required,$parent_vocabulary_id,$schema_id,$vocabulary_id,$new_level);	
	}

	function deleteSchemaElement($get){
		$schemadefinition_id = mysql_real_escape_string($get['schemadefinition_id']);
		$vocabulary_id = mysql_real_escape_string($get['vocabulary_id']);
		$schema_id = mysql_real_escape_string($get['schema_id']);
		$this->manager->deleteSchema($schemadefinition_id,$vocabulary_id);

	}
	function removeSchemaElement($get, $post){
		$schema_id = mysql_real_escape_string($post['schema_id']);
		$schemadefinition_id = mysql_real_escape_string($post['schemadefinition_id']);
		$vocabulary_id = mysql_real_escape_string($post['vocabulary_id']);
		$this->manager->deleteSchema($schemadefinition_id,$vocabulary_id);

	}
	function deleteSchemaAction($get){
		$schema_id = mysql_real_escape_string($get['schema_id']);
		$no=$this->manager->getSchemaNumModels($schema_id);
		$models=$this->manager->getSchemaModelsList($schema_id);		
		return (array( "models" => $models,"count" =>$no,"schema_id"=>$schema_id));
	}
	function removeSchema($get, $post){
		$schema_id = mysql_real_escape_string($post['schema_id']); 
		$shema_def=$this->manager->getSchemaDefinition($schema_id);	
			foreach($shema_def as $key=>$item){
				$this->manager->deleteSchema($item['schemadefinition_id'],$item['vocabulary_id']);
			}
		$this->manager->deleteSchemas($schema_id);
	}

	function duplicateSchemaAction($get){
		$schema_id = mysql_real_escape_string($get['schema_id']);
		$schema_details= $this->manager->getSchema($schema_id);

		$name=mysql_real_escape_string($schema_details['schema_name']);	
		$schema_name_space = mysql_real_escape_string($schema_details['schema_namespace']);	
		$schema_des =mysql_real_escape_string($schema_details['schema_description']);	
		$schema_encode = mysql_real_escape_string($schema_details['schema_parent_id']);	
		$schema_parent_id = mysql_real_escape_string($schema_details['schema_parent_id']);	
		$schema_root_tag = mysql_real_escape_string($schema_details['schema_root_tag']);	
		$schema_default_tag = mysql_real_escape_string($schema_details['schema_default_tag']);	
		$schema_lang = mysql_real_escape_string($schema_details['schema_lang']);

		$schema_name=$this->manager->getUniqueSchemaName($name);	
		$schema_val=$this->manager->insertSchema($schema_name,$schema_name_space,$schema_des,$schema_encode,$schema_parent_id,$schema_root_tag,$schema_default_tag,$schema_lang);

		$this->manager->duplicateSchemadefinitions($schema_id,$schema_val);
	}
	function renameSchema($get){		
		$schema_id = mysql_real_escape_string($get['schema_id']);
		$name=$this->manager->getSchemaName($schema_id);
		return ( array( "name" => $name,"id" =>$schema_id ));	
			
	}
	function renameSchemaAction($get,$post){		
		$schema_id = mysql_real_escape_string($post['schema_id']);	
		$schema_name = mysql_real_escape_string($post['schema_name']);	
		$this->manager->updateSchemaName($schema_id,$schema_name);
	}
	function addSchemaVocabularyNode($get){
		$schemadefinition_id = $get['schemadefinition_id'];
		$schema_id = $get['schema_id'];
		$vocabulary_id = $get['vocabulary_id'];
		$schema_name=$this->manager->getSchemaName($schema_id);
		return array("schema_id" => $schema_id,"vocabulary_id"=>$vocabulary_id,"schemadefinition_id"=>$schemadefinition_id,"schema_name"=>$schema_name);						
	}
	function insertSchemaVocabularyNode($get,$post){
		$schema_definision_id = $post['schema_definision_id'];
		$parent_vocabulary_id = $post['vocabulary_id'];
		$schema_id = $post['schema_id'];

		$vocabulary_name = $post['vocabulary_name'];
		$default_value = $post['default_value'];
		$prefix = $post['prefix'];
		$schemadefinition_required = $post['schemadefinition_required'];
		
		$level=$this->manager->getSchemaDefinisionLevel($schema_definision_id);
		$new_level=$level+1;
		
		$vocabulary_id=$this->manager->insertVocabulary($vocabulary_name,$prefix,'NODE',$default_value);
		$this->manager->insertSchemaDefinition($schemadefinition_required,$parent_vocabulary_id,$schema_id,$vocabulary_id,$new_level);	

	
	}
	function addAttribute($get){
	$schema = $this->manager->getSchema($get['schema_id']); 	
	$id=$get['schemadefinition_id'];
	$value=$this->manager->getSchemaVocabulary($id);
	return array("schema" => $schema, "vocabulary" => $value[0]);
	}
	function insertAttribute($get,$post){
	$parent_vocabulary_id=$post['vocabulary_id'];
	$schemadefinition_id=$post['schemadefinition_id'];
	$schema_id=$post['schema_id'];

	$name=$post['name'];
	$value=$post['value'];

	$level=$this->manager->getSchemaDefinisionLevel($schemadefinition_id);
	$new_level=$level+1;

	$vocabulary_id=$this->manager->insertAttribute($parent_vocabulary_id,$name,$value);

	$this->manager->insertSchemaDefinition('TRUE',$parent_vocabulary_id,$schema_id,$vocabulary_id,$new_level);	
	}
	function manageAttributes($get){
	$schema = $this->manager->getSchema($get['schema_id']); 	
	$id=$get['schemadefinition_id'];
	$value=$this->manager->getSchemaVocabulary($id);
	$attributes=$this->manager->getAttributes($get['vocabulary_id']);
	return array("schema" => $schema, "vocabulary" => $value[0],"attributes"=>$attributes);
	}
	function editAttribute($get){
	$schema = $this->manager->getSchema($get['schema_id']); 	
	$vocabulary_id=$get['vocabulary_id'];
	$attributes=$this->manager->getVocabulary_id($vocabulary_id);
// 	$element=$attributes[0]['vocabulary_parent_id'];
// 	$element_value=$this->manager->getVocabulary_id($element);
// 	$element_definition_id=$this->manager->getSchemaDefinitionid($element);
// 	return array("schema" => $schema, "parentvocabulary" => $element_value[0],"parentschemadefinition_id"=>$element_definition_id,"attributes"=>$attributes[0]);
	return array("schema" => $schema,"attributes"=>$attributes[0]);
	}
	function updateAttribute($get,$post){
	$attribute_id=$post['vocabulary_id'];
	$name=$post['name'];
	$value=$post['value'];
	$this->manager->updateAttribute($attribute_id,$name,$value);	
	}
	function deleteAttribute($get){
	$attribute_id=$get['attribute_id'];
	$this->manager->deleteAttribute($attribute_id);	
	}
	function setRepeatElement($get){
	$schema_id=$get['schema_id'];
	$vocabulary_id=$get['vocabulary_id'];
	$details=$this->manager->getSchemaDefDetails($schema_id);
	foreach($details as $key=>$item){
		if($item['vocabulary_id']==$vocabulary_id){$itrate=1;}
		else{$itrate=0;}
		$this->manager->updateRepeatValue($item['vocabulary_id'],$itrate);
	}
	}
}


?>
