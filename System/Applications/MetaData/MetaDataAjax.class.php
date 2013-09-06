<?php

class MetaDataAjax extends SmartestSystemApplication{
    
    public function getForeignKeyFilterSelector(){
        
        $types = SmartestDataUtility::getDataTypes('field');
        $acceptable_type_codes = array_keys($types);
        
        if($this->getRequestParameter('type')){
            
            if(in_array($this->getRequestParameter('type'), $acceptable_type_codes)){
		        
		        $data_type_code = $this->getRequestParameter('type');
		        $data_type = $types[$data_type_code];
		        $this->send($data_type_code, 'selected_type');
		        
		        if($data_type['valuetype'] == 'foreignkey' && isset($data_type['filter']['typesource'])){
	                
	                if(is_file($data_type['filter']['typesource']['template'])){
	                    $this->send(SmartestDataUtility::getForeignKeyFilterOptions($data_type_code), 'foreign_key_filter_options');
	                    $this->send(SM_ROOT_DIR.$data_type['filter']['typesource']['template'], 'filter_select_template');
	                    $this->send(true, 'show_filter_select');
	                }else{
	                    // $this->send($data_type['filter']['typesource']['template'], 'intended_file');
	                    // $this->send(SM_ROOT_DIR.'System/Applications/Items/Presentation/FKFilterSelectors/filtertype.unknown.tpl', 'filter_select_template');
	                    $this->send(false, 'show_filter_select');
	                }
	                
	                // $this->send(true, 'foreign_key_filter_select');
	            }
		        
		        // $this->send(true, 'show_full_form');
		        $this->send($this->getSite(), 'site');
		    }
            
        }
        
    }
    
}