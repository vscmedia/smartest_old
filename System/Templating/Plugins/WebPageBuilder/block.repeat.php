<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_block_repeat($params, $content, &$smartest_engine, &$repeat){
	
	$dah = new SmartestDataAppearanceHelper;
	
	if ($repeat) {
	
		$items = $smartest_engine->getRepeatBlockData($params);
		$index = 0;
		
		if($params['limit'] > 0){
			$items = array_slice($items, 0, $params['limit']);
		}

	}else{
		
		$items = array_pop($smartest_engine->_set_items_res);
    	$index = array_pop($smartest_engine->_set_items_index)+1;
    	
	}
	
	$item = $items[$index];
	$check_length=($length == 0 or $index < $length);
	$repeat = !empty($item);

	if($item){
		
		$smartest_engine->_set_items_res[] = &$items;
		$smartest_engine->_set_items_index[] = &$index;
		
	}
	
	echo $content;
	
	if($item){
	    
	    // these instructions are executed right before the item is displayed.
	    $smartest_engine->assign("repeated_item", $item);
	    $smartest_engine->assign("repeated_item_object", $item);
	    // var_dump($item->getId());
	    
	    // if($smartest_engine->getDraftMode()){
	        $dah->setItemAppearsOnPage($item->getId(), $smartest_engine->getPage()->getId());
        // }
	}
	
}