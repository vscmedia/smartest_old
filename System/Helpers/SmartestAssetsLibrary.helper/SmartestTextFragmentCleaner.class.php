<?php

class SmartestTextFragmentCleaner{
    
    public static function clean($input){
        
        $output = $input;
        
        $output = str_ireplace('<br /><br />', '</p>'."\n".'<p>', $output);
        
        return $output;
        
    }
    
}