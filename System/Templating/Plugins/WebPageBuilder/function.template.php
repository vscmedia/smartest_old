<?php

function smarty_function_template($params, &$smartest_engine){
	
	if(isset($params["name"]) && strlen($params["name"])){
	    
	    // echo get_class($smartest_engine);
	    // if(get_class($smartest_engine) == "Smartest"){
	        return $smartest_engine->renderTemplateTag($params['name']);
	    /* }else{
	        
	    } */
	    
	    
	}else{
	    
	    return $smartest_engine->raiseError("&ltl?sm:template:?&gt; tag requires name=\"\" attribute.");
	    
	}
	
}