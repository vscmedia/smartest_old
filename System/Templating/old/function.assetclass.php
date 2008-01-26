<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_assetclass($params, &$smarty){
	if(@$params['get']){
		return $smarty->getAssetClass($params['get']);
	}else{
		return null;
	}
}

?>
