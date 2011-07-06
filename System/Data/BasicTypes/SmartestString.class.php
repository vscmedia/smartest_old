<?php

class SmartestString implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
	
	protected $_string;
	
    public function __construct($string=''){
        $this->setValue($string);
    }
    
    public function __toString(){
        /* if(defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && constant('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && defined('SM_CMS_PAGE_ID')){
            return (string) SmartestStringHelper::toXmlEntities($this->_string);
        }else{ */
            return (string) $this->_string;
        // }
    }
    
    public function stdObjectOrScalar(){
        return $this->_string;
    }
    
    public function setValue($v){
        if(strlen($v)){
            $this->_string = (string) $v;
        }else{
            $this->_string = '';
        }
    }
    
    public function getValue(){
        return $this->_string;
    }
    
    public function getWordCount(){
        return SmartestStringHelper::getWordCount($this->_string);
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_string;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function toSlug(){
    	return SmartestStringHelper::toSlug($this->_string);
    }
    
    public function toVarName(){
    	return SmartestStringHelper::toVarName($this->_string);
    }
    
    public function toConstantName(){
    	return SmartestStringHelper::toConstantName($this->_string);
    }
    
    public function toCamelCase(){
    	return SmartestStringHelper::toCamelCase($this->_string);
    }
    
    public function toHexUrlEncoded(){
        return SmartestStringHelper::toHexUrlEncoded($this->_string);
    }
    
    public function toHtmlEncoded(){
        return SmartestStringHelper::toHtmlEncoded($this->_string);
    }
    
    public function isMd5Hash(){
    	return SmartestStringHelper::isMd5Hash($this->_string);
    }
    
    public function toParagraphsArray(){
        return SmartestStringHelper::toParagraphsArray($this->_string);
    }
    
    public function offsetExists($offset){
        return in_array(strtolower($offset), array('slug', 'varname', 'constantname', 'camelcase', 'is_md5', 'length', 'paragraphs', 'encoded', 'urlencoded', 'wordcount', 'xmlentities'));
    }
    
    public function offsetGet($offset){
        switch(strtolower($offset)){
            case "slug":
            return $this->toSlug();
            case 'varname':
            return $this->toVarName();
            case "constantname":
            return $this->toConstantName();
            case 'camelcase':
            return $this->toCamelCase();
            case "is_md5":
            return $this->isMd5Hash();
            case "length":
            return strlen($this->_string);
            case "paragraphs":
            return $this->toParagraphsArray();
            case "encoded":
            return $this->toHexEncoded();
            case "urlencoded":
            return urlencode($this->_string);
            case "wordcount":
            return $this->getWordCount();
            case "xmlentities":
            return (string) SmartestStringHelper::toXmlEntities($this->_string);
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
 
}