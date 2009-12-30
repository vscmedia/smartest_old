<?php

class SmartestObjectModelHelper{

	static function buildClassFile($id, $name){
		
		$model = new SmartestModel;
		
		if($model->find($id)){
		    
		    $className = $model->getClassName();
		    
		    if($file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/object_template.txt')){
		
    			$file = str_replace('__THISCLASSNAME__', $className, $file);
    			$file = str_replace('__AUTOCLASSNAME__', 'auto'.$className, $file);
    			$file = str_replace('__TIME__', date("Y-m-d H:i:s"), $file);
    			
    			file_put_contents($model->getClassFilePath(), $file);
			
    			return true;
			
    		}else{
    			return false;
    		}
		
	    }
		
	}
	
	static function buildAutoClassFile($id, $name){
		
		/* if(count(explode(' ', $name)) > 1){
		    $className = SmartestStringHelper::toCamelCase($name);
	    }else{
	        $className = $name;
	    } */
	    
		$model = new SmartestModel;
		
		if($model->find($id)){
		    
		    $className = $model->getClassName();
		    $properties = $model->getProperties();
		    $constants = '';
		
    		foreach($properties as $property){
			
    			$constant_name  = SmartestStringHelper::toConstantName($property->getName());
    			$constant_value = $property->getId();
			
    			if(is_numeric($constant_name{0})){
    				$constant_name = '_'.$constant_name;
    			}
			
    			$new_constant = '    const '.$constant_name.' = '.$constant_value.";\n";
			
    			$constants .= $new_constant;
			
    		}
		
    		if($file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_template.txt')){
			
    			$functions = self::buildAutoClassFunctionCode($model);
    			$varnames_lookup = self::buildAutoClassVarnameLookupCode($model);
			
    			$file = str_replace('__THISCLASSNAME__', 'auto'.$className, $file);
    			$file = str_replace('__THECONSTANTS__', $constants, $file);
    			$file = str_replace('__THEFUNCTIONS__', $functions, $file);
    			$file = str_replace('__THEVARNAMELOOKUPS__', $varnames_lookup, $file);
    			$file = str_replace('__MODEL_ID__', $id, $file);
    			$file = str_replace('__TIME__', date("Y-m-d h:i:s"), $file);
		
    			// file_put_contents(SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$className.'.class.php', $file);
    			file_put_contents($model->getAutoClassFilePath(), $file);
    			return true;
			
    		}else{
    			return false;
    		}
		
	    }
		
	}
	
	static function buildAutoClassVarnameLookupCode(SmartestModel $m){
	    
	    $varnames_lookup = '    protected $_varnames_lookup = array('."\n";
		$i = 1;
		$properties = $m->getProperties();
		
		foreach($properties as $property){
			
			$new_constant = "        '".$property->getVarname()."' => ".$property->getId();
			
			if($i < count($properties)){
			    $new_constant .= ',';
			}
			
			$new_constant .= "\n";
			
			$varnames_lookup .= $new_constant;
			$i++;
			
		}
		
		$varnames_lookup .= '    );'."\n\n";
		
		return $varnames_lookup;
	    
	}
	
	static function buildAutoClassFunctionCode(SmartestModel $m){
	    
	    $file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_datafunctions.txt');
	    $code = '';
	    
	    foreach($m->getProperties() as $property){
			
			$constant_name  = SmartestStringHelper::toCamelCase($property->getName());
			$constant_value = $property->getId();
			$bool_typecast = $property->getDatatype() == '' ? '(bool) ' : '';
			
			$f = str_replace('__PROPNAME__', $constant_name, $file);
			$f = str_replace('__PROPID__', $constant_value, $f);
			$f = str_replace('__BOOLTYPECAST__', $bool_typecast, $f);
			
			$code .= $f;
			
		}
		
		return $code;
	    
	}

}