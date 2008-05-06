<?php

SmartestHelper::register('Yaml');

// wrapper class for Spyc

include SM_ROOT_DIR.'Library/Spyc/spyc.php5';

class SmartestYamlHelper extends SmartestHelper{
    
    public static function load($file_name){
        if(is_file($file_name)){
            $array = Spyc::YAMLLoad($file_name);
            return $array;
        }else{
            // error 
        }
    }
    
    public static function create($data_array){
        
    }
    
}