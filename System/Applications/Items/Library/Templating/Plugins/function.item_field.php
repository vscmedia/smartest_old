<?php

function smarty_function_item_field($params, &$smarty){
    
    if(isset($params['property'])){
      
        if(isset($params['value'])){
            $value = $params['value'];
        }else{
            $value = null;
        }
        
        if(isset($params['property']) && $params['property']['datatype']){
            
            $file = 'Fields/property.'.strtolower(substr($params['property']['datatype'], 12)).'.tpl';
            
            $input_data = new SmartestParameterHolder('Edit item field '.$params['property']['name']);
            $input_data->setParameter('id', 'item_property_'.$params['property']['id']);
            $input_data->setParameter('name', 'item['.$params['property']['id'].']');
            $input_data->setParameter('required', SmartestStringHelper::toRealBool($params['property']['required']));
            
            if(is_file(constant('SM_CONTROLLER_MODULE_PRES_DIR').$file)){
                $smarty->_smarty_include(array('smarty_include_tpl_file'=>constant('SM_CONTROLLER_MODULE_PRES_DIR').$file, 'smarty_include_vars'=>array('value'=>$value, 'property'=>$params['property'], '_input_data'=>$input_data)));
            }else{
                return constant('SM_CONTROLLER_MODULE_PRES_DIR').$file;
            }
            
        }else{
            return 'no datatype';
        }
        
    }else{
        // return 'params missing';
    }
}