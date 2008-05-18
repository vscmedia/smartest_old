<?php
/**
 *
 * PHP versions 4/5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

 require_once 'Managers/SchemaManager.class.php';

class Vocabulary extends SmartestApplication{
  
	var $schemaManager;
	
	function __moduleConstruct(){
		$this->schemaManager = new SchemaManager();
	}
 
	function general(){
		return true;
	}
	function addVocabulary($get){
		
		$schema = $this->schemaManager->getSchema($get['schema_id']);        
		$vocabulary = $this->schemaManager->getSchemaVocabulary($get['schemadefinition_id']);
		$vocabularies = $this->database->queryToArray("SELECT * FROM Vocabulary ORDER BY vocabulary_name");
		
		return( array('schema'=>$schema, 'vocabulary'=>$vocabulary, 'vocabularies'=>$vocabularies) );    
	}
	
	function addVocabularyToSchemaAction($get, $post){
		
		$voc = $this->database->queryToArray($sql);
		$vocabulary_id = $post['vocabulary_id'];
		$schemadefinition_level = 	$post['schemadefinition_level'] + 1;

		if(!isset($post['schemadefinition_parent_id'])){
			$post['schemadefinition_parent_id'] = 0;
		}
		
		$sql = "INSERT INTO SchemaDefinitions 
						(`schemadefinition_schema_id`, `schemadefinition_vocabulary_id`, `schemadefinition_parent_id`, `schemadefinition_level`) 
						VALUES 
						('".$post['schemadefinition_schema_id']."', '".$vocabulary_id."', '".$post['schemadefinition_parent_id']."', '".$schemadefinition_level."') ";

		return $this->database->rawQuery($sql);
	}
	
	function findVocabulary($search=''){
		$this->schemaManager = new SchemaManager();
		$name= $search['q'];
		$sql = "SELECT * FROM `Vocabulary` WHERE vocabulary_name LIKE '".$name."%'";
		$types = $this->database->queryToArray($sql);
    
		$resource = "<?xml version='1.0' encoding='utf-8'  ?".">\n<ul class=\"LSRes\">";    
		
		for($i=0; $i < count($types); $i++){      
			$resource .= "<li class=\"LSRow\">".$types[$i]['vocabulary_name']."</li>";      
		}
		
		$resource .= "</ul>";
    

		header('Content-type: text/xml'); 
		die( $resource );
	}
	
	function getVocabularyMembers($get){
		$this->schemaManager = new SchemaManager();
		$schema = $this->schemaManager->getSchema($get['schemadefinition_id']);        
		//print_r( array('schema'=>$schema) );    
		return( array('schema'=>$schema) );    
	}

	function editVocabulary($get){
		$id = $get['vocabulary_id'];
		$value = $this->database->queryToArray("SELECT * FROM Vocabulary WHERE vocabulary_id='$id'");
		return( array("vocabulary"=> $value[0]));				
	}

	function getItemVocabulary(){		
		return( array("vocabulary"=> $this->database->queryToArray("SELECT * FROM Vocabulary")));		
	}
	
	function deleteDefinitionVocabulary($get){
		$schema_id = mysql_real_escape_string($get['schemadefinition_id']);
		return $this->manager->deleteDefinitionVocabulary($schema_id);
	}
	
	function addNewVocabulary($get, $post){
		$name = $post['vocabulary_name'];
		$prefix = $post['vocabulary_prefix'];
		$namespace = $post['vocabulary_namespace'];
		$description = $post['vocabulary_description'];
		$type = $post['vocabulary_type'];
		
		$sql = "INSERT INTO  `Vocabulary` (`vocabulary_id` ,  `vocabulary_name` ,  `vocabulary_prefix` ,  `vocabulary_namespace` ,  `vocabulary_description` ,  `vocabulary_type` ,  `vocabulary_max` ,  `vocabulary_min` ) 
						VALUES ('',  '$name',  '$prefix',  '$namespace',  '$description',  '$type',  '0',  '0' )";
		$this->database->rawQuery($sql);
	}
	
  
}


?>
