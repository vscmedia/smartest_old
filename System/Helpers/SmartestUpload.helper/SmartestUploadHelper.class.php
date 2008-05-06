<?php

SmartestHelper::register('Upload');

class SmartestUploadHelper extends SmartestHelper{
    
    protected $_upload_name = null;
    protected $_directory = null;
    protected $_file_name = null;
    protected $_temp_file_name = null;
    protected $_original_file_name = null;
    
	function __construct($upload_name, $new_file_dir='', $new_file_name=''){
		
		if(isset($_FILES[$upload_name]) && $_FILES[$upload_name]){
		    
		    $this->_upload_name = $upload_name;
		    
		    if(!@strlen($new_file_name)){
    			$this->_file_name = $_FILES[$this->_upload_name]['name'];
    		}
    		
    		$this->_original_file_name = $_FILES[$this->_upload_name]['name'];
    		$this->_temp_file_name = $_FILES[$this->_upload_name]['tmp_name'];
    		
    		if(strlen($new_file_dir)){
    		    $this->setUploadDirectory($new_file_dir);
		    }
		
	    }else{
	        // ERROR - upload doesn't exist
	        // echo 'Error $_FILES['.$upload_name.'] doesn\'t exist.';
	    }
		
	}
	
	function save(){
	    if(@move_uploaded_file($_FILES[$this->_upload_name]['tmp_name'], $this->getUploadDirectory().$this->getFileName())){
	        return true;
        }else{
            return false;
        }
	}
	
	function hasDotSuffix(){
	    
	    if($this->getFileName()){
	        
	        $args = func_get_args();
	        
	        if(count($args)){
	            
	            if(is_array($args[0])){
	                $suffixes = $args[0];
	            }else{
	                $suffixes = $args;
	            }
	        
	            foreach($suffixes as $s){
	                if(SmartestStringHelper::getDotSuffix($this->getFileName()) == $s){
	                    return true;
	                }
	            }
	        
            }
	        
	        return false;
        }
	}
	
	function setFileName($file_name){
	    
	    // $max_tries = 1;
	    
	    if($this->getUploadDirectory()){
	    
	        /*while(file_exists($this->getUploadDirectory().$file_name) && $max_tries < 1000){
	            $file_name = $file_name.'_'.$max_tries;
	        }*/
	        
	        $file_name = basename(SmartestFileSystemHelper::getUniqueFileName($this->getUploadDirectory().$file_name));
	        $this->_file_name = $file_name;
	        
        }
	}
	
	function getFileName(){
	    return $this->_file_name;
	}
	
	function getOriginalFileName(){
	    return $this->_original_file_name;
	}
	
	function setUploadDirectory($directory){
	    
	    if(!SmartestStringHelper::startsWith($directory, '/')){
	        $directory = SM_ROOT_DIR.$directory;
	    }
	    
	    if(is_dir($directory) && is_writable($directory)){
	        
	        if(SmartestStringHelper::endsWith($directory, '/')){
    	        $this->_directory = $directory;
		    }else{
		        $this->_directory = $directory.'/';
		    }
	    }
	    
	    // recalculate file name to make sure it is still unique in the new directory.
	    $this->setFileName($this->_file_name);
	    
	}
	
	function getUploadDirectory(){
	    return $this->_directory;
	}
	
	function getName(){
	    return $this->_upload_name;
	}
	
}