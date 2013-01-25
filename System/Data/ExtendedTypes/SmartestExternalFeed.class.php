<?php

class SmartestExternalFeed extends SmartestExternalUrl implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    public function offsetGet($offset){
        switch($offset){
/*             case "_host":
            return $this->getValue();
            case '_request':
            return $this->getValue();
            case '_protocol':
            return $this->getValue();
            case "encoded":
            case "urlencoded":
            return urlencode($this->getValue());
            case 'empty':
            return !strlen($this->getValue());
            case 'string':
            return $this->__toString(); */
        }
        
        return parent::offsetGet($offset);
    }
    
}