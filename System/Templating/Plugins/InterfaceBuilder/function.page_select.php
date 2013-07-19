<?php

function smarty_function_page_select($params, &$smartest_engine){
    
    // if(isset($params['name'])){
        
        $input_data_holder = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $input_data_holder->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $input_data_holder->setParameter('id', $params['id']);
        }else{
            $input_data_holder->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $input_data_holder->setParameter('value', $params['value']);
        }else{
            $input_data_holder->setParameter('value', array());
        }
        
        if(isset($params['options'])){
            $input_data_holder->setParameter('options', $params['options']);
        }else{
            $input_data_holder->setParameter('options', array());
        }
        
        if(isset($params['required'])){
            $input_data_holder->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $input_data_holder->setParameter('required', false);
        }
        
        if(isset($params['property_id'])){
            $input_data_holder->setParameter('property_id', $params['property_id']);
        }else{
            $input_data_holder->setParameter('property_id', null);
        }
        
        if(isset($params['host_item_id']) && is_numeric($params['host_item_id'])){
            $input_data_holder->setParameter('host_item_id', $params['host_item_id']);
        }else{
            $input_data_holder->setParameter('host_item_id', null);
        }
        
        /* if(is_numeric($input_data_holder->g('host_item_id')) && $input_data_holder->g('property_id')){
            $input_data_holder->setParameter('show_new_field', true);
        }else{
            $input_data_holder->setParameter('show_new_field', false);
        } */
        
        $smartest_engine->assign('_input_data', $input_data_holder);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/page.tpl', array());
        
    /* }else{
        
    } */
    
}