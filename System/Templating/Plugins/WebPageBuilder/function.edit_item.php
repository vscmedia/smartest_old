<?php

function smarty_function_edit_item($params, &$smartest_engine){
	if(isset($params['id']) && !empty($params['id'])){
		return $smartest_engine->renderItemEditButton($params['id']);
	}else{
		return "Field error: 'name' not specified<br />";
	}
		
}