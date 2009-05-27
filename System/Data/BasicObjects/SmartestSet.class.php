<?php

class SmartestSet extends SmartestBaseSet{
    
    protected $_membership_type;
    
    public function __objectConstruct(){
        
        throw new SmartestException("SmartestSet should not be instantiated directly. Please instantiate a SmartestCmsItemSet, SmartestPageGroup, SmartestAssetGroup, or SmartestUserGroup", SM_ERROR_USER);
        
    }

}
