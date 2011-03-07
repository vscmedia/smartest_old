<?php

function smarty_function_stylesheet($params, &$smartest_engine){
    
    if(isset($params['file']) && strlen($params['file'])){
        
        $file = $params['file'];
        
        if(!$smartest_engine->getStylesheetIncluded($file)){
            
            $smartest_engine->setStylesheetIncluded($file);
            return '<link rel="stylesheet" href="'.SmartestPersistentObject::get('request_data')->g('domain').'Resources/Stylesheets/'.$file.'" />';
            
        }
    }
}