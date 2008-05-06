<?php

// a class for checking if libraries are installed

SmartestHelper::register('Lib');

class SmartestLibHelper extends SmartestHelper{

	static function isInstalled($library){
		
		if(SmartestCache::hasData('libraries_data', true)){
			$libs = SmartestCache::load('libraries_data', true);
		}else{
			$rawlibs = SmartestXmlHelper::loadFile('System/CoreInfo/libraries.xml');
			$libraries = array();
			foreach($rawlibs['library'] as $lib){
				if(!array_key_exists($lib['name'], $libraries)){
					$libraries[$lib['name']] = $lib;
				}
			}
			SmartestCache::save('libraries_data', $libraries, -1, true);
		}
		
		$name_parts = explode("/", $library);
		
		switch(count($name_parts)){
			case '2':
				// $file = SmartestStringHelper::toCamelCase();
				break;
			case '1':
				// $file = SmartestStringHelper::toCamelCase();
				break;
		}
		
	}
	
	static function load($library){
		
	}
	
	static function getLoaded(){
		if(SmartestCache::hasData('libraries_data', true)){
			$libs = SmartestCache::load('libraries_data', true);
		}else{
			$libs = SmartestXmlHelper::loadFile('System/CoreInfo/libraries.xml');
		}
		
		return $libs;
	}
	
	static function isLoaded($library){
		
	}

}