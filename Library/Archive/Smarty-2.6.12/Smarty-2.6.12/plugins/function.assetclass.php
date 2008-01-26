<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_assetclass($params, &$smarty){
	if($smarty->_db_obj && @$params['retrieve'] && @$params['method']){
		if($params['method'] == "renderPageFromId"){
			
			// echo $params['method'];
			// print_r($smarty->_db_obj);
			// echo $_SERVER["REQUEST_URI"];
			// return "worked";
		}else if($params['method'] == "renderPageFromUrl"){
			// echo $params['method'];
			// print_r($smarty->_db_obj);
			// echo $_SERVER["REQUEST_URI"];
			// return "worked";
		}
	}else{
		return false;
	}
}

?>
