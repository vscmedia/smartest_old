<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_list($params, &$smartest_engine){

	if(@$params['name']){
		$result = $smartest_engine->renderList($params['name'], $params);
		return $result;
	}else{
		return null;
	}	
}
