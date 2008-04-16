<?php

function smarty_function_edit_field($params, &$smartest_engine){
	if(isset($params['name']) && !empty($params['name'])){
		return $smartest_engine->renderEditFieldButton($params['name'], $params);
	}else{
		return "Field error: 'name' not specified<br />";
	}
		
}