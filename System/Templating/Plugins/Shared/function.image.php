<?php

/**
 * Smarty plugin for Smartest
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_image($params, &$smarty){
	if(@$params['file']){
		return $smarty->getImage($params);
	}else{
		return null;
	}
}

?>