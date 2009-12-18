<?php

function smarty_function_item_field($params, &$smarty){
    
    if(isset($params['property']) && isset($params['value'])){
        
        if(isset($params['property']) && $params['property']['datatype']){
            
            $file = 'Fields/property.'.strtolower(substr($params['property']['datatype'], 12)).'.tpl';
            
            if(is_file(constant('SM_CONTROLLER_MODULE_PRES_DIR').$file)){
                $smarty->_smarty_include(array('smarty_include_tpl_file'=>constant('SM_CONTROLLER_MODULE_PRES_DIR').$file, 'smarty_include_vars'=>array('value'=>$params['value'], 'property'=>$params['property'])));
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