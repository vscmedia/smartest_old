<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */
// include_once('Libraries/SmartestEngine/Smarty.class.php');

function smarty_function_list($params, &$smarty){

	if(@$params['name']){
		$result = $smarty->renderList($params['name'], $params);
		return $result;
	}else{
		return null;
	}	
}
