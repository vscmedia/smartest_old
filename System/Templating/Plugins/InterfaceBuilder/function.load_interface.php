<?php

function smarty_function_load_interface($params, &$smarty){
    if(is_file(SM_ROOT_DIR.$params['file'])){
        $smarty->_smarty_include(array('smarty_include_tpl_file'=>SM_ROOT_DIR.$params['file'], 'smarty_include_vars'=>array()));
    }else if(is_file(constant('SM_CONTROLLER_MODULE_PRES_DIR').'Shared/'.$params['file'])){
        $smarty->_smarty_include(array('smarty_include_tpl_file'=>constant('SM_CONTROLLER_MODULE_PRES_DIR').'Shared/'.$params['file'], 'smarty_include_vars'=>array()));
    }else if(is_file(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/'.$params['file'])){
        $smarty->_smarty_include(array('smarty_include_tpl_file'=>SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/'.$params['file'], 'smarty_include_vars'=>array()));
    }else if(is_file(constant('SM_CONTROLLER_MODULE_PRES_DIR').$params['file'])){
        $smarty->_smarty_include(array('smarty_include_tpl_file'=>constant('SM_CONTROLLER_MODULE_PRES_DIR').$params['file'], 'smarty_include_vars'=>array()));
    }else{
        return '<div class="error">The interface file you tried to load, '.$params['file'].', was not found in any of the expected locations.</div>';
    }
}