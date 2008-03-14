<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_stylesheet($params, &$smarty){
	if(@$params['file']){
		return $smarty->getStylesheet($params);
	}else{
		return null;
	}
}

?>