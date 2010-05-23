<?php

class ItemsAjax extends SmartestSystemApplication{

    public function simpleItemTextSearch(){
	    
	    $db = SmartestDatabase::getInstance('SMARTEST');
	    $sql = "SELECT * FROM Items WHERE (item_site_id='".$this->getSite()->getId()."' OR item_shared=1) AND item_deleted='0' AND (item_name LIKE '%".$this->getRequestParameter('query')."%' OR item_search_field LIKE '%".$this->getRequestParameter('query')."%') ORDER BY item_name";
	    $result = $db->queryToArray($sql);
	    $items = array();
	    
	    foreach($result as $r){
	        $item = new SmartestItem;
	        $item->hydrate($r);
	        $items[] = $item;
	    }
	    
	    $this->send($items, 'items');
	    
	}

}