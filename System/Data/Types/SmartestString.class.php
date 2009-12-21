<?php

class SmartestString implements SmartestBasicType, ArrayAccess{
	
	protected $_string;
	
    public function __construct($string=''){
        if(strlen($string)){
            $this->_string = $string;
        }else{
            $this->_string = '';
        }
    }
    
    public function __toString(){
        return $this->_string;
    }
    
    public function setValue($v){
        $this->_string = $v;
    }
    
    public function getValue(){
        return $this->_string;
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
        return in_array(strtolower($offset), array('slug', 'varname', 'constantname', 'camelcase', 'is_md5', 'length'));
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
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
 
}