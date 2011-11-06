<?php

class SmartestDateTime implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_value;
    protected $_all_day = false;
    protected $_time_format = "g:ia";
    protected $_day_format = "l jS F, Y";
    
    public function __construct($date=''){
        if((bool) $date){
            $this->setValue($date);
        }else{
            $this->_value = time();
        }
    }
    
    public function setValue($v){
        
        if(is_array($v)){
            $this->setValueFromUserInputArray($v);
            return true;
        }else if(is_numeric($v)){
            $this->_value = $v;
            return true;
        }else if(strlen($v) == 19){ // this is the fastest way to check for the format YYYY-MM-DD hh:ii:ss
            $this->setValueFromUserInputArray(array(
                'h' => substr($v, 11, 2),
                'i' => substr($v, 14, 2),
                's' => substr($v, 17, 2),
                'Y' => substr($v, 0, 4),
                'M' => substr($v, 5, 2),
                'D' => substr($v, 8, 2)
            ));
            return true;
        }else{
            return $this->_value = strtotime($v);
        }
    }
    
    public function setValueFromUserInputArray($v){
        $this->hydrateFromFormData($v);
    }
    
    public function getValue($format="l jS F, Y"){
        if(strlen($format)){
            return date($format, $this->_value);
        }else{
            return $this->_value;
        }
    }
    
    public function getUnixFormat(){
        return $this->_value;
    }
    
    /*
    
    $hours = floor($total_time/3600);
	$rounded_hours = ceil($total_time/3600);
	
	$remaining_time = $total_time-($hours*3600);
	$minutes = floor($remaining_time/60);
	
	$remaining_time -= $minutes*60;
	$seconds = ceil($remaining_time);
	
	return array("H"=>$hours, "M"=>$minutes, "S"=>$seconds, "R"=>$rounded_hours);
    
    */
    
    public function __toString(){
        if($this->_all_day){
            return date($this->_day_format, $this->_value);
        }else{
            return date($this->_time_format.' \o\n '.$this->_day_format, $this->_value);
        }
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_value;
        // MySQL Native date format
        // return date('Y-m-d H:i:s', $this->_value);
    }
    
    public function hydrateFromStorableFormat($v){
        return $this->setValue($v);
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        
        if(isset($v['h'])){
            $hour = $v['h'];
        }else{
            $hour = 0;
        }
        
        if(isset($v['i'])){
            $minute = $v['i'];
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
            throw new SmartestException("Arrays passed to SmartestDateTime::hydrateFromFormData() must have Y, M, and D keys");
        }
        
        if(isset($v['M'])){
            $month = $v['M'];
        }else{
            throw new SmartestException("Arrays passed to SmartestDateTime::hydrateFromFormData() must have Y, M, and D keys");
        }
        
        if(isset($v['D'])){
            $day = $v['D'];
        }else{
            throw new SmartestException("Arrays passed to SmartestDateTime::hydrateFromFormData() must have Y, M, and D keys");
        }
        
        $this->_value = mktime($hour, $minute, $second, $month, $day, $year);
        
        return true;
        
    }
    
    public function offsetExists($offset){
	    
	    return in_array($offset, array('g', 'i', 'a', 'm', 's', 'h', 'Y', 'M', 'D', 'unix'));
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case 'i':
	        return date('i', $this->_value);
	        
	        case 'h':
	        return date('h', $this->_value);
	        
	        case 'H':
	        return date('H', $this->_value);
	        
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
	        
	        case 'mysql_day':
	        return date('Y-m-d', $this->_value);
	        
	        case 'londonline':
	        return date('j F Y', $this->_value);
	        
	        case 'day_only':
	        return date($this->_day_format, $this->_value);
	        
	        case 'time_only':
	        return date($this->_time_format, $this->_value);
	        
	        case 'month_only':
	        return date('F Y', $this->_value);
	        
	        default:
	        return date($offset, $this->_value);
	        
	    }
	    
	}
	
	public function offsetSet($offset, $value){
	    // read only
	}
	
	public function offsetUnset($offset){
	    // read only
	}
    
}