<?php

function smarty_function_template($params, &$smartest_engine){
	
	if(isset($params["name"]) && strlen($params["name"])){
	    
	    /* if(file_exists(SM_ROOT_DIR.'Presentation/Layouts/'.$params["name"])){
	        $smartest_engine->_smarty_include(array('smarty_include_tpl_file'=>SM_ROOT_DIR.'Presentation/Layouts/'.$params["name"], 'smarty_include_vars'=>array()));
	    }else{
	        // error - file doesn't exist
	    } */
	    
	    return $smartest_engine->renderTemplateTag($params['name']);
	    
	}else{
	    // error - name not specified
	}
	
}