<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_placeholder($params, &$smartest_engine){
	if(@$params['name']){
		// return $smartest_engine->getAssetClass($params['name'], $params);
		return $smartest_engine->renderPlaceholder($params['name'], $params, $smartest_engine->getPage());
	}else{
		return null;
	}
}
