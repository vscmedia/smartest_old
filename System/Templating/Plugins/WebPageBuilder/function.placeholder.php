<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_placeholder($params, &$smarty){
	if(@$params['name']){
		// return $smarty->getAssetClass($params['name'], $params);
		return $smarty->renderPlaceholder($params['name'], $params, $smarty->getPage());
	}else{
		return null;
	}
}
