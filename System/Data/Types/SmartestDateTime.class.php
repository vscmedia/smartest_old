<?php

class SmartestDateTime implements SmartestBasicType, ArrayAccess{
    
    protected $_value;
    protected $_use_time = true;
    
    public function setValue($v){
        
        if(is_array($v)){
            
            if(isset($v['h'])){
                $hour = $v['h'];
            }else{
                $hour = 0;
            }
            
            if(isset($v['m'])){
                $minute = $v['m'];
            }else{
                $minute = 0;
            }
            
            if(isset($v['s'])){
                $second = $v['s'];
            }else{
                $second = 0;
            }
            
            if(isset($v['Y'])){
                $year = $v['Y'];
            }else{
                throw new SmartestException("Arrays passed to SmartestDateTime::setValue() must have Y, M, and D keys");
            }
            
            if(isset($v['M'])){
                $month = $v['M'];
            }else{
                throw new SmartestException("Arrays passed to SmartestDateTime::setValue() must have Y, M, and D keys");
            }
            
            if(isset($v['D'])){
                $day = $v['D'];
            }else{
                throw new SmartestException("Arrays passed to SmartestDateTime::setValue() must have Y, M, and D keys");
            }
            
            $this->_value = mktime($hour, $minute, $second, $month, $day, $year);
        }else if(is_numeric($v)){
            $this->_value = $v;
        }else{
            $this->_value = strtotime($v);
        }
    }
    
    public function getValue($format=''){
        if(strlen($format)){
            return date($format, $this->_value);
        }else{
            return $this->_value;
        }
    }
    
    public function __toString(){
        return date(SM_OPTIONS_DATE_FORMAT, $this->_value);
    }
    
    public function offsetExists($offset){
	    
	    return in_array($offset, array('m', 's', 'h', 'Y', 'M', 'D', 'unix'));
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case 'm':
	        return date('i', $this->_value);
	        
	        case 'h':
	        return date('h', $this->_value);
	        
	        case 's':
	        return date('s', $this->_value);
	        
	        case 'Y':
	        return date('Y', $this->_value);
	        
	        case 'M':
	        return date('m', $this->_value);
	        
	        case 'D':
	        return date('d', $this->_value);
	        
	        case 'unix':
	        return $this->_value;
	        
	    }
	    
	}
	
	public function offsetSet($offset, $value){
	    // read only
	}
	
	public function offsetUnset($offset){
	    // read only
	}
    
}