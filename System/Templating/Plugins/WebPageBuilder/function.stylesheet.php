<?php

function smarty_function_stylesheet($params, &$smartest_engine){
    
    if(isset($params['file']) && strlen($params['file'])){
        
        $file = $params['file'];
        
        if(!$smartest_engine->getStylesheetIncluded($file)){
            
            $smartest_engine->setStylesheetIncluded($file);
            
            $a = new SmartestRenderableAsset;
            
            // var_dump(SmartestPersistentObject::get('request_data')->g('domain'));
            
            if($a->findBy('url', $file)){
                return $a->render();
            }else{
                return '<link rel="stylesheet" href="'.SmartestPersistentObject::get('request_data')->g('domain').'Resources/Stylesheets/'.$file.'" />';
            }
            
        }
        
    }else{
        
        return $smartest_engine->raiseError('You must specify a stylesheet to include with the file="" parameter.');
        
    }
}