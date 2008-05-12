<?php

class SmartestObjectModelHelper{

	static function buildClassFile($id, $name){
		
		$className = $name;
		
		if($file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/object_template.txt')){
		
			$file = str_replace('__THISCLASSNAME__', $className, $file);
			$file = str_replace('__AUTOCLASSNAME__', 'auto'.$className, $file);
			$file = str_replace('__TIME__', date("Y-m-d H:i:s"), $file);
		
			file_put_contents(SM_ROOT_DIR.'Library/ObjectModel/'.$className.'.class.php', $file);
			
			return true;
			
		}else{
			return false;
		}
		
	}
	
	static function buildAutoClassFile($id, $name){
		
		if(count(explode(' ', $name)) > 1){
		    $className = SmartestStringHelper::toCamelCase($name);
	    }else{
	        $className = $name;
	    }
	    
		$constants = '';
		$model = new SmartestModel;
		$model->hydrate($id);
		
		foreach($model->getProperties() as $property){
			
			$constant_name  = SmartestStringHelper::toConstantName($property->getName());
			$constant_value = $property->getId();
			
			if(is_numeric($constant_name{0})){
				$constant_name = '_'.$constant_name;
			}
			
			$new_constant = 'const '.$constant_name.' = '.$constant_value.";\n";
			
			$constants .= $new_constant;
			
		}
		
		if($file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_template.txt')){
			
			$file = str_replace('__THISCLASSNAME__', 'auto'.$className, $file);
			$file = str_replace('__THECONSTANTS__', $constants, $file);
			$file = str_replace('__MODEL_ID__', $id, $file);
			$file = str_replace('__TIME__', date("Y-m-d h:i:s"), $file);
		
			// echo $file;
		
			file_put_contents(SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$className.'.class.php', $file);
			return true;
		}else{
			return false;
		}
		
	}

}