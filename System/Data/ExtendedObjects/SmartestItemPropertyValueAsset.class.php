<?php

class SmartestItemPropertyValueAsset extends SmartestAsset{
    
    protected $_display_params;
    
    public function __toString(){
	    
	    $r = new SmartestAssetRenderer($this, $this->getStringId());
        return $r->render();
        
	}
    
}