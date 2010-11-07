<?php

class SmartestRenderableAsset extends SmartestAsset implements SmartestDualModedObject{
    
    protected $_render_data;
    protected $_draft_mode = false;
    
    protected function __objectConstruct(){
		
		$this->_render_data = new SmartestParameterHolder('Asset render data');
		
	}
	
	public function hydrate($id, $site_id=''){
	    
	    $result = parent::hydrate($id, $site_id);
	    
	    if($result){
	        $this->setAdditionalRenderData($this->getDefaultParams());
	        return is_object($result) ? $result : true;
	    }else{
	        return false;
	    }
	    
	}
	
	public function hydrateBy($field, $value, $site_id=''){
	    
	    SmartestLog::getInstance('system')->log("Deprecated function used: SmartestRenderableAsset->hydrateBy()");
	    return $this->findBy($field, $value, $site_id);
	    
	}
	
	public function findBy($field, $value, $site_id=''){
	    
	    $result = parent::findBy($field, $value, $site_id);
	    
	    if($result){
	        $this->setAdditionalRenderData($this->getDefaultParams());
	        return true;
	    }else{
	        return false;
	    }
	    
	}
	
	/* public function hydrate($id, $site_id=''){
		
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
	        
	        $this->setAdditionalRenderData($this->getDefaultParams());
				
			$this->_came_from_database = true;
			
			return true;
		
		}else if(is_object($id) && (!method_exists($id, '__toString') || !is_numeric($id->__toString()))){
		    
		    throw new SmartestException("Tried to hydrate a SmartestRenderableAsset object with another object (of type ".get_class($id).")");
		
		}else{
		    
		    return $this->find($id, $site_id);
	        
		}
		
	} */
	
	public function find($id, $site_id=''){
	    
	    $result = parent::find($id, $site_id);
	    
	    if($result){
	        $this->setAdditionalRenderData($this->getDefaultParams());
	    }
	    
	    return $result;
	    
	}
	
	public function __toString(){
	    // This function has to return a string:
	    $output = $this->render();
	    if(strlen($output)){
	        return $output;
	    }else{
	        return '';
	    }
	}
	
	public function extractId(){
	    $regex = "/".$this->_type_info['url_translation']['format']."/i";
	    preg_match($regex, $this->getUrl(), $matches);
	    // print_r($matches);
	    $position = isset($this->_type_info['url_translation']['id_position']) ? $this->_type_info['url_translation']['id_position'] : 1;
	    return $matches[$position];
	}
	
	public function render($draft_mode='unset'){
	    
	    if($draft_mode === 'unset'){
	        $draft_mode = $this->_draft_mode;
	    }
	    
	    if($this->_type_info['storage']['type'] == 'external_translated'){
	        $this->_render_data->setParameter('remote_id', $this->extractId());
	        // print_r($this->_render_data->getParameters());
	    }
	    
	    if($this->getId()){
	        
	        $sm = new SmartyManager('BasicRenderer');
            $r = $sm->initialize($this->getStringId());
            $r->assignAsset($this);
            $r->setDraftMode($draft_mode);
    	    $content = $r->renderAsset($this->_render_data);
    	    
    	    return $content;
	    
	    }else{
            if($draft_mode){
                return '<span class="smartest-preview-hint">[No file selected for this value]</span>';
            }
        }
	    
	}
	
	public function setDraftMode($m){
	    $this->_draft_mode = (bool) $m;
	}
	
	public function getDraftMode(){
	    return $this->_draft_mode;
	}
	
	public function setAdditionalRenderData($info, $not_empty_only=false){
	    
	    if($info instanceof SmartestParameterHolder){
	        $info = $info->getParameters();
	    }
	    
	    if(is_array($info)){
	        foreach($info as $key=>$value){
	            if(!$not_empty_only || ($not_empty_only && strlen($value))){
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
            
            case "link_contents":
            return 'image:'.$this->getUrl();
        
        }
        
        if(strlen($this->_render_data->getParameter($offset))){
	        return $this->_render_data->getParameter($offset);
	    }
        
        return parent::offsetGet($offset);
        
    }
    
}