<?php

class SmartestPagePreset extends SmartestBasePagePreset{

	protected $_preset_definitions = array();
	protected $_original_page;
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'plp_';
		$this->_table_name = 'PageLayoutPresets';
		
	}
	
	public function getDefinitions(){
		
		if(count($this->_preset_definitions)){
			
			return $this->_preset_definitions;
			
		}else{
		
			if($this->_came_from_database){
				
				$sql = "SELECT * FROM PageLayoutPresetDefinitions WHERE plpd_preset_id='".$this->getId()."' ORDER BY plpd_id";
				$result = $this->database->queryToArray($sql);
				
				foreach($result as $r){
				    $def = new SmartestPagePresetDefinition;
				    $def->hydrate($r);
				    $this->_preset_definitions[] = $def;
				}
				
				return $this->_preset_definitions;
				
			}else{
				return array();
			}
		
		}
	}
	
	public function getOriginalPage(){
	    
	    if(!is_object($this->_original_page)){
	        $page = new SmartestPage;
	        $page->hydrate($this->getOrigFromPageId());
	        $this->_original_page = $page;
	    }
	    
	    return $this->_original_page;
	}
	
	public function addContainerDefinition($container_id){
	    
	    $page = $this->getOriginalPage();
	    
	    $container = new SmartestContainer;
        
        if($container->hydrate($container_id)){
            
            $definition = new SmartestContainerDefinition;
            
            if($definition->load($container->getName(), $page, true)){
                
                $def = new SmartestPagePresetDefinition;
                $def->setElementType(SmartestPagePresetDefinition::CONTAINER);
                $def->setElementId($container_id);
                $def->setElementValue($definition->getDraftAssetId());
                $this->_preset_definitions[] = $def;
            }
            
        }
	    
	}
	
	public function addPlaceholderDefinition($placeholder_id){
	    
	    $page = $this->getOriginalPage();
	    
	    $placeholder = new SmartestPlaceholder;
        
        if($placeholder->hydrate($placeholder_id)){
            
            $definition = new SmartestPlaceholderDefinition;
            
            if($definition->load($placeholder->getName(), $page, true)){
                
                $def = new SmartestPagePresetDefinition;
                $def->setElementType(SmartestPagePresetDefinition::PLACEHOLDER);
                $def->setElementId($placeholder_id);
                $def->setElementValue($definition->getDraftAssetId());
                $this->_preset_definitions[] = $def;
            }
            
        }
	}
	
	public function addFieldDefinition($field_id){
	    
	    $page = $this->getOriginalPage();
	    
	    $field = new SmartestPageField;
	
		if($field->hydrate($field_id)){
	
		    $definition = new SmartestPageFieldDefinition;
    		$definition->loadForUpdate($field->getName(), $page);
		    
		    $def = new SmartestPagePresetDefinition;
            $def->setElementType(SmartestPagePresetDefinition::FIELD);
            $def->setElementId($field->getId());
            $def->setElementValue($definition->getDraftValue());
            $this->_preset_definitions[] = $def;
	
	    }
	    
	}
	
	public function applyToPage($page){
	    
	    if($page instanceof SmartestPage){
	        
	        foreach($this->getDefinitions() as $def){
	            $def->applyToPage($page);
	        }
	        
	    }
	}
	
	/* public function getDefinitions(){
	    
	    if(!count($this->_preset_definitions)){
	        
	        $sql = "SELECT * FROM PageLayoutPresetDefinitions WHERE plpd_preset_id='".$this->getId()."'";
	        $result = $this->database->queryToArray($sql);
	        
	        foreach($result as $array){
	            $def = new SmartestPagePresetDefinition;
	            $def->hydrate($array);
	            $this->_preset_definitions[] = $def;
	        }
	        
	    }
	    
	    return $this->_preset_definitions;
	    
	} */
	
	public function save(){
	    
	    parent::save();
	    
	    foreach($this->_preset_definitions as $d){
	        
	        if(!$d->getPresetId()){
	            $d->setPresetId($this->getId());
	        }
	        
	        $d->save();
	    }
	    
	}

}