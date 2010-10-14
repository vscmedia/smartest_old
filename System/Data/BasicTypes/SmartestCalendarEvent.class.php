<?php

class SmartestCalendarEvent implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_start_time;
    protected $_end_time;
    protected $_is_all_day = false;
    protected $_day_format = "l jS F, Y";
    protected $_time_format = "g:i a";
    
    public function __construct($value=''){
        if($value){
            $this->setValue($value);
        }else{
            $this->_start_time = new SmartestDateTime(0);
            $this->_end_time = new SmartestDateTime(60*15);
        }
    }
    
    public function getValue(){
        
    }
    
    public function setValue($value){
        
    }
    
    public function isCurrent(){
        $time = time();
        return ($this->_start_time <= $time && $time < $this->_end_time);
    }
    
    public function __toString(){
        if($this->_is_all_day){
            return "All day on ".date($this->_day_format, $this->_start_time);
        }else{
            if(date('dmY', $this->_start_time->getUnixFormat()) == date('dmY', $this->_end_time->getUnixFormat())){
                return "From ".date($this->_time_format, $this->_start_time->getUnixFormat())." until ".date($this->_time_format.' \o\n '.$this->_day_format, $this->_end_time->getUnixFormat());
            }else{
                return "From ".date($this->_time_format.' \o\n '.$this->_day_format, $this->_start_time->getUnixFormat())." until ".date($this->_time_format.' \o\n '.$this->_day_format, $this->_end_time->getUnixFormat());
            }
        }
    }
    
    public function getDurationInHours($rounded=false){
        if($rounded){
            return ceil($this->_value/3600);
        }else{
            return floor($this->_value/3600);
        }
    }
    
    public function getDurationInDays($rounded=false){
        if($rounded){
            return ceil($this->_value/3600/24);
        }else{
            return floor($this->_value/3600/24);
        }
    }
    
    public function getDuration($rounded=false){
        if($this->_is_all_day){
            $num_seconds = 3600*24;
        }else{
            $num_seconds = $this->_end_time->getUnixFormat() - $this->_start_time->getUnixFormat();
        }
    }
    
    public function getStorableFormat(){
        $obj = new stdClass;
        $obj->start_time = $this->_start_time->getUnixFormat();
        $obj->end_time = $this->_end_time->getUnixFormat();
        $obj->is_all_day = $this->_is_all_day;
        return serialize($obj);
    }
    
    public function hydrateFromStorableFormat($value){
        if($obj = unserialize($value)){
            $this->_start_time = new SmartestDateTime($obj->start_time);
            $this->_end_time = new SmartestDateTime($obj->end_time);
            $this->_is_all_day = (bool) $obj->is_all_day;
            return true;
        }else{
            return false;
        }
    }
    
    public function hydrateFromFormData($v){
        if(is_array($v)){
            if(isset($v['all_day'])){
                $this->_is_all_day = true;
                $this->_start_time = new SmartestDateTime($v['start_time']);
            }else{
                $this->_is_all_day = false;
                $this->_start_time = new SmartestDateTime($v['start_time']);
                $this->_end_time = new SmartestDateTime($v['end_time']);
            }
            return true;
        }
        
    }
    
    public function offSetGet($offset){
        
        switch($offset){
            
            case "start_time":
            return $this->_start_time;
            
            case "end_time":
            return $this->_end_time;
            
            case "duration":
            return $this->getDuration();
            
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}

}