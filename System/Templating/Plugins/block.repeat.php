<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_block_repeat($params, $content, &$smarty, &$repeat){
	
	if ($repeat) {
	
		$items = $smarty->getRepeatBlockData($params);
		$index = 0;
		
		if($params['limit'] > 0){
			$items = array_slice($items, 0, $params['limit']);
		}
		
		// $block_name = md5(microtime(true));

	}else{
		
		$items = array_pop($smarty->_set_items_res);
    	$index = array_pop($smarty->_set_items_index)+1;
    	
	}
	
	$item = $items[$index];
	$check_length=($length == 0 or $index < $length);
	$repeat = !empty($item);

	if($item){
		$properties = $item->__toArray();
		
		// $properties["_name"] = $item["item_name"];
		// $properties["_id"] = $item["item_id"];
		$smarty->assign("repeated_item", $properties);
		$smarty->_set_items_res[] = &$items;
		$smarty->_set_items_index[] = &$index;
		
	}else{
	    
	}
	
	// echo $block_name;
	
	echo $content;
	
	$smarty->assign("repeated_item_object", $item);
	// echo "repeated_item_object".$item->getName();
}