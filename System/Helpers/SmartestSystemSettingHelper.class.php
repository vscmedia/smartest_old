<?php

class SmartestSystemSettingHelper extends SmartestHelper{

	static function load($token){
		
		$file_name = md5($token).'.setting';
		
		// echo $file_name;
		
		$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
	
		if(file_exists($file_path)){
			return unserialize(file_get_contents($file_path));
		}else{
			return null;
		}
	}
	
	static function save($token, $data){
		
		$file_name = md5($token).'.setting';
		
		// echo $file_name;
		
		$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
	    
	    if(file_put_contents($file_path, serialize($data))){
			return true;
		}else{
			return false;
		}
	}
	
	static function hasData($token){
		
		$file_name = md5($token).'.setting';
		
		$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
	    
	    // echo $token.' '.$file_path.'<br />';
	    
		if(file_exists($file_path)){
			return true;
		}else{
			return false;
		}
	}
	
	static function clear($token=""){
		
		// clear just one thing
		if(strlen($token)){
			
			$file_name = md5($token).'.setting';
			
			$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
			
			// delete the file
			if(file_exists($file_path)){
				$success = unlink($file_path);
				return $success;
			}else{
				return false;
			}
			
		}else{
		
			return false;
			
		}
	}
	
	static function getFileName($token=""){
	    if(strlen($token)){
			
			$file_name = md5($token).'.setting';
			
			$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
			
			return $file_path;
			
		}else{
		
			return false;
			
		}
	}
	
}