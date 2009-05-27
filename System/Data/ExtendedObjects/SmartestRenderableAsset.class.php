<?php

class SmartestRenderableAsset extends SmartestAsset{
    
    protected $_render_data;
    protected $_draft_mode = false;
    
    protected function __objectConstruct(){
		
		$this->_render_data = new SmartestParameterHolder('Asset render data');
		
	}
	
	public function hydrate($id, $site_id=''){
		
		if(is_array($id)){
		        
	        $offset = strlen($this->_table_prefix);
	        
	        foreach($this->_original_fields as $fn){
	            // if the new array has a value with a key that exists in this object's table 
	            if(isset($id[$fn])){
	                // if the field is exempted from prefix (rare)
	                if(isset($this->_no_prefix[$fn])){
	                    $this->_properties[$fn] = $id[$fn];
	                }else{
	                    $this->_properties[substr($fn, $offset)] = $id[$fn];
	                }
	            }
	        }
	        
	        // print_r($this->getDefaultParams());
	        $this->setAdditionalRenderData($this->getDefaultParams());
				
			$this->_came_from_database = true;
			
			return true;
		
		}else if(is_object($id) && (!method_exists($id, '__toString') || !is_numeric($id->__toString()))){
		    
		    throw new SmartestException("Tried to hydrate a SmartestRenderableAsset object with another object (of type ".get_class($id).")");
		
		}else{
		    
		    return $this->find($id, $site_id);
	        
		}
		
	}
	
	public function find($id, $site_id=''){
	    
	    $result = parent::find($id, $site_id);
	    
	    if($result){
	        // print_r($this->getDefaultParams());
	        $this->setAdditionalRenderData($this->getDefaultParams());
	    }
	    
	    return $result;
	    
	}
	
	public function __toString(){
	    return $this->render();
	}
	
	public function render($draft_mode='unset'){
	    
	    if($this->getId()){
	        
	        if($draft_mode == 'unset'){
    	        $draft_mode = $this->_draft_mode;
    	    }
	        
	        $sm = new SmartyManager('BasicRenderer');
            $r = $sm->initialize($this->getStringId());
            $r->assignAsset($this);
            $r->setDraftMode($draft_mode);
    	    $content = $r->renderAsset($this->_render_data);
    	    
    	    return $content;
	    
	    }else{
            if($this->_draft_mode){
                return '<em>[No file selected for this value]</em>';
            }
        }
	    
	}
	
	public function setDraftMode($m){
	    $this->_draft_mode = (bool) $m;
	}
	
	public function getDraftMode($m){
	    return $this->_draft_mode;
	}
	
	public function setAdditionalRenderData($info, $not_empty_only=false){
	    
	    if($info instanceof SmartestParameterHolder){
	        $info = $info->getParameters();
	    }
	    
	    if(is_array($info)){
	        foreach($info as $key=>$value){
	            if(!$not_empty_only || ($not_empty_only && strlen($value))){
	                // echo $key.'='.$value."\n";
	                // var_dump($not_empty_only);
	                // var_dump(strlen($value));
	                $this->_render_data->setParameter($key, $value);
                }
	        }
	    }
	    
	}
	
	public function getRenderData(){
	    return $this->_render_data;
	}
	
	public function offsetGet($offset){
        
        switch($offset){
            
            case "html":
            return $this->render();
            
            case "render_data":
            return $this->_render_data;
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}