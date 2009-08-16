<?php

function smarty_function_asset_group_select($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $asset_group = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $asset_group->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $asset_group->setParameter('id', $params['id']);
        }else{
            $asset_group->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $asset_group->setParameter('value', $params['value']);
        }else{
            $asset_group->setParameter('value', array());
        }
        
        if(isset($params['options'])){
            $asset_group->setParameter('options', $params['options']);
        }else{
            $asset_group->setParameter('options', array());
        }
        
        if(isset($params['required'])){
            $asset_group->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $asset_group->setParameter('required', false);
        }
        
        $smartest_engine->assign('_input_data', $asset_group);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/asset_group.tpl', array());
        
    }else{
        
    }
    
}