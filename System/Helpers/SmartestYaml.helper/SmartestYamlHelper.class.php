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
            SmartestLog::getInstance('system')->log("SmartestYamlHelper tries to load non-existent file: ".$file_name, SM_LOG_WARNING);
        }
    }
    
    public static function fastLoad($file_name){
        if(is_file($file_name)){
            if(SmartestCache::load('syhh_'.crc32($file_name), true) == md5_file($file_name)){
                return SmartestCache::load('syhc_'.crc32($file_name), true);
            }else{
                SmartestCache::save('syhh_'.crc32($file_name), md5_file($file_name), -1, true);
                $content = self::load($file_name);
                SmartestCache::save('syhc_'.crc32($file_name), $content, -1, true);
                return $content;
            }
        }else{
            SmartestLog::getInstance('system')->log("SmartestYamlHelper tries to load non-existent file: ".$file_name, SM_LOG_WARNING);
        }
    }
    
    public static function create($data_array){
        
    }
    
}