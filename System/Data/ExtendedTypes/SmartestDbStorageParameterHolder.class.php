<?php

class SmartestDbStorageParameterHolder extends SmartestParameterHolder{
    
    public function getParameter($n, $default=null){
        
        return utf8_encode(stripslashes(rawurldecode(parent::getParameter($n, $default))));
        
    }
    
}