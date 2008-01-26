<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_imagepath($params, &$smarty){
	if(@$params['file']){
		return $smarty->getImagePath($params);
	}else{
		return null;
	}
}

?>