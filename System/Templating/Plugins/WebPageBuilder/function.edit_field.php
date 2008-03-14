<?php

function smarty_function_edit_field($params, &$smarty){
	if(isset($params['name']) && !empty($params['name'])){
		return $smarty->renderEditFieldButton($params['name'], $params);
	}else{
		return "Field error: 'name' not specified<br />";
	}
		
}