<?php

class SmartestFilterChain{
    
    protected $_name;
    protected $_draft_mode = false;
    
    public function __construct($chain_name){
        
        if(is_dir(SM_ROOT_DIR.'System/Response/Filters/'.$chain_name.'/')){
            $this->_name = $chain_name;
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
    
    public function execute($html){
        
        /* preg_match('/<body[^>]*?'.'>/i', $html, $match);
		
		if(!empty($match[0])){
			$body_tag = $match[0];
		}else{
			$body_tag = '';
		}
		
		if(SM_CONTROLLER_METHOD == "renderPageFromUrl" || SM_CONTROLLER_METHOD == "renderPageFromId"){
			$creator = "\n<!--Powered by Smartest-->\n";
		}else{
			$creator = "";
		} */
		
        $filters = $this->loadFilters();
		
		$end_time = microtime(true);
		$full_time_taken = number_format(($end_time - SM_START_TIME)*1000, 2, ".", "");
		
		define("SM_TOTAL_TIME", $full_time_taken);
		define("SM_SMARTY_TIME", $full_time_taken - SM_OVERHEAD_TIME);
		
		foreach($filters as $f){
		    
		    $html = $f->execute($html);
		    
		}
		
		return $html;
        
    }
    
}