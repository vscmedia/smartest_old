<?php

class SmartestExifDataTranslator{
    
    public function getTranslatedValue($tag, $name, $value){
        
        $exif_file = dirname(__FILE__).'/exif.yml';
        $exif_data = SmartestYamlHelper::fastLoad($exif_file);
        
        if(isset($exif_data['exif'][$tag])){
            
            if(isset($exif_data['exif'][$tag][$name])){
                
                if(isset($exif_data['exif'][$tag][$name][$value])){
                    return $exif_data['exif'][$tag][$name][$value];
                }else{
                    return "Unknown value";
                }
                
            }else{
                
                SmartestLog::getInstance('system')->log("Unknown EXIF field name given: ".$name);
                return null;
                
            }
            
        }else{
            
            SmartestLog::getInstance('system')->log("Unknown EXIF tag name given: ".$tag);
            return null;
            
        }
        
    }
    
}