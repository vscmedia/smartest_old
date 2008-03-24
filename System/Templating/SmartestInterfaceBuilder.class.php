<?php

class SmartestInterfaceBuilder extends SmartestEngine{
    
    public function __construct($pid){
	    
	    parent::__construct($pid);
	    $this->_context = SM_CONTEXT_SYSTEM_UI;
	    $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/InterfaceBuilder/";
	    
	}
    
}