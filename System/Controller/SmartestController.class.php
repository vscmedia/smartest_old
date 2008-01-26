<?php

class SmartestController extends Quince{
	
	const APPLICATION = 'SM_APP';
	const PROCESS = 'SM_PROC';
	
	function __construct($file, $automatic=true, $cache=true){
		$this->cache_dir = SM_ROOT_DIR."System/Cache/Controller/";
		parent::Quince($file, $automatic, $cache);
	}
	
	// non-standard non-quince stuff goes here
	
	public function getUserActionObject(){
		return $this->user_object;
	}
	
	public function registerProcess($type){
	    
	    $process = $this->modules[$this->moduleIndex];
	    
	    if($this->className && !is_object($this->user_object)){
	        $this->user_object = new $this->className;
	    }
	    
	    // print_r($process);
	    
	    if(is_object($this->user_object)){
	        
	        $this->user_object->setProcessName($process['name']);
	        $this->user_object->setProcessType($type);
	        $this->user_object->setProcessDirectory($process['directory']);
	        
	        if(isset($process['label']) && strlen($process['label'])){
	            $this->user_object->setProcessLongName($process['label']);
	        }
	    }
	    
	    // print_r($this->user_object);
	    
	}
		
}