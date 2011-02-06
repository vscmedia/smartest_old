<?php

class SmartestFilterChain{
    
    protected $_name;
    protected $_draft_mode = false;
    protected $_data;
    
    public function __construct($chain_name){
        
        if(is_dir(SM_ROOT_DIR.'System/Response/Filters/'.$chain_name.'/')){
            $this->_name = $chain_name;
            $this->_data = new SmartestParameterHolder("Filter chain data");
        }else{
            throw new SmartestException("The filter chain $chain name does not exist: System/Response/Filters/".$chain_name.'/ could not be found.');
        }
        
    }
    
    public function getDraftMode(){
        return $this->_draft_mode;
    }
    
    public function setDraftMode($m){
        $this->_draft_mode = (bool) $m;
    }
    
    public function loadFilters(){
        
        $filters = array();
        $filter_names = array();
        
        if($res = opendir(SM_ROOT_DIR.'System/Response/Filters/'.$this->_name.'/')){
		    
		    $system_helper_cache_string = '';
		    
			while (false !== ($file = readdir($res))) {
    		    
    		    if(is_dir(SM_ROOT_DIR.'System/Response/Filters/'.$this->_name.'/'.$file) && preg_match('/([A-Za-z]\w+)\.filter$/', $file, $matches) && is_file(SM_ROOT_DIR.'System/Response/Filters/'.$this->_name.'/'.$file.'/filter.'.$matches[1].'.php')){
    				$filter = new SmartestFilter;
    				$filter->setName($matches[1]);
    				$filter->setFunctionFile(SM_ROOT_DIR.'System/Response/Filters/'.$this->_name.'/'.$file.'/filter.'.$matches[1].'.php');
    				$filter->setDirectory(SM_ROOT_DIR.'System/Response/Filters/'.$this->_name.'/'.$file.'/');
    				$filter->attachChain($this);
    				$filters[] = $filter;
    				$filter_names[] = $filter->getName();
    			}
    		
			}
			
			closedir($res);
			
			return $filters;
			
		}else{
		    
		    throw new SmartestException(SM_ROOT_DIR.'System/Helpers/ could not be read.');
		    
		}
        
    }
    
    public function setParameter($name, $value){
        return $this->_data->setParameter($name, $value);
    }
    
    public function getParameter($name){
        return $this->_data->getParameter($name);
    }
    
    public function execute($html){
        
        $filters = $this->loadFilters();
        
        $start_time = SmartestPersistentObject::get('timing_data')->getParameter('start_time');
        $overhead_time = SmartestPersistentObject::get('timing_data')->getParameter('overhead_time');
		$overhead_time_taken = $overhead_time - $start_time;
		
		$end_time = microtime(true);
		$full_time_taken = $end_time - $start_time;
		$smarty_time_taken = $full_time_taken - $overhead_time_taken;
		
		SmartestPersistentObject::get('timing_data')->setParameter('full_time_taken', number_format($full_time_taken*1000, 2, ".", ""));
		SmartestPersistentObject::get('timing_data')->setParameter('overhead_time_taken', number_format($overhead_time_taken*1000, 2, ".", ""));
		SmartestPersistentObject::get('timing_data')->setParameter('smarty_time_taken', number_format($smarty_time_taken*1000, 2, ".", ""));
		
		// define("SM_TOTAL_TIME", $full_time_taken);
		// define("SM_SMARTY_TIME", $full_time_taken - $overhead_time_taken);
		
		foreach($filters as $f){
		    
		    $html = $f->execute($html);
		    
		}
		
		return $html;
        
    }
    
}