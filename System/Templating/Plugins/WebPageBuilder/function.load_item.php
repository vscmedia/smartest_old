<?php

function smarty_function_load_item($params, &$smartest_engine){
    
    if(isset($params['assign']) && isset($params['assign']{0})){
        
        if(isset($params['id'])){
        
            $item = $smartest_engine->loadItemAsArray($params['id']);//sss
            $smartest_engine->assign($params['assign'], $item);
        
        }else{
            $smartest_engine->raiseError('load_item: parameter "id" must be defined');
        }
        
    }else{
        
        $smartest_engine->raiseError('load_item: parameter "assign" must be defined');
        
    }
    
}