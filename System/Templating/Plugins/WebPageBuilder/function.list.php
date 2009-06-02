<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_list($params, &$smartest_engine){

	if(isset($params['name']) && strlen($params['name'])){
		$result = $smartest_engine->renderList($params['name'], $params);
		return $result;
	}else{
		return null;
	}	
}
