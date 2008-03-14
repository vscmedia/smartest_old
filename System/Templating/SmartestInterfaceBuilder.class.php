<?php

class SmartestInterfaceBuilder extends SmartestEngine{
    
    public function __construct(){
	    
	    parent::__construct();
	    $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/InterfaceBuilder/";
	    
	}
    
}