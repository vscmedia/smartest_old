<?php
  
// include any needed files - optional of course.
// require_once "%NEEDED_FILE%.php";

// if managers_autoload is turned off in settings but you want to use a manager class, 
// you must include it here as such:
// require_once SM_SYSTEM_MANAGERS_DIR."%SAMPLE%Manager.class.php";

// Extend SmartestApplication
// not strictly required if you like to do everything by hand, but no good reason not to.
// Gives access to DataManager API
// Gives access to Controller values and templating object
class Dropdowns extends SmartestApplication{
  

	// ModuleBase already has a constructor, so if you want your class to have a constructor,
	// put it here called __moduleConstruct() and SmartestApplication will call it.
	protected function __moduleConstruct(){
	    
	}
	
	// no other requirements at all.
	// define your methods as normal and have fun...
	
	public function startPage($get){
	    $this->setFormReturnUri();
	    $dropdowns = $this->manager->getDropdowns();      
	    return array("dropdowns"=> $dropdowns,"count"=>count($dropdowns));	
	}

	public function addDropDown($get){ 
		
	}

	public function insertDropDown($get, $post){ 
	    /* $dropdown=$post['drop_down'];
	    $this->manager->insertDropDown($dropdown); */
	    
	    $label = $post['dropdown_label'];
	    $name = SmartestStringHelper::toVarName($label);
	    
	    $dropdown = new SmartestDropdown;
	    
	    if(!$dropdown->hydrateBy('name', $name)){
	        $dropdown->setName($name);
	        $dropdown->setLabel($label);
	        $dropdown->save();
	        $this->addUserMessageToNextRequest('Your new dropdown menu was saved successfully.');
	    }else{
	        $this->addUserMessageToNextRequest('A dropdown menu with that name already exists.');
	    }
	    
	    $this->formForward();
	    
	}

	public function editDropDown($get){ 
	    $dropdown_id=$get['drop_down'];
	    $dropdown_details=$this->manager->getDropdownDetails($dropdown_id);
	    return array("dropdown_details"=>$dropdown_details);      
	}

	public function updateDropDown($get,$post){ 
	    $dropdown=$post['drop_down'];$drop_down_id=$post['drop_down_id'];
	    $this->manager->updateDropDown($dropdown,$drop_down_id);
	}

	public function deleteDropDown($get){ 
	    $dropdown_id=$get['drop_down'];
	    $this->manager->deleteDropDown($dropdown_id);
	}

	public function viewDropDown($get){ 
	    
	    $this->setFormReturnUri();
	    $dropdown_id = $get['drop_down'];
	    
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->hydrate($dropdown_id)){
	        
	        // echo 'hydrated';
	        
	        $options = $dropdown->getOptionsAsArrays();
	        
	        // print_r($options);
	        
	        $this->send($dropdown->__toArray(), 'dropdown_details');
	        $this->send($options, 'dropdown_options');
	        
	    }else{
	        $this->addUserMessage("The supplied dropdown ID was not recognized.");
	    }
	    
	    // print_r($dropdown);
	    
	    /* $dropdown_details = $this->manager->getDropdownDetails($dropdown_id);
	    $dropdown_values = $this->manager->getDropdownValues($dropdown_id);
	    return array("dropdown_details"=>$dropdown_details,"dropdown_values"=>$dropdown_values,"count"=>count($dropdown_values));*/
	}

	public function addDropDownValue($get){ 
	    $dropdown_id=$get['drop_down'];
	    $dropdown_details=$this->manager->getDropdownDetails($dropdown_id);
	    return array("dropdown_details"=>$dropdown_details);      
	}	

	public function insertDropDownValue($get, $post){
	    
	    $label = $post['dropdownvalue_label'];
	    $value = addslashes(SmartestStringHelper::sanitizeFileContents($post['dropdownvalue_value']));
	    $dropdown_id = $post['dropdown_id'];
	    $order = (int) $post['dropdownvalue_order'];
	    
	    $option = new SmartestDropdownOption;
	    
	    // if(!$option->hydrateBy('name', $name)){
	        
	        $option->setDropdownId($dropdown_id);
	        $option->setLabel($label);
	        $option->setValue($value);
	        $option->setOrder($order);
	        $option->save();
	        
	        $this->addUserMessageToNextRequest('Your new dropdown menu was saved successfully.');
	        
	    // }else{
	    //    $this->addUserMessageToNextRequest('A dropdown menu with that name already exists.');
	    // }
	    
	    $this->formForward();
	    
	    /* $dropdown_id=$post['drop_down_id'];
	    $drop_down_value=$post['drop_down_value'];
	    $drop_down_order=$post['drop_down_order'];
	    $this->manager->insertDropDownValue($dropdown_id,$drop_down_value,$drop_down_order); */
	    
	}

	public function editDropDownValue($get){ 
	    $dropdown_id=$get['drop_down'];
	    $drop_down_value_id=$get['drop_down_value_id'];
	    $dropdown_details=$this->manager->getDropdownDetails($dropdown_id);
	    $value_details=$this->manager->getDropdownValueDetails($drop_down_value_id);
	    return array("dropdown_details"=>$dropdown_details,"value_details"=>$value_details);      
	}

	public function updateDropDownValue($get, $post){ 
	    $dropdown_id=$post['drop_down_id'];
	    $drop_down_value_id=$post['drop_down_value_id'];
	    $drop_down_value=$post['drop_down_value'];
	    $drop_down_order=$post['drop_down_order'];
	    $this->manager->updateDropDownValue($dropdown_id,$drop_down_value_id,$drop_down_value,$drop_down_order);     
	}
	
	public function deleteDropDownValue($get){ 
	    $dropdown_id=$get['drop_down'];
	    $drop_down_value_id=$get['drop_down_value_id'];
	    $this->manager->deleteDropDownValue($dropdown_id,$drop_down_value_id);
	}
	
	public function reorderDropDownValue($get){ 
	    $dropdown_id=$get['drop_down'];
	    $dropdown_details=$this->manager->getDropdownDetails($dropdown_id);
	    $dropdown_values=$this->manager->getDropdownValues($dropdown_id);
	    return array("dropdown_details"=>$dropdown_details,"dropdown_values"=>$dropdown_values);
	}
	
	public function updateDropDownOrder($get,$post){ 
	    $dropdown_id=$post['drop_down_id'];
	    $order=$post['cmbRole'];
	    $this->manager->updateDropDownOrder($dropdown_id,$order);     
	}
	
	public function setBar(){
		
	}
}