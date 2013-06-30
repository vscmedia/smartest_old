<?php

function smarty_function_set_total($params, &$smartest_engine){
    
    if(isset($params['setname'])){
        
        $name = SmartestStringHelper::toVarName($params['setname']);
        return count($smartest_engine->getDataSetItemsByName($name));
        
    }else{
        
        return $smartest_engine->raiseError('Function set_total needs a set_name parameter');
        
    }
    
}