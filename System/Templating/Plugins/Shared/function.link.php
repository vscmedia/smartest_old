<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_link($params, &$smarty){
	if(@$params['to']){
		return $smarty->renderLink($params['to'], $params);
	}else{
		return null;
	}
}