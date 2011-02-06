<?php

function smarty_function_dataset_select($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $dataset_info = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $dataset_info->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $dataset_info->setParameter('id', $params['id']);
        }else{
            $dataset_info->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $dataset_info->setParameter('value', $params['value']);
        }else{
            $dataset_info->setParameter('value', array());
        }
        
        if(isset($params['options'])){
            $dataset_info->setParameter('options', $params['options']);
        }else{
            $dataset_info->setParameter('options', array());
        }
        
        if(isset($params['required'])){
            $dataset_info->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $dataset_info->setParameter('required', false);
        }
        
        $smartest_engine->assign('_input_data', $dataset_info);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/dataset.tpl', array());
        
    }else{
        
    }
    
}