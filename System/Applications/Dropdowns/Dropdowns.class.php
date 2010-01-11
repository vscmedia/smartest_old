<?php

class Dropdowns extends SmartestSystemApplication{
  

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
	    
	    $label = $post['dropdown_label'];
	    
	    if(strlen($label)){
	    
    	    $name = SmartestStringHelper::toVarName($label);
	    
    	    $dropdown = new SmartestDropdown;
	    
    	    if(!$dropdown->hydrateBy('name', $name)){
    	        $dropdown->setName($name);
    	        $dropdown->setLabel($label);
    	        $dropdown->save();
    	        $this->addUserMessageToNextRequest('Your new dropdown menu was saved successfully.', SmartestUserMessage::SUCCESS);
    	        $this->redirect('/dropdowns/editDropDown?dropdown_id='.$dropdown->getId());
    	    }else{
    	        $this->addUserMessageToNextRequest('A dropdown menu with that name already exists.', SmartestUserMessage::INFO);
    	        $this->redirect('/dropdowns/addDropDown');
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('You must enter a valid label for the dropdown.', SmartestUserMessage::ERROR);
            $this->redirect('/dropdowns/addDropDown');
            
        }
	    
	}

	public function dropdownInfo($get){
	    
	    $dropdown_id = (int) $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $this->send($dropdown, 'dropdown');
	        $this->send($dropdown->getFieldsWhereUsed($this->getSite()->getId()), 'fields');
	        $this->send($dropdown->getItemPropertiesWhereUsed($this->getSite()->getId()), 'item_properties');
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	         
	}
	
	public function editValues($get){
	    
	    $dropdown_id = (int) $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $this->send($dropdown, 'dropdown');
	        $dropdown->getOptions();
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	         
	}

	public function updateDropDown($get, $post){ 
	    
	    $dropdown_id = (int) $post['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $dropdown->setLabel($post['dropdown_label']);
	        $dropdown->setLanguage($post['dropdown_language']);
	        $dropdown->save();
	    }
	    
	    $this->formForward();
	    
	}

	public function deleteDropDown($get){ 
	    
	    $dropdown_id = (int) $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $dropdown->delete();
	        $this->addUserMessageToNextRequest('The dropdown was successfully deleted.', SmartestUserMessage::SUCCESS);
	        $this->redirect('/smartest/dropdowns');
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	    
	}

	public function dropdownValues($get){ 
	    
	    $this->setFormReturnUri();
	    $dropdown_id = $get['dropdown_id'];
	    
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $options = $dropdown->getOptions();
	        
	        $this->send($dropdown, 'dropdown');
	        $this->send($options, 'options');
	        
	    }else{
	        $this->addUserMessage("The supplied dropdown ID was not recognized.");
	    }
	    
	}

	public function addDropDownValue($get){ 
	    
	    $dropdown_id = (int) $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $this->send($dropdown, 'dropdown');
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }  
	}	

	public function insertDropDownValue($get, $post){
	    
	    $label = $post['dropdownvalue_label'];
	    $value = SmartestStringHelper::toVarName($post['dropdownvalue_value']);
	    
	    $dropdown_id = (int) $post['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $option = new SmartestDropdownOption;
	    
	        $option->setDropdownId($dropdown->getId());
	        $option->setLabel($label);
	        $option->setValue($value);
	        $option->setOrder($dropdown->getNextOptionOrderIndex());
	        
	        $option->save();
	        
	        $this->addUserMessageToNextRequest('Your new dropdown menu was saved successfully.', SmartestUserMessage::SUCCESS);
	        $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The dropdown ID menu was not recognized.', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}

	public function editDropDownValue($get){ 
	    
	    $dropdown_value_id=$get['dropdown_value_id'];
	    
	    $option = new SmartestDropdownOption;
	    
	    if($option->find($dropdown_value_id)){
	    
	        $this->send($dropdown_id, 'dropdown_id');
	        $this->send($option, 'option');
	        $this->send($option->getDropdown(), 'dropdown');
	    
        }else{
            
            $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/dropdowns');
            
        }
	        
	}

	public function updateDropDownValue($get, $post){ 
	    
	    $id = (int) $post['dropdown_value_id'];
	    
	    $option = new SmartestDropdownOption;
	    
	    if($option->find($id)){
	        $drop_down_label = SmartestStringHelper::sanitize($post['dropdown_label']);
	        $option->setLabel($drop_down_label);
	        $option->save();
	        $this->addUserMessageToNextRequest("The option was updated.", SmartestUserMessage::SUCCESS);
	    }else{
	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();  
	}
	
	public function deleteDropDownValue($get){ 
	    
	    $id = (int) $get['dropdown_value_id'];
	    
	    $option = new SmartestDropdownOption;
	    
	    if($option->find($id)){
	        $option->delete();
	        $this->addUserMessageToNextRequest("The option was deleted.", SmartestUserMessage::SUCCESS);
	        $this->redirect('/dropdowns/editValues?dropdown_id='.$option->getDropdownId());
	    }else{
	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function moveDropDownValueUp($get, $post){
	    
	    $dropdown_id = $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $dropdown->fixOrderIndices();
	        
	        $option_id = $get['dropdown_value_id'];
    	    $option = new SmartestDropdownOption;

    	    if($option->find($option_id)){
                $option->moveUp();
                $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }else{
    	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
    	        $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }
    	    
	    }else{
	        $this->addUserMessageToNextRequest("The dropdown ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	    
	}
	
	public function moveDropDownValueDown($get, $post){
	    
	    $dropdown_id = $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $dropdown->fixOrderIndices();
	        
	        $option_id = $get['dropdown_value_id'];
    	    $option = new SmartestDropdownOption;

    	    if($option->find($option_id)){
                $option->moveDown();
                $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }else{
    	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
    	        $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }
    	    
	    }else{
	        $this->addUserMessageToNextRequest("The dropdown ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	    
	}
	
}