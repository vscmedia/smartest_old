<?php

class SmartestRgbColor implements ArrayAccess, SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_red;
    protected $_green;
    protected $_blue;
    protected $_depth = 16;
    
    const RGB_256_MATCH = '/^(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})$/';
    const RGB_PERCENTAGES_MATCH = '/^(\d{1,3})%,\s*(\d{1,3})%,\s*(\d{1,3})%$/';
    const RGB_HEX_MATCH = '/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i';
    
    public function __construct($v=null){
        $this->setValue($v);
    }
    
    public function setValue($v){
        
        if(!strlen($v)){
            $v = '#00f';
        }
        
        if(preg_match(self::RGB_HEX_MATCH, $v, $matches)){
            
            $code = $matches[1];
            $this->_depth = (strlen($code) == 6) ? 24 : 12;
            
            $red_h = ($this->_depth == 12) ? substr($code, 0, 1).substr($code, 0, 1) : substr($code, 0, 2);
            $green_h = ($this->_depth == 12) ? substr($code, 1, 1).substr($code, 1, 1) : substr($code, 2, 2);
            $blue_h = ($this->_depth == 12) ? substr($code, 2, 1).substr($code, 2, 1) : substr($code, 4, 2);
            
            $this->_red = new Smartest8BitInteger(hexdec($red_h));
            $this->_green = new Smartest8BitInteger(hexdec($green_h));
            $this->_blue = new Smartest8BitInteger(hexdec($blue_h));
            
            return true;
            
        }else if(preg_match(self::RGB_256_MATCH, $v, $matches)){
            
            $red_p = (int) $matches[1];
            $green_p = (int) $matches[2];
            $blue_p = (int) $matches[3];
            
            $this->_red = ($red_p > 255) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($red_p)));
            $this->_green = ($green_p > 255) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($green_p)));
            $this->_blue = ($blue_p > 255) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($blue_p)));
            $this->_depth = 24;
            
            return true;
            
        }else if(preg_match(self::RGB_PERCENTAGES_MATCH, $v, $matches)){
            
            $red_p = (int) $matches[1];
            $green_p = (int) $matches[2];
            $blue_p = (int) $matches[3];
            
            $this->_red = ($red_p > 100) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($red_p/100*255)));
            $this->_green = ($green_p > 100) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($green_p/100*255)));
            $this->_blue = ($blue_p > 100) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($blue_p/100*255)));
            $this->_depth = 24;
            
            return true;
            
        }else{
            
            $this->_red = new Smartest8BitInteger(0);
            $this->_green = new Smartest8BitInteger(0);
            $this->_blue = new Smartest8BitInteger(255);
            
            return true;
            
        }
        
        return false;
    }
    
    public function getValue(){
        return $this->toRgb256();
    }
    
    public function __toString(){
        return $this->toHex();
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return '#'.$this->toHex();
    }
    
    public function hydrateFromStorableFormat($v){
        return $this->setValue(substr($v, 1));
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        
        /* $red_p = (int) $v['red'];
        $green_p = (int) $v['green'];
        $blue_p = (int) $v['blue'];
        
        $this->_red = ($red_p > 100) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($red_p/100*255)));
        $this->_green = ($green_p > 100) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($green_p/100*255)));
        $this->_blue = ($blue_p > 100) ? new Smartest8BitInteger(255) : new Smartest8BitInteger(abs(ceil($blue_p/100*255))); */
        
        return $this->setValue($v);
        
    }
    
    public function toHex(){
        return $this->_red->toHex().$this->_green->toHex().$this->_blue->toHex();
    }
    
    public function toRgb256(){
        return $this->_red->getValue().','.$this->_green->getValue().','.$this->_blue->getValue();
    }
    
    public function toIntegers(){
        $ints = new SmartestParameterHolder("Color ints");
        $ints->setParameter('red', $this->_red);
        $ints->setParameter('green', $this->_green);
        $ints->setParameter('blue', $this->_blue);
    }
    
    public function getRed(){
        return $this->_red;
    }
    
    public function getGreen(){
        return $this->_green;
    }
    
    public function getBlue(){
        return $this->_blue;
    }
    
    public function getBrightness(){
        // Brightness calculation constants © 2006 Darel Rex Finley http://alienryderflex.com/hsp.html
        $raw = sqrt( .299*pow($this->_red->getValue(), 2) + .587*pow($this->_green->getValue(), 2) + .114*pow($this->_blue->getValue(), 2));
        return new Smartest8BitInteger($raw);
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "red":
            case "r":
            return $this->_red;
            case "green":
            case "g":
            return $this->_green;
            case "blue":
            case "b":
            return $this->_blue;
            case "hex":
            return $this->toHex();
            case "rgb":
            return $this->toRgb256();
            case "brightness":
            return $this->getBrightness();
        }
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
    
}