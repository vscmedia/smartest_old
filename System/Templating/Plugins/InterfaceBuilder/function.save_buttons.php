<?php

function smarty_function_save_buttons($params, &$smartest_engine){
    
    $vars['_continue_message'] = isset($params['continue_message']) ? $params['continue_message'] : "Save";
    $vars['_quit_message'] = isset($params['quit_message']) ? $params['quit_message'] : "Save &amp; return to ".SmartestSession::get("form:return:description");
    $vars['_cancel_message'] = isset($params['cancel_message']) ? $params['cancel_message'] : "Cancel";
    
    $smartest_engine->_smarty_include(array('smarty_include_tpl_file'=>SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/editform_buttons.tpl', 'smarty_include_vars'=>$vars));
    
}