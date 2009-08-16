<?php

function smarty_function_item_select($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $item = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $item->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $item->setParameter('id', $params['id']);
        }else{
            $item->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $item->setParameter('value', $params['value']);
        }else{
            $item->setParameter('value', array());
        }
        
        if(isset($params['options'])){
            $item->setParameter('options', $params['options']);
        }else{
            $item->setParameter('options', array());
        }
        
        if(isset($params['required'])){
            $item->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $item->setParameter('required', false);
        }
        
        $smartest_engine->assign('_input_data', $item);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/cms_item.tpl', array());
        
    }else{
        
    }
    
}