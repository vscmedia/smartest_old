<?php

function smarty_function_javascript($params, &$smartest_engine){
    
    if(isset($params['file']) && strlen($params['file'])){
        
        $file = $params['file'];
        
        if(!$smartest_engine->getScriptIncluded($file)){
            
            $smartest_engine->setScriptIncluded($file);
            
            if(substr($file, 0, 4) == 'http'){
                return '<script type="text/javascript" src="'.$file.'"></script>';
            }else{
                return '<script type="text/javascript" src="'.$smartest_engine->getRequestData()->getParameter('domain').'Resources/'.$file.'"></script>';
            }
        }
    }
}