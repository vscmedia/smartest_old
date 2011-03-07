<?php

function smarty_function_stylesheet($params, &$smartest_engine){
    
    if(isset($params['file']) && strlen($params['file'])){
        
        $file = $params['file'];
        
        if(!$smartest_engine->getStylesheetIncluded($file)){
            
            $smartest_engine->setStylesheetIncluded($file);
            
            if(substr($file, 0, 4) == 'http'){
                return '<link rel="stylesheet" type="text/css" href="'.$file.'"></script>';
            }else{
                return '<link rel="stylesheet" type="text/css" href="'.SmartestPersistentObject::get('request_data')->g('domain').'Resources/Stylesheets/'.$file.'" />';
            }
        }
    }
}