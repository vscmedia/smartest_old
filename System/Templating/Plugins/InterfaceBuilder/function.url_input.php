<?php

function smarty_function_url_input($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $url = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $url->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $url->setParameter('id', $params['id']);
        }else{
            $url->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $url->setParameter('value', $params['value']);
        }else{
            $url->setParameter('value', array());
        }
        
        $smartest_engine->assign('_input_data', $url);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/url.tpl', array());
        
    }else{
        
    }
    
}