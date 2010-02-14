<?php

class SmartestRgbColor implements ArrayAccess{
    
    protected $_red = 0;
    protected $_green = 32;
    protected $_blue = 255;
    protected $_depth = 16;
    
    const RGB_256_MATCH = '/^([0-255]),\s*([0-255]),\s*([0-255])$/';
    const RGB_PERCENTAGES_MATCH = '/^([0-100])%,\s*([0-100])%,\s*([0-100])%$/';
    const RGB_HEX_MATCH = '/^#?([0-9a-f]){3|6}$/';
    
    public function __construct($v=false){
        if($v){
            $this->setValue($v);
        }
    }
    
    public function setValue($v){
        
        if(preg_match(self::RGB_HEX_MATCH, $v, $matches)){
            
            $code = $matches[1];
            $this->_depth = (strlen($code) == 6) ? 16 : 8;
            
            $red_h = ($this->_depth == 8) ? '0x'.substr($code, 0, 1) : '0x'.substr($code, 0, 2);
            $green_h = ($this->_depth == 8) ? '0x'.substr($code, 1, 1) : '0x'.substr($code, 2, 2);
            $blue_h = ($this->_depth == 8) ? '0x'.substr($code, 2, 1) : '0x'.substr($code, 4, 2);
            
            $this->_red = (int) $red_h;
            $this->_green = (int) $green_h;
            $this->_blue = (int) $blue_h;
            
        }else if(preg_match(self::RGB_256_MATCH, $v, $matches)){
            
            $red_p = (int) $matches[1];
            $green_p = (int) $matches[2];
            $blue_p = (int) $matches[3];
            
            $this->_red = ($red_p > 255) ? 255 : abs(ceil($red_p/255*100));
            $this->_green = ($green_p > 255) ? 255 : abs(ceil($green_p/255*100));
            $this->_blue = ($blue_p > 255) ? 255 : abs(ceil($blue_p/255*100));
            $this->_depth = 16;
            
        }else if(preg_match(self::RGB_PERCENTAGES_MATCH, $v, $matches)){
            
            $red_p = (int) $matches[1];
            $green_p = (int) $matches[2];
            $blue_p = (int) $matches[3];
            
            $this->_red = ($red_p > 100) ? 255 : abs(ceil($red_p/100*255));
            $this->_green = ($green_p > 100) ? 255 : abs(ceil($green_p/100*255));
            $this->_blue = ($blue_p > 100) ? 255 : abs(ceil($blue_p/100*255));
            $this->_depth = 16;
            
        }
    }
    
    public function __toString(){
        return $this->toHex();
    }
    
    public function toHex(){
        return $this->getRed().$this->getGreen().$this->getBlue();
    }
    
    public function toIntegers(){
        $ints = new SmartestParameterHolder("Color ints");
        $ints->setParameter('red', $this->getRed('int'));
        $ints->setParameter('green', $this->getGreen('int'));
        $ints->setParameter('blue', $this->getGreen('int'));
    }
    
    public function getRed($mode='hex'){
        
    }
    
    public function getGreen($mode='hex'){
        
    }
    
    public function getBlue($mode='hex'){
        
    }
    
    public function offsetGet($offset){
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
    
}