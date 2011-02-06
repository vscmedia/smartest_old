<?php

function smarty_function_asset_select($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $asset = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $asset->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $asset->setParameter('id', $params['id']);
        }else{
            $asset->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $asset->setParameter('value', $params['value']);
        }else{
            $asset->setParameter('value', array());
        }
        
        if(isset($params['options'])){
            $asset->setParameter('options', $params['options']);
        }else{
            $asset->setParameter('options', array());
        }
        
        if(isset($params['required'])){
            $asset->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $asset->setParameter('required', false);
        }
        
        // $smartest_engine->assign('_input_data', $asset);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/asset.tpl', $asset);
        
    }else{
        
    }
    
}