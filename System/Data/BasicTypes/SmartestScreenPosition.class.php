<?php

class SmartestScreenPosition implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_x = 0;
    protected $_y = 0;
    protected $_v_orientation = 'T';
    protected $_h_orientation = 'L';
    
    public function __toString(){
        return $this->getCssVerticalProperty().':'.$this->_y.'px;'.$this->getCssHorizontalProperty().':'.$this->_x.'px;';
    }
    
    public function setValue($v){
        
        $p = explode(';', $v);
        $this->_x = (int) $p[0];
        $this->_y = (int) $p[1];
        $this->_h_orientation = $p[2];
        $this->_v_orientation = $p[3];
        
    }
    
    public function getValue(){
        return $this->_x.','.$this->_y;
    }
    
    // The next two methods are for the SmartestStorableValue interface
    
    public function getStorableFormat(){
        $f = $this->_x.';'.$this->_y.';'.$this->_h_orientation.';'.$this->_v_orientation;
        // echo $f;
        return $f;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        
        if(is_array($v)){
            
            $this->_x = (int) $v['x'];
            $this->_y = (int) $v['y'];
            
            switch($v['orient']){
                
                case "BR":
                $this->_v_orientation = 'B';
                $this->_h_orientation = 'R';
                break;
                
                case "BL":
                $this->_v_orientation = 'B';
                $this->_h_orientation = 'L';
                break;
                
                case "TR":
                $this->_v_orientation = 'T';
                $this->_h_orientation = 'R';
                break;
                
                case "TL":
                default:
                $this->_v_orientation = 'T';
                $this->_h_orientation = 'L';
                break;
            }
            
            return true;
            
        }else{
            
            return false;
            
        }
        
    }
    
    // ...and ArrayAccess
    
    public function offsetExists($offset){
        return false;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "css":
            return $this->__toString();
            case "css_h":
            case "css_x":
            return $this->getCssHorizontalProperty().':'.$this->_x.'px;';
            case "css_v":
            case "css_y":
            return $this->getCssVerticalProperty().':'.$this->_y.'px;';
            case "x":
            return new SmartestNumeric($this->_x);
            case "y":
            return new SmartestNumeric($this->_y);
            case "orient":
            return $this->_v_orientation.$this->_h_orientation;
        }
        
        return null;
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    
    // Some CSS Helping methods
    
    public function getCssHorizontalProperty(){
        return ($this->_h_orientation == 'R') ? 'right' : 'left';
    }
    
    public function getCssVerticalProperty(){
        return ($this->_v_orientation == 'B') ? 'bottom' : 'top';
    }
    
}