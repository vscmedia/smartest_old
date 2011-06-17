<?php

class SmartestAssetCreationHelper{
    
    protected $_alh;
    protected $_asset;
    protected $_asset_type;
    
    public function __construct($asset_type){
        
        $this->_alh = new SmartestAssetsLibraryHelper;
        
        $types = $this->_alh->getTypes();
        
        if(isset($types[$asset_type])){
            $this->_asset_type = $types[$asset_type];
        }else{
            throw new SmartestAssetCreationException('Tried to create a file by upload of a non-existent file type: \''.$asset_type.'\'');
        }
        
    }
    
    public function finish(){
        
        $this->_asset->save();
        
        if($this->_asset_type['storage']['type'] == 'database'){
            $this->_asset->getTextFragment()->save();
        }
        
        return $this->_asset;
        
    }
    
    public function createNewAssetFromFileUpload(SmartestUploadHelper $upload, $asset_label){
        
        $this->_asset = new SmartestAsset;
        $this->_asset->setWebid(SmartestStringHelper::random(32));
        $this->_asset->setCreated(time());
        $this->_asset->setStringId(SmartestStringHelper::toVarName($asset_label));
        $this->_asset->setLabel($asset_label);
        $this->_asset->setUserId(SmartestSession::get('user')->getId());
        $this->_asset->setType($this->_asset_type['id']);
        
        $suffixes = $this->_alh->getAllSuffixesForType($this->_asset_type['id']);
        
        if(!$upload->hasDotSuffix($suffixes)){
    		$upload->setFileName(SmartestStringHelper::toVarName($upload->getFileName()).'.'.$suffixes[0]);
    	}
    	
    	// create filename based on existing filename
        $raw_filename = $upload->getFileName();
        $filename = SmartestStringHelper::toSensibleFileName($raw_filename);
        
        // give it hashed name for now and save it to disk
        $upload->setFileName(md5(microtime(true)).'.'.$suffixes[0]);
        $r = $upload->save();
        
        $new_temp_file = SM_ROOT_DIR.'System/Temporary/'.$upload->getFileName();
        
        if($this->_asset_type['storage']['type'] == 'database'){
        
            // if storage type is database, save the file to System/Temporary/ and get its contents
            $content = SmartestFileSystemHelper::load($new_temp_file, true);
            $this->_asset->getTextFragment()->setContent($content);
            $this->_asset->setUrl($filename);
            return true;
        
        }else{
            
            $intended_file_name = SM_ROOT_DIR.$this->_asset_type['storage']['location'].$filename;
	        $final_file_name = SmartestFileSystemHelper::getUniqueFileName($intended_file_name);
	        
	        if(is_file($new_temp_file)){
        
	            if(SmartestFileSystemHelper::move($new_temp_file, $final_file_name)){
		            $this->_asset->setUrl(basename($final_file_name));
		            return true;
		        }else{
		            // $this->addUserMessageToNextRequest(sprintf("Could not move %s to %s. Please check file permissions.", basename($new_temp_file), basename($final_file_name)), SmartestUserMessage::ERROR);
		            // $this->addUserMessageToNextRequest(sprintf("Could not move %s to %s. Please check file permissions.", $new_temp_file, $final_file_name));
		            $message = sprintf("Could not move %s to %s. Please check file permissions.", basename($new_temp_file), basename($final_file_name));
		            throw new SmartestAssetCreationException($message);
		            SmartestLog::getInstance('site')->log($message, SmartestLog::ERROR);
		            SmartestLog::getInstance('site')->log("File that failed to move to final location is still stored at: ".$new_temp_file, SmartestLog::NOTICE);
		        }
	        
	        }else{
                
                throw new SmartestAssetCreationException("Temporary upload ".$new_temp_file." was unexpectedly not created.");
                SmartestLog::getInstance('site')->log("Temporary upload ".$new_temp_file." was unexpectedly not created.", SmartestLog::ERROR);
                
            }
            
        }
        
        return false;
        
    }
    
    public function createNewAssetFromUrl(SmartestExternalUrl $url, $asset_label){
        
        // $sc = $url->getHttpStatusCode();
        
        /* if(substr($sc, 0, 1) > 2){
            throw new SmartestAssetCreationeException("The URL you entered returned an unexpected HTTP status code: ".$sc);
        }else{ */
            $this->_asset = new SmartestAsset;
            $this->_asset->setWebid(SmartestStringHelper::random(32));
            $this->_asset->setCreated(time());
            $this->_asset->setStringId(SmartestStringHelper::toVarName($asset_label));
            $this->_asset->setLabel($asset_label);
            $this->_asset->setUserId(SmartestSession::get('user')->getId());
            $this->_asset->setType($this->_asset_type['id']);
            $this->_asset->setUrl($url->getValue());
            return true;
        // }
        
    }
    
    public function createNewAssetFromUnImportedFile($file_name, $asset_label){
        
        $proposed_import_target = SM_ROOT_DIR.$this->_asset_type['storage']['location'].$file_name;
        
        if(is_file($proposed_import_target)){
            if($this->_alh->getAssetRecordExistsWithFilename($file_name, $this->_asset_type['id'])){
                throw new SmartestAssetCreationException("That file has already been imported into the repository.");
            }else{
                $this->_asset = new SmartestAsset;
                $this->_asset->setWebid(SmartestStringHelper::random(32));
                $this->_asset->setCreated(time());
                $this->_asset->setStringId(SmartestStringHelper::toVarName($asset_label));
                $this->_asset->setLabel($asset_label);
                $this->_asset->setUserId(SmartestSession::get('user')->getId());
                $this->_asset->setType($this->_asset_type['id']);
                $this->_asset->setUrl($file_name);
                return true;
            }
        }else{
            throw new SmartestAssetCreationException("You tried to import a non-existent file into the repository.");
        }
        
    }
    
    public function createNewAssetFromTextArea($textarea_contents, $asset_label){
        
        $this->_asset = new SmartestAsset;
        $this->_asset->setWebid(SmartestStringHelper::random(32));
        $this->_asset->setCreated(time());
        $this->_asset->setStringId(SmartestStringHelper::toVarName($asset_label));
        $this->_asset->setLabel($asset_label);
        $this->_asset->setUserId(SmartestSession::get('user')->getId());
        $this->_asset->setType($this->_asset_type['id']);
        
        $suffixes = $this->_alh->getAllSuffixesForType($this->_asset_type['id']);
        
        $textarea_contents = SmartestTextFragmentCleaner::convertDoubleLineBreaks($textarea_contents);
        
        $new_temp_file = SM_ROOT_DIR.'System/Temporary/'.md5(microtime(true)).'.'.$suffixes[0];
        SmartestFileSystemHelper::save($new_temp_file, SmartestStringHelper::sanitize($textarea_contents), true);
        
        if($this->_asset_type['storage']['type'] == 'database'){
            
            $filename = $this->_asset->getStringId().'.'.$suffixes[0];
            
            if(is_file($new_temp_file)){
                // add contents of file in System/Temporary/ to database as a text fragment
                $this->_asset->getTextFragment()->setContent(SmartestFileSystemHelper::load($new_temp_file, true));
            }else{
                $this->_asset->getTextFragment()->setContent(SmartestStringHelper::sanitize($textarea_contents));
            }
            
            $this->_asset->setUrl($filename);
            return true;
        
	    }else{
            
            $filename = $this->_asset->getStringId().'.'.$suffixes[0];
	        
	        $intended_file_name = SM_ROOT_DIR.$this->_asset_type['storage']['location'].$filename;
	        $final_file_name = SmartestFileSystemHelper::getUniqueFileName($intended_file_name);
	        
	        if(is_file($new_temp_file)){
            
	            if(SmartestFileSystemHelper::move($new_temp_file, $final_file_name)){
	                $this->_asset->setUrl(basename($final_file_name));
	                chmod($final_file_name, 0666);
		            return true;
		        }else{
		            $everything_ok = false;
		            $message = sprintf("Could not move %s to %s. Please check file permissions on directory ".$this->_asset_type['storage']['location'].".", basename($new_temp_file), basename($final_file_name));
		            throw new SmartestAssetCreationException($message);
		            SmartestLog::getInstance('site')->log($message, SmartestLog::ERROR);
		            SmartestLog::getInstance('site')->log("File that failed to move to final location is still stored at: ".$new_temp_file, SmartestLog::NOTICE);
		        }
	        
            }else{
                SmartestLog::getInstance('site')->log("Temporary upload ".$new_temp_file." was unexpectedly not created.", SmartestLog::ERROR);
            }
	    }
	    
	    return false;
	    
    }

}