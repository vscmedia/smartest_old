<?php
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
class SchemasManager{

	var $database;

 /**
  * description
  * @access public
  */
	function SchemasManager(){
		$this->database = SmartestPersistentObject::get('db:main');
	}
	
  
 /**
  * description
  * @access public
  */
  function getSchemas(){
    $sql = "SELECT * FROM Schemas";
    $schemas = $this->database->queryToArray($sql); 
    return $schemas;
  }

  
 /**
  * description
  * @access public
  */
  function getSchema($schema_id){
    $sql = "SELECT * FROM Schemas WHERE schema_id='".$schema_id."'";
    $schema = $this->database->queryToArray($sql);     
		return $schema[0];
  }

  
  function getVocabulary(){
    $sql = "SELECT vocabulary_name FROM Vocabulary";
    $vocabulary = $this->database->queryToArray($sql); 
    return $vocabulary;
  }
  
  function getVocabularyList(){
    $sql = "SELECT vocabulary_name FROM Vocabulary";
    $vocabulary = $this->database->queryToArray($sql); 
    $words = null;
    foreach($vocabulary as $item){
      $words[] = $item['vocabulary_name'];
    }
    return $words;
  }

 /**
  * description
  * @access public
  */
  function getSchemaDefinition($schema_id_or_name){
    $sql = null;
    if(is_numeric ($schema_id_or_name) ){
      $sql = "SELECT *
              FROM SchemaDefinitions, Vocabulary
              WHERE Vocabulary.vocabulary_id = SchemaDefinitions.schemadefinition_vocabulary_id
              AND SchemaDefinitions.schemadefinition_schema_id ='".$schema_id_or_name."' ORDER BY Vocabulary.vocabulary_definition,SchemaDefinitions.schemadefinition_vocabulary_id";
    }
    else if (is_string($schema_id_or_name) ){
    }
    else{
      return false;
    }

    $schemas = $this->database->queryToArray($sql); 
    return $schemas;
  }
  function getSchemaDefDetails($schema_id){
    
      $sql = "SELECT *
              FROM SchemaDefinitions, Vocabulary
              WHERE Vocabulary.vocabulary_id = SchemaDefinitions.schemadefinition_vocabulary_id
              AND SchemaDefinitions.schemadefinition_schema_id ='".$schema_id."' ORDER BY SchemaDefinitions.schemadefinition_vocabulary_id";  

    $schemas = $this->database->queryToArray($sql); 
    return $schemas;
  }
	
	function getSchemaVocabulary($id){
		$sql = "SELECT * FROM SchemaDefinitions,Vocabulary, Schemas 
			WHERE SchemaDefinitions.schemadefinition_id='$id' 
			AND SchemaDefinitions.schemadefinition_schema_id=Schemas.schema_id 
			AND SchemaDefinitions.schemadefinition_vocabulary_id=Vocabulary.vocabulary_id 
			LIMIT 1";
    $schemavocabulary = $this->database->queryToArray($sql); 
    return $schemavocabulary;				
	}
	function deleteSchemas($id){
		$sql = "DELETE FROM Schemas WHERE schema_id='$id'";
// 		echo $sql;
		return $this->database->rawQuery($sql); 
	}
	
	function deleteSchema($id,$vid){
	
		$sql = "DELETE FROM SchemaDefinitions WHERE schemadefinition_id='$id' AND schemadefinition_vocabulary_id= '$vid' LIMIT 1";
// 		echo $sql;
		$this->database->rawQuery($sql); 
		$sql2 = "DELETE FROM Vocabulary WHERE vocabulary_id='$vid'  LIMIT 1";	
		$this->database->rawQuery($sql2); 		
		
	}
	function updateVocabulary($vocabulary_id,$vocabulary_name,$vocabulary_prefix,$vocabulary_datatype,$default_value){
		$sql="UPDATE Vocabulary SET vocabulary_name='$vocabulary_name', vocabulary_type='$vocabulary_datatype', vocabulary_prefix = '$vocabulary_prefix' ,vocabulary_default_content = '$default_value'  WHERE vocabulary_id ='$vocabulary_id' LIMIT 1";/*echo $sql;*/
		return $this->database->rawQuery($sql);
	}

	function updateSchemaDefinition($schemadefinition_required,$vocabulary_id,$schema_id){
		$sql="UPDATE SchemaDefinitions SET schemadefinition_required='".$schemadefinition_required."' WHERE schemadefinition_schema_id =$schema_id AND schemadefinition_vocabulary_id =$vocabulary_id LIMIT 1 ";/*echo $sql;*/
		return $this->database->rawQuery($sql);
	}
	function insertVocabulary($vocabulary_name,$vocabulary_prefix,$vocabulary_datatype,$default_value){
		$sql="INSERT INTO Vocabulary (vocabulary_name,vocabulary_prefix,vocabulary_type,vocabulary_default_content,vocabulary_definition) VALUES ('$vocabulary_name','$vocabulary_prefix', '$vocabulary_datatype',  '$default_value','TRUE')";/*echo $sql;*/
		$this->database->rawQuery($sql);
		return $this->database->getInsertId();
	}
	function getSchemaDefinisionLevel($id){
		$sql = "SELECT schemadefinition_level FROM SchemaDefinitions WHERE schemadefinition_id='$id'";
    		$schemadefinision = $this->database->queryToArray($sql); 
    		return ($schemadefinision[0]['schemadefinition_level']);				
	}
	function insertSchemaDefinition($schemadefinition_required,$parent_vocabulary_id,$schema_id,$vocabulary_id,$new_level){
	$sql="INSERT INTO SchemaDefinitions (schemadefinition_schema_id,schemadefinition_vocabulary_id,schemadefinition_parent_id,schemadefinition_level,schemadefinition_required) VALUES ('$schema_id', '$vocabulary_id', '$parent_vocabulary_id', '$new_level', '$schemadefinition_required')"; /*echo $sql;*/
	return $this->database->rawQuery($sql);		
	}
	function insertSchema($schema_name,$schema_name_space,$schema_des,$schema_encode,$schema_parent_id,$schema_root_tag,$schema_default_tag,$schema_lang,$schema_varname){
	$sql="INSERT INTO Schemas (schema_name,schema_namespace,schema_description,schema_parent_id,schema_encoding,schema_root_tag,schema_default_tag,schema_lang,schema_varname) VALUES ('$schema_name', '$schema_name_space', '$schema_des',  '$schema_parent_id','$schema_encode', '$schema_root_tag', '$schema_default_tag','$schema_lang','$schema_varname')";/*echo $sql;*/
	$this->database->rawQuery($sql);	
	return $this->database->getInsertId();
	}
	function duplicateSchemadefinitions($schema_parent_id,$schema){

		$def=$this->getSchemaDefDetails($schema_parent_id);
		foreach($def as $key=>$details){
		$v_id=$details['vocabulary_id'];
		$vocabulary_name=$details['vocabulary_name'];
		$vocabulary_prefix=$details['vocabulary_prefix'];
		$vocabulary_namespace=$details['vocabulary_namespace'];
		$vocabulary_description=$details['vocabulary_description'];
		$vocabulary_type=$details['vocabulary_type'];
		$vocabulary_max=$details['vocabulary_max'];
		$vocabulary_min=$details['vocabulary_min'];
		$vocabulary_htmlform=$details['vocabulary_htmlform'];
		$vocabulary_nested=$details['vocabulary_nested'];
		$vocabulary_setting=$details['vocabulary_setting'];
		$vocabulary_iterates=$details['vocabulary_iterates'];
		$vocabulary_default_content=$details['vocabulary_default_content'];
			$sql_insert="INSERT INTO Vocabulary (vocabulary_name,vocabulary_prefix,vocabulary_namespace, vocabulary_description,vocabulary_type,vocabulary_max,vocabulary_min,vocabulary_htmlform, vocabulary_nested,vocabulary_setting,vocabulary_iterates,vocabulary_default_content)
			VALUES ('$vocabulary_name', '$vocabulary_prefix', '$vocabulary_namespace', '$vocabulary_description', '$vocabulary_type', '$vocabulary_max','$vocabulary_min', '$vocabulary_htmlform', '$vocabulary_nested', '$vocabulary_setting', '$vocabulary_iterates', '$vocabulary_default_content')";//echo $sql_insert;
			$this->database->rawQuery($sql_insert);
			$vid=$this->database->getInsertId();
			$vocabulary_array[$key][0]=$v_id;$vocabulary_array[$key][1]=$vid;
// 			$v_diff=$vid-$v_id;echo $v_diff;
		$level=$details['schemadefinition_level'];
		$setting=$details['schemadefinition_setting'];
		$root=$details['schemadefinition_root'];
		$def_req=$details['schemadefinition_required'];
		$p=$details['schemadefinition_parent_id'];
		if($p!=0){
			foreach($vocabulary_array as $id_val){
			if($p==$id_val[0]){$p=$id_val[1];}
			}
		}
			$sql_def="INSERT INTO SchemaDefinitions (schemadefinition_schema_id,schemadefinition_vocabulary_id,schemadefinition_parent_id,schemadefinition_level,schemadefinition_setting,schemadefinition_root,schemadefinition_required) VALUES ('$schema','$vid','$p','$level','$setting','$root','$def_req')";
			$this->database->rawQuery($sql_def);//echo $sql_def;
			
			}
		
	}
	function insertSchemadefinitions($schema_parent_id,$schema,$schema_name,$root){
		if($schema_parent_id==0){
		$sql="INSERT INTO Vocabulary (vocabulary_name,vocabulary_prefix,vocabulary_default_content,vocabulary_definition) VALUES ('root','root','root','TRUE')";
		$this->database->rawQuery($sql);
		$vid=$this->database->getInsertId();
		$sql2="INSERT INTO SchemaDefinitions (schemadefinition_schema_id,schemadefinition_vocabulary_id,schemadefinition_parent_id,schemadefinition_root) VALUES ('$schema', '$vid', '$schema_parent_id','$root')";
		$this->database->rawQuery($sql2);	
		}

	}
	
	function getSchemaDetails($id){
		$sql = "SELECT * FROM  SchemaDefinitions WHERE   schemadefinition_schema_id='$id'";
    		$schema= $this->database->queryToArray($sql); return $schema;
    						
	}
	function getSchemaName($id){
		$sql = "SELECT schema_name FROM  Schemas WHERE schema_id='$id'";
    		$schema= $this->database->queryToArray($sql); return $schema[0]['schema_name'];
    						
	}
	function getSchemaVarName($id){
		$sql = "SELECT schema_varname FROM  Schemas WHERE schema_id='$id'";
    		$schema= $this->database->queryToArray($sql); return $schema[0]['schema_varname'];
    						
	}
	function getschemaitems($id){
		$sql = "SELECT itemclass_id FROM  ItemClasses WHERE   itemclass_schema_id='$id'";
    		$items_id= $this->database->queryToArray($sql); return $items_id;
    						
	}


	function getUniqueSchemaName($name){	    		
		$name_temp=$name;
		$sql="SELECT * from Schemas where schema_name = '$name'";
		$count=$this->database->howMany($sql);
		$i=1;
		
		while($count>0){
		
			$i=$i+1;
			$name=$name_temp.' '.$i;
			$sql="SELECT * from Schemas where schema_name = '$name'";
			$v1=$this->database->howMany($sql);
			
			if($v1==0){
				break;
			}else{
				$count=$v1;
			}
		}
		return $name;
	}
	function updateSchemaName($id,$name){
		$sql = "UPDATE Schemas SET  schema_name='$name' WHERE schema_id='$id'";
		$this->database->rawQuery($sql);
	}
	function getSchemaNumModels($id){
		$sql = "SELECT * from ItemClasses WHERE itemclass_schema_id = '$id'";
		return $this->database->howMany($sql);
	}
	function getSchemaModelsList($id){
		$sql = "SELECT * from ItemClasses WHERE itemclass_schema_id = '$id'";
		return $this->database->queryToArray($sql);
	}
	function getVocabulary_prefix(){
	$sql = "SELECT DISTINCT(vocabulary_prefix) FROM Vocabulary";
		return $this->database->queryToArray($sql);
	}
	function getVocabularydetails($prefix){
	$sql = "SELECT * FROM Vocabulary WHERE vocabulary_prefix='$prefix'";
		return $this->database->queryToArray($sql);
	}
	function getVocabulary_id($id){
	$sql = "SELECT * FROM Vocabulary WHERE vocabulary_id='$id'";
		return $this->database->queryToArray($sql);
	}
	function getVocabulary_name($id){
	$sql = "SELECT vocabulary_name FROM Vocabulary WHERE vocabulary_id='$id'";
		$result= $this->database->queryToArray($sql);return $result[0]['vocabulary_name'];
	}
  	function getVocabulary_type($id){
    	$sql = "SELECT vocabulary_definition FROM Vocabulary WHERE vocabulary_id='$id' LIMIT 1";
   	 $vocabulary = $this->database->queryToArray($sql); 
   	 return $vocabulary[0];
  	}

	function insertAttribute($vocabulary_id,$name,$value){
		$sql="INSERT INTO Vocabulary (vocabulary_name,vocabulary_default_content,vocabulary_parent_id,vocabulary_definition) VALUES ('$name','$value', '$vocabulary_id', 'FALSE')";/*echo $sql;*/
		$this->database->rawQuery($sql);
		return $this->database->getInsertId();
	}
	function updateAttribute($attribute_id,$name,$value){
		$sql="UPDATE Vocabulary SET vocabulary_name='$name', vocabulary_default_content = '$value'  WHERE vocabulary_id ='$attribute_id' LIMIT 1";/*echo $sql;*/
		return $this->database->rawQuery($sql);
	}
	function getSchemaDefinitionid($id){
   		return $this->database->specificQuery("schemadefinition_id", "schemadefinition_vocabulary_id", $id, "SchemaDefinitions");		
	}
	function updateRepeatValue($vocabulary_id,$vocabulary_itrate){
		$sql="UPDATE Vocabulary SET vocabulary_iterates='$vocabulary_itrate'  WHERE vocabulary_id ='$vocabulary_id' LIMIT 1";/*echo $sql;*/
		return $this->database->rawQuery($sql);
	}
	function getSchemaDetail($var_name){
		$sql = "SELECT * FROM Schemas WHERE schema_varname='$var_name' ";//echo $sql;
		$resultt=$this->database->queryToArray($sql);	return $resultt[0];			
	}

}


?>
