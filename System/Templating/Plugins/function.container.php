<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_container($params, &$smarty){
	if(@$params['name']){
		// return $smarty->getTemplateAssetClass($params['name'], $params);
		return $smarty->renderContainer($params['name'], $params, $smarty->getPage());
	}else{
		return null;
	}
}
