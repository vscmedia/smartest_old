<?php

function smarty_function_load_interface($params, &$smarty){
    if(is_file(constant('SM_CONTROLLER_MODULE_PRES_DIR').$params['file'])){
        $smarty->_smarty_include(array('smarty_include_tpl_file'=>constant('SM_CONTROLLER_MODULE_PRES_DIR').$params['file'], 'smarty_include_vars'=>array()));
    }else{
        return 'interface file not found.';
    }
}