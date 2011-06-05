<?php

class SmartestTag extends SmartestBaseTag{
    
    protected $_pages = array();
    protected $_page_ids = array();
    protected $_page_lookup_attempted = array();
    
    protected $_simple_items = array();
    protected $_items = array();
    protected $_item_ids = array();
    protected $_item_lookup_attempted = false;
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'tag_';
		$this->_table_name = 'Tags';
        
    }
    
    public function getPages($site_id='', $draft=false){
        
        if(!$site_id || !is_numeric($site_id)){
            $site_id = 'all';
        }
        
        if(!isset($this->_page_lookup_attempted[$site_id])){
        
            $sql = "SELECT * FROM TagsObjectsLookup, Pages WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=page_id AND taglookup_type='SM_PAGE_TAG_LINK'";
            
            if(is_numeric($site_id)){
                $sql .= " AND page_site_id='".$site_id."'";
            }
            
            if(!$draft){
                $sql .= " AND page_is_published='TRUE'";
            }
            
            $result = $this->database->queryToArray($sql);
            
            $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getCurrentSiteId());
        
            $pages = array();
        
            foreach($result as $page_array){
                
                if($type_index[$page_array['page_id']] == 'ITEMCLASS'){
                    $page = new SmartestItemPage;
                }else{
                    $page = new SmartestPage;
                }
                
                $page->hydrate($page_array);
                $pages[] = $page;
                
                if($page->getId() && !in_array($page->getId(), $this->_page_ids)){
                    $this->_page_ids[] = $page->getId();
                }
            }
            
            $this->_page_lookup_attempted[$site_id] = true;
            $this->_pages[$site_id] = $pages;
        
        }
        
        return $this->_pages[$site_id];
        
    }
    
    public function getPageIds($site_id='', $draft=false){
        
        if(!$site_id || !is_numeric($site_id)){
            $site_id = 'all';
        }
        
        $this->getPages($site_id, $draft);
        return $this->_page_ids;
        
    }
    
    public function getSimpleItems($site_id=false, $draft=false, $model_id=false){
        
        if(!$site_id || !is_numeric($site_id)){
            $site_id = 'all';
        }
        
        if(!$this->_item_lookup_attempted[$site_id]){
        
            $sql = "SELECT * FROM TagsObjectsLookup, Items WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=item_id AND taglookup_type='SM_ITEM_TAG_LINK' AND item_deleted = '0'";
            
            if(is_numeric($site_id)){
                $sql .= " AND item_site_id='".$site_id."'";
            }
            
            if(!$draft){
                $sql .= " AND item_public='TRUE'";
            }
            
            if($model_id && is_numeric($model_id)){
                $sql .= " AND item_itemclass_id='".$model_id."'";
            }
            
            $result = $this->database->queryToArray($sql);
            
            $items = array();
        
            foreach($result as $item_array){
                $item = new SmartestItem;
                $item->hydrate($item_array);
                $items[] = $item;
                
                if($item->getId() && !in_array($item->getId(), $this->_item_ids)){
                    $this->_item_ids[] = $item->getId();
                }
                
            }
            
            $this->_item_lookup_attempted[$site_id] = true;
            $this->_simple_items = $items;
        
        }
        
        return $this->_simple_items;
        
    }
    
    public function getSimpleItemsAsArrays($site_id=false, $draft=false, $model_id=false){
        
        $items = $this->getSimpleItems($site_id, $draft, $model_id);
        $arrays = array();
        
        foreach($items as $i){
            $arrays[] = $i->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getSimpleItemIds($site_id=false, $draft=false, $model_id=false){
        
        $items = $this->getSimpleItems($site_id, $draft, $model_id);
        $ids = array();
        
        foreach($items as $i){
            $ids[] = $i->getId();
        }
        
        return $ids;
        
    }
    
    public function getItems($site_id=''){
        
        if(!$this->_item_lookup_attempted){
        
            $sql = "SELECT Items.item_id FROM TagsObjectsLookup, Items WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=item_id AND taglookup_type='SM_ITEM_TAG_LINK' AND Items.item_public='TRUE' AND Items.item_deleted='0'";
            
            if($site_id && is_numeric($site_id)){
                $sql .= ' AND Items.item_site_id=\''.$site_id.'\'';
            }
            
            $result = $this->database->queryToArray($sql);
            
            $ids = array();
            
            foreach($result as $r){
                $ids[] = $r['item_id'];
            }
            
            $h = new SmartestCmsItemsHelper;
            $items = $h->hydrateMixedListFromIdsArray($ids);
        
            $this->_item_lookup_attempted = true;
            $this->_items = $items;
        
        }
        
        return $this->_items;
        
    }
    
    public function hasPage($page_id){
        
        // make sure pages have been retrieved
        $this->getPages();
        
        if(in_array($page_id, $this->_page_ids)){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function hasItem($item_id){
        
        // make sure pages have been retrieved
        $this->getSimpleItems(false, true, false);
        
        if(in_array($item_id, $this->_item_ids)){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function getObjectsOnSite($site_id, $draft=false){
        
        $master_array = array();
        
        $pages = $this->getPages($site_id, $draft);
        $items = $this->getItems($site_id, $draft);
        
        foreach($pages as $p){
            
            $key = $p->getDate();
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $p;
            
        }
        
        foreach($items as $i){
            
            $key = $i->getDate();
            if($key instanceof SmartestDateTime){
                $key = $key->getUnixFormat();
            }
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $i;
            
        }
        
        krsort($master_array);
        
        return $master_array;
        
    }
    
    public function getObjectsOnSiteAsArrays($site_id, $draft){
        
        $objects = $this->getObjectsOnSite($site_id, $draft);
        $arrays = array();
        
        foreach($objects as $o){
            $arrays[] = $o->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "objects":
            return $this->getObjectsOnSite($this->getCurrentSiteId(), true);
            
            case "url":
            return $this->_request->getDomain().'tags/'.$this->getName().'.html';
            
            case "feed_url":
            return $this->_request->getDomain().'tags/'.$this->getName().'/feed';
            
            default:
            
            $du = new SmartestDataUtility;
            $models = $du->getModelPluralNamesLowercase();
            
            if(isset($models[$offset])){
                // TODO: Model-specific tagged objects retrieval by model name
            }else{
                return parent::offsetGet($offset);
            }
            
            break;
            
        }
        
        
        
    }
    
}