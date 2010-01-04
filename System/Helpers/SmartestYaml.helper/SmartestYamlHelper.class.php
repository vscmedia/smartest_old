<?php

SmartestHelper::register('Yaml');

// wrapper class for Spyc

include SM_ROOT_DIR.'Library/Spyc/spyc.php';

class SmartestYamlHelper extends SmartestHelper{
    
    public static function load($file_name){
        if(is_file($file_name)){
            $spyc = new Spyc;
            $array = $spyc->loadFile($file_name);
            return $array;
        }else{
            // error
            SmartestLog::getInstance('system')->log("SmartestYamlHelper tried to load non-existent file: ".$file_name, SM_LOG_WARNING);
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
            SmartestLog::getInstance('system')->log("SmartestYamlHelper tried to load non-existent file: ".$file_name, SM_LOG_WARNING);
        }
    }
    
    public static function create($data_array, $file_name=false){
        
        $spyc = new Spyc;
        $yaml = $spyc->dump($data_array, false, false);
        
        if(is_file($file_name) && is_writable($file_name)){
            return file_put_contents($file_name, $yaml);
        }else{
            return $yaml;
        }
    }
    
    public static function fromParameterHolder(SmartestParameterHolder $ph, $file_name=false){
        $data = $ph->toArray();
        return self::create($data, $file_name);
    }
    
    public static function toParameterHolder($file_name, $fast=true){
        
        if($fast){
            $data = self::fastLoad($file_name);
        }else{
            $data = self::load($file_name);
        }
        
        $name = 'Data from '.$file_name;
        
        $ph = new SmartestParameterHolder($name);
        $ph->loadArray($data);
        
        return $ph;
        
    }
    
}