<?php

function smarty_function_edit_asset($params, &$smartest_engine){
	if(isset($params['id']) && !empty($params['id'])){
		return $smartest_engine->renderEditAssetButton($params['id'], $params, false);
	}else{
		return "Edit asset button error: 'id' not properly specified.";
	}
		
}