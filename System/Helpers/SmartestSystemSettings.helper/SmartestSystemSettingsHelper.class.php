<?php

class SmartestSystemSettingHelper extends SmartestHelper{

	public static function load($token){
		
		$file_name = md5($token).'.setting';
		
		$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
	
		if(file_exists($file_path)){
			return unserialize(file_get_contents($file_path));
		}else{
			return null;
		}
	}
	
	public static function save($token, $data){
		
		$file_name = md5($token).'.setting';
		
		$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
	    
	    if(file_put_contents($file_path, serialize($data))){
			return true;
		}else{
			return false;
		}
	}
	
	public static function hasData($token){
		
		$file_name = md5($token).'.setting';
		
		$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
	    
	    if(file_exists($file_path)){
			return true;
		}else{
			return false;
		}
	}
	
	public static function clear($token=""){
		
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
	
	public static function getFileName($token=""){
	    if(strlen($token)){
			
			$file_name = md5($token).'.setting';
			
			$file_path = SM_ROOT_DIR.'System/Cache/Settings/'.$file_name;
			
			return $file_path;
			
		}else{
		
			return false;
			
		}
	}
	
	public static function getInstallId(){
	    
	    $ph = SmartestPersistentObject::get('prefs_helper');
	    $value = $ph->getGlobalPreference('install_id', '0', '0');
	    
	    if(!strlen($value)){
            $value = implode(':', str_split(substr(md5(SM_ROOT_DIR.$_SERVER["SERVER_ADDR"]), 0, 12), 2));
            $ph->setGlobalPreference('install_id', $value, '0', '0');
        }
        
        return $value;
	    
	}
	
	public static function getSiteLogosFileGroupId(){
	    
	    $ph = SmartestPersistentObject::get('prefs_helper');
	    $id = $ph->getGlobalPreference('default_site_logo_asset_group_id', '0', '0');
	    
	    if(!strlen($id)){
            $group = new SmartestAssetGroup;
            $group->setLabel("Site logos");
            $group->setName("site_logos");
            $group->setIsSystem(1);
            $group->setIsHidden(1);
            $group->setShared(1);
            $group->setFilterType("SM_SET_FILTERTYPE_ASSETCLASS");
            $group->setFilterValue("SM_ASSETCLASS_STATIC_IMAGE");
            $group->save();
            $id = $group->getId();
            $ph->setGlobalPreference('default_site_logo_asset_group_id', $id, '0', '0');
        }
        
        return $id;
	    
	}
	
}