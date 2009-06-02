<?php

class SmartestCmsItemList extends SmartestBaseCmsItemList{
    
    protected $_list_items = array();
    protected $_data_set;
    protected $_fetch_attempted = false;
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'list_';
		$this->_table_name = 'Lists';
		
	}
	
	public function load($list_name, $page, $draft=false){
	    
	    if(is_object($page)){
	        
	        $sql = "SELECT * FROM Lists WHERE list_name='".$list_name."' AND list_page_id='".$page->getId()."'";
	        $result = $this->database->queryToArray($sql);
	        // print_r($result);
	        
	        if(count($result)){
	            $this->hydrate($result[0]);
	            return true;
	        }else{
	            return false;
	        }
	        
	    
        }
	    
	}
	
	public function exists($list_name, $page_id){
	    
	    $sql = "SELECT * FROM Lists WHERE list_name='".SmartestStringHelper::toVarName($list_name)."' AND list_page_id='".$page_id."'";
        $result = $this->database->queryToArray($sql);
        
        // echo $sql."<br />";
        
        if(count($result)){
            $this->hydrate($result[0]);
            return true;
        }else{
            return false;
        }
        
        
	}
	
	public function getInfoForPageTree($level=1){
	    
	    $info = array();
	    $info['exists'] = 'true';
	    $info['defined'] = $this->hasChanged() ? 'DRAFT' : 'PUBLISHED';
	    $info['assetclass_name'] = $this->_properties['name'];
		$info['type'] = "list";
		$info['level'] = $level;
		return $info;
	    
	}
	
	public function hasChanged(){
	    return ($this->_properties['draft_set_id'] == $this->_properties['live_set_id'] && $this->_properties['draft_template_file'] == $this->_properties['live_template_file'] && $this->_properties['draft_header_template'] == $this->_properties['live_header_template'] && $this->_properties['draft_footer_template'] == $this->_properties['live_footer_template']) ? true : false;
	}
	
	public function hasHeaderTemplate($draft=false){
	    
	    if($draft){
	        $header_template_file_name = $this->getDraftHeaderTemplate();
	    }else{
	        $header_template_file_name = $this->getLiveHeaderTemplate();
	    }
	    
	    return ((strlen($header_template_file_name) > 4) && is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$header_template_file_name)) ? true : false;
	}
	
	public function hasRepeatingTemplate($draft=false){
	    
	    if($draft){
	        $repeating_template_file_name = $this->getDraftTemplateFile();
	    }else{
	        $repeating_template_file_name = $this->getLiveTemplateFile();
	    }
	    
	    return ((strlen($repeating_template_file_name) > 4) && (is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$repeating_template_file_name) || is_file(SM_ROOT_DIR.'Presentation/Layouts/'.$repeating_template_file_name))) ? true : false;
	}
	
	public function hasFooterTemplate($draft=false){
	    
	    if($draft){
	        $footer_template_file_name = $this->getDraftFooterTemplate();
	    }else{
	        $footer_template_file_name = $this->getLiveFooterTemplate();
	    }
	    
	    return ((strlen($footer_template_file_name) > 4) && is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$footer_template_file_name)) ? true : false;
	    
	}
	
	public function getRepeatingTemplate($draft=false){
	    
	    if($draft){
	        $repeating_template_file_name = $this->getDraftTemplateFile();
	    }else{
	        $repeating_template_file_name = $this->getLiveTemplateFile();
	    }
	    
	    if($this->getType() == 'SM_LIST_ARTICULATED'){
	        return SM_ROOT_DIR.'Presentation/ListItems/'.$repeating_template_file_name;
        }else{
            return SM_ROOT_DIR.'Presentation/Layouts/'.$repeating_template_file_name;
        }
        
	}
	
	public function getHeaderTemplate($draft=false){
	    
	    if($draft){
	        $header_template_file_name = $this->getDraftHeaderTemplate();
	    }else{
	        $header_template_file_name = $this->getLiveHeaderTemplate();
	    }
	    
	    return SM_ROOT_DIR.'Presentation/ListItems/'.$header_template_file_name;
	    
	}
	
	public function getFooterTemplate($draft=false){
	    
	    if($draft){
	        $footer_template_file_name = $this->getDraftFooterTemplate();
	    }else{
	        $footer_template_file_name = $this->getLiveFooterTemplate();
	    }
	    
	    return SM_ROOT_DIR.'Presentation/ListItems/'.$footer_template_file_name;
	    
	}
	
	public function getItems($draft=false){
	    
	    if(!$this->_fetch_attempted){
	    
	        $this->_data_set = new SmartestCmsItemSet;
	    
    	    if($draft){
    	        $this->_data_set->hydrate($this->getDraftSetId());
    	    }else{
    	        $this->_data_set->hydrate($this->getLiveSetId());
    	    }
	        
	        $mode = $draft ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT;
	        
	        $has_limit = (bool) $this->getMaximumLength();
	        $limit = $has_limit ? (int) $this->getMaximumLength() : null;
	        
	        // var_dump($limit);
	        
	        if($has_limit){
	            $this->_list_items = array_slice($this->_data_set->getMembers($mode, false, $limit), 0, $limit);
            }else{
                $this->_list_items = $this->_data_set->getMembers($mode, false, $limit);
            }
	        
	        // var_dump($this->_data_set->getLabel());
    	    
    	    $this->_fetch_attempted = true;
	    
	    }
	    
	    return $this->_list_items;
	    
	}
	
	public function getItemsAsArrays($draft){
	    
	    if(!$this->_fetch_attempted){
	    
	        /* $this->_data_set = new SmartestCmsItemSet;
	    
    	    if($draft){
    	        $this->_data_set->hydrate($this->getDraftSetId());
    	    }else{
    	        $this->_data_set->hydrate($this->getLiveSetId());
    	    }
	    
    	    $this->_list_items = $this->_data_set->getMembers();
    	    $this->_fetch_attempted = true; */
    	    
    	    // force list to generate list members
    	    $items = $this->getItems($draft);
	    
	    }
	    
	    return $this->_data_set->getMembersAsArrays($draft);
	}

}