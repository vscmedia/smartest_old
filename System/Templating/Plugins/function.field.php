<?php

function smarty_function_field($params, &$smarty){
	if(isset($params['name']) && !empty($params['name'])){
		/* if(isset($smarty->_page[$params['name']])){
			return $smarty->_page[$params['name']];
		} */
		return $smarty->renderField($params['name'], $params);
	}else{
		return "Field error: 'name' not specified<br />";
	}
	
	
	
}