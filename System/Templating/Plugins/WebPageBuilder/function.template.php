<?php

function smarty_function_template($params, &$smartest_engine){
	
	if(isset($params["name"]) && strlen($params["name"])){
	    
	    return $smartest_engine->renderTemplateTag($params['name']);
	    
	}else{
	    
	    return $smartest_engine->raiseError("&ltl?sm:template:?&gt; tag requires name=\"\" attribute.");
	    
	}
	
}