<?php

function smarty_function_field($params, &$smartest_engine){
	if(isset($params['name']) && !empty($params['name'])){
		/* if(isset($smartest_engine->_page[$params['name']])){
			return $smartest_engine->_page[$params['name']];
		} */
		if(isset($params['display']) && SmartestStringHelper::isFalse($params['display'])){
	        return $smartest_engine->renderEditFieldButton($params['name'], $params);
	    }else{
		    return $smartest_engine->renderField($params['name'], $params);
	    }
	}else{
		return "Field error: 'name' not specified<br />";
	}
	
	
	
}