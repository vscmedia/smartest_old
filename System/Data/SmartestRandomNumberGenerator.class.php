<?php

class SmartestRandomNumberGenerator implements ArrayAccess{
    
    protected $_last = null;
    
    public function getRandomBetween($min=0, $max=1000){
        
        $n = mt_rand($min, $max);
        $this->_last = $n;
        return new SmartestNumeric($n);
        
    }
    
    public function getRandomAmongst($vals){
        
        $length = count($vals);
        $key = mt_rand(0, $length-1);
        $val = $vals[$key];
        return $val;
        
    }
    
    public function getSameAgain(){
        return $this->_last;
    }
    
    public function offsetGet($offset){
        
        // echo $offset;
        /* var_dump((bool) $vals = explode(',', $offset));
        var_dump(count($vals) > 1); */
        
        switch($offset){
            case "again":
            return $this->getSameAgain();
        }
        
        if(is_numeric($offset)){
            return new SmartestNumeric($this->getRandomBetween(0, $offset));
        }else if(preg_match('/(\d+)_(\d+)/', $offset, $matches)){
            return $this->getRandomBetween(min(array($matches[1], $matches[2])), max(array($matches[1], $matches[2])));
        }else if(preg_match('/^\d+(,(\d+))+$/', $offset)){
            // echo "worked";
            $vals = explode(',', $offset);
            return new SmartestNumeric($this->getRandomAmongst($vals));
        }else if(preg_match('/([\d-]+)/', $offset, $matches)){
            $values = explode('-', $matches[1]);
            $numbers = array();
            foreach($values as $v){
                if(is_numeric($v)){
                    $numbers[] = $v;
                }
            }
            return new SmartestNumeric($numbers[rand(0, count($numbers)-1)]);
        }else{
            return $this->getRandomBetween();
        }
        
    }
    
    public function offsetExists($offset){}
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    
    public function __toString(){
        return ''.$this->getRandomBetween();
    }
    
}