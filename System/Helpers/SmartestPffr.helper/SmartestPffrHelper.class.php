<?php

class SmartestPffrHelper{
    
    public static function getContents($file){
        
        $pffr = new SmartestPffrFile($file);
        return $pffr->getData();
        
    }
    
    public static function getContentsFast($file){
        
        // only parses the file it it has changed
        $hash_hash = md5($file.'hash');
        $contents_hash = md5($file.'contents');
        
        if(!SmartestCache::load($hash_hash, true) || (SmartestCache::hasData($hash_hash, true) && SmartestCache::load($hash_hash, true) != md5_file($file))){
            $tokens = self::getContents($file);
            SmartestCache::save($hash_hash, md5_file($file), -1, true);
            SmartestCache::save($contents_hash, $tokens, -1, true);
        }else{
            $tokens = SmartestCache::load($contents_hash, true);
        }
        
        return $tokens;
        
    }
    
}