<?php
/**
 *
 * PHP versions 4/5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

require_once 'Managers/SchemasManager.class.php';
require_once 'XML/Serializer.php'; 

class Schema extends ModuleBase{
  
	var $filter;

	function general(){
	
	}
	
 /**
  * description
  * @access public
  */
	function getItemSchemas(){
		
		$sql = "SELECT * FROM Schemas";
		$schemas =  $this->database->queryToArray($sql);

		$sql = "SELECT itemclass_schema_id FROM ItemClasses";
		$itemClasses =  $this->database->queryToArray($sql);
		$itemClassIds = null;
		foreach($itemClasses as $key=>$item){
			$itemClassIds[] = $item['itemclass_schema_id'];
		}		

		$itemSchemas = null;
		foreach($schemas as $key=>$item){
			if(array_search ( $item['schema_id'], $itemClassIds )){
				$item['locked'] = "true";
				$itemSchemas[] = $item;
			}
			else{
				$item['locked'] = "false";
				$itemSchemas[] = $item;			
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
		$this->manager = new SchemaManager();
		return ( array( "schemas" => $this->manager->getSchemas() ) );
    
	}
	
 /**
  * description
  * @access public
  */
	function createSchemaAction($get, $post){
		$this->manager->createSchemaAction($post);
	}
	
 /**
  * description
  * @access public
  */
	function deleteSchema($get){
		$schema_id = mysql_real_escape_string($get['schema_id']);
		return $this->manager->deleteSchema($schema_id);
	}
	
	
 /**
  * description
  * @access public
  */	
	function deleteSchemaElement($get){
		$schemadefinition_id = mysql_real_escape_string($get['schemadefinition_id']);
		$sql = "DELETE FROM SchemaDefinitions WHERE SchemaDefinitions.schemadefinition_id='$schemadefinition_id' LIMIT 1";
		$this->database->rawQuery($sql);
	}
	


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
			
		return( array('schema'=>$schema, 'definition'=>$definition) );    
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
    
	function exportXmlSchema($get){ 
  
		$this->manager = new SchemaManager();
		$schemaUnordered = $this->manager->getSchemaDefinition($get["schema_id"]);

		$rootName = $schema[0]['vocabulary_prefix'];
		
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
			$maxDepth = 0;
		foreach($schemaUnordered as $key=>$value){
			if($value['schemadefinition_level'] > $maxDepth){
				$maxDepth = $value['schemadefinition_level'];
			}
		}
    //print_r($schema);
		$resource = null;
		$schema = null;
		$this->createNestedArray($schemaUnordered, $schemaChildrened, 0, 0, $maxDepth+1);
		$this->generateXmlRecursively($schemaChildrened, $schema, 0, 0, $maxDepth+1 );
		
		$serializer = &new XML_serializer($serializer_options); 
		$status = $serializer->serialize($resource); 
		
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
	  
}


?>
