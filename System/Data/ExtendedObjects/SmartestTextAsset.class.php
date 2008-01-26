<?php

class SmartestTextAsset extends SmartestAsset{
    
    /* public function renderAsMarkup(){
	    
        switch($this->getType()){
	        
            case "SM_ASSETTYPE_RICH_TEXT":
            $markup = stripslashes($this->getTextFragment()->getContent());
            break;
            case "SM_ASSETTYPE_PLAIN_TEXT":
            case "SM_ASSETTYPE_SL_TEXT":
            $markup = htmlentities(stripslashes($this->getTextFragment()->getContent()), ENT_COMPAT, 'UTF-8');
            break;
	        
        }
	    
        return $markup;
    } */
    
    public function save(){
        
        $this->getTextFragment()->save();
        $this->setFragmentId($this->getTextFragment()->getId());
        parent::save();
        
    }
    
}