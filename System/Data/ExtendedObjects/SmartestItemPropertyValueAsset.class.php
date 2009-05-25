<?php

class SmartestItemPropertyValueAsset extends SmartestAsset{
    
    protected $_display_params;
    protected $_draft_mode;
    
    public function __toString(){
	    
	    return $this->render(array(), $this->_display_params, $this->_draft_mode);
        
	}
    
}