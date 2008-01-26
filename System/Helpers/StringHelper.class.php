<?php

SmartestHelper::register('StringHelper');

class SmartestStringHelper extends SmartestHelper{

	static function random($size){
	
		$possValues = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s"
			    , "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", 
			    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		$stem = "";
	
		for($i=0; $i<$size; $i++){
			$randNum = rand(0, count($possValues)-1);
			$shoot = $possValues[$randNum];
			$plant = $stem.$shoot;
			$stem = $plant;
		}	
	
		return $plant;
	
	}

	static function toSlug($normal_string){
	
		$page_name = strtolower($normal_string);
		$page_name = trim($page_name, " ?!%$#&£*|/\\");
		$page_name = preg_replace("/[\"'\.,]+/", "", $page_name);
		$page_name = preg_replace("/[^\w-_]+/", "-", $page_name);
		return $page_name;
	
	}
	
	static function toVarName($normal_string){
	
		$page_name = strtolower($normal_string);
		$page_name = trim($page_name, " ?!%$#&£*|/\\");
		$page_name = preg_replace("/[\"'\.,]+/", "", $page_name);
		$page_name = preg_replace("/[^\w_-]+/", "_", $page_name);
		return $page_name;
	
	}
	
	static function toConstantName($string){
		
		$constant_name = trim($string, " ?!%$#&£*|/\\");
		$constant_name = preg_replace("/[\"'\.,]+/", "", $constant_name);
		$constant_name = preg_replace("/[^\w-_]+/", "_", $constant_name);
		$constant_name = strtoupper($constant_name);
    	
    		return $constant_name;
	}
	
	static function isMd5Hash($string){
		if(preg_match("/^[0-9a-f]{32}$/", $string)){ // if
			return true;
		}else{
			return false;
		}
	}

}