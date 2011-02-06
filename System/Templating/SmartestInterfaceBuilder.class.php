<?php

class SmartestInterfaceBuilder extends SmartestEngine{
    
    public function __construct($pid){
	    
	    parent::__construct($pid);
	    $this->_context = SM_CONTEXT_SYSTEM_UI;
	    $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/InterfaceBuilder/";
	    
	}
	
	public function getDraftMode(){
	    return false;
	}
	
	public function renderBasicInput(SmartestParameterHolder $p){ // $datatype, $name, $existing_value='', $values=''
	    $info = SmartestDataUtility::getDataType($p->getParameter('type'));
	    // echo $datatype;
	    // echo $info['input']['template'];
	    // $_render_data = new SmartestParameterHolder('Input Render Data: '.$name);
	    // $_render_data->setParameter('options', $values);
	    // $_render_data->setParameter('name', $name);
	    $this->run(SM_ROOT_DIR.$info['input']['template'], $p);
	}
    
}