<?php

function smarty_function_boolean($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $boolean = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $boolean->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $boolean->setParameter('id', $params['id']);
        }else{
            $boolean->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $boolean->setParameter('value', SmartestStringHelper::toRealBool($params['value']));
        }else{
            $boolean->setParameter('value', false);
        }
        
        if(isset($params['required'])){
            $boolean->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $boolean->setParameter('required', false);
        }
        
        if(isset($params['type']) && in_array($params['type'], array('radio', 'select', 'checkbox'))){
            $tpl = SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/boolean_'.$params['type'].'.tpl';
        }else{
            $tpl = SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/boolean_radio.tpl';
        }
        
        $smartest_engine->assign('_input_data', $boolean);
        $smartest_engine->run($tpl, array());
        
    }else{
        
    }
    
}