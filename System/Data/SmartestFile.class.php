<?php

/**
 * undocumented class
 *
 * @package Smartest
 * @author Marcus Gilroy-Ware
 **/
 
class SmartestFile implements ArrayAccess{
    
    protected $_original_file_path;
    protected $_current_file_path;
    
    public function __construct(){
        
    }
    
    public function loadFile($file_path){
        if(is_file($file_path)){
            $this->_original_file_path = $file_path;
            $this->_current_file_path = $file_path;
            return true;
        }else{
            // throw new SmartestException($file_path.' does not exist or is not a valid file.');
            return false;
        }
    }
    
    public function exists(){
        return file_exists($this->_current_file_path);
    }
    
    public function getPath(){
        return $this->_current_file_path;
    }
    
    public function getFileName(){
        return basename($this->getPath());
    }
    
    public function getOriginalPath(){
        return $this->_original_file_path;
    }
    
    public function rename($new_name, $force=false){
        // keeps the file in the same directory, just changes the name
    }
    
    public function moveTo($new_location, $force=false){
        // if $new_location is a directory, keep the file name as it is and just move it
        // otherwise, attempt to move it and change its name to whatever the new name was
    }
    
    public function send(){
        // send the file to the client by instantiating a new SmartestFileDownload object
    }
    
    public function getMimeType(){
        // use xml file to get mime type from dot suffix
    }
    
    public function offsetGet($offset){
        
        switch($offset){
	        
	        case "url":
	        return $this->getWebUrl();
	        break;
	        
	        case "file_path":
	        return $this->getFullPath();
	        break;
	        
	        case "public_file_path":
	        return $this->getPublicPath();
	        break;
	        
	    }
        
    }
    
    public function offsetExists($offset){
        
    }
    
    public function offsetSet($offset, $value){
        
    }
    
    public function offsetUnset($offset){
        
    }

} // END class 