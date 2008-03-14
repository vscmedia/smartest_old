<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_url($params, &$smarty){
	if(@$params['to']){
		return $smarty->renderUrl($params['to'], $params);
	}else{
		return null;
	}
}