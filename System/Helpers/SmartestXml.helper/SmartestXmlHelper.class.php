<?php

/**
 * undocumented class
 *
 * @package Smartest
 * @subpackage XmlHelper
 * @author Marcus Gilroy-Ware
 **/

SmartestHelper::register('Xml');

class SmartestXmlHelper extends SmartestHelper{
	
	static function loadFile($filename){
		
		if(is_file($filename)){
			
			if(!class_exists("XML_Unserializer")){
				@include_once 'PEAR.php';
				@include_once 'XML/Unserializer.php'; 
				@include_once 'XML/Serializer.php';
			}
			
			if(class_exists("XML_Unserializer")){

	    		$option = array('complexType' => 'array', 'parseAttributes' => TRUE);
	    		$unserialized = new XML_Unserializer($option);
	    		$result = $unserialized->unserialize($filename, true);

	    		if (PEAR::isError($result)) {
					// ERROR: XML file could not be parsed: PEAR said "$result->getMessage()"
					// echo 'xml file unparsable';
					throw new SmartestException('Couldn\'t parse file: '.$filename.'. PEAR said: '.$result->getMessage());
					// return false;
	    		}else{
	    			// load contents from xml file
	    			$data = $unserialized->getUnserializedData();
	    			// print_r($data);
	    			return $data;
	    		}
	
	    	}else{
	    		// ERROR: XML file could not be parsed because the PEAR XML_Unserializer library could not be found.
				// echo 'required pear libraries missing';
	    	}
		}else{
			// ERROR: File does not exist
			// echo 'no such file';
		}
	}
	
	static function loadString($string){
		
		if(strlen($string)){
			
			if(!class_exists("XML_Unserializer")){
				@include_once 'PEAR.php';
				@include_once 'XML/Unserializer.php'; 
				@include_once 'XML/Serializer.php';
			}
			
			if(class_exists("XML_Unserializer")){

	    		$option = array('complexType' => 'array', 'parseAttributes' => TRUE);
	    		$unserialized = new XML_Unserializer($option);
	    		$result = $unserialized->unserialize($string);

	    		if (PEAR::isError($result)) {
					// ERROR: XML file could not be parsed: PEAR said "$result->getMessage()"
					// echo 'xml file unparsable';
					return false;
	    		}else{
	    			// load contents from xml file
	    			$data = $unserialized->getUnserializedData();
	    			return $data;
	    		}
	
	    	}else{
	    		// ERROR: XML file could not be parsed because the PEAR XML_Unserializer library could not be found.
				// echo 'required pear libraries missing';
	    	}
		}else{
			// ERROR: File does not exist
			// echo 'no such file';
		}
	}
	
} // END class