<?php

class SmartestSite extends SmartestDataObject{
    
    protected $_home_page = null;
    protected $_containers = array();
    protected $_placeholders = array();
    protected $_sets = array();
    protected $_models = array();
    protected $displayPages = array();
    protected $displayPagesIndex = 0;
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'site_';
		$this->_table_name = 'Sites';
		
	}
	
	public function getHomePage(){
	    
	    // $this->setTopPageId(29);
	    // $this->save();
	    
	    // echo $this->getTopPageId();
	    
	    // if(!$this->_home_page){
	        $page = new SmartestPage;
	        $page->hydrate($this->getTopPageId());
	        $this->_home_page = $page;
	    // }
	    
	    return $this->_home_page;
	    
	}
	
	public function getPagesTree($draft_mode=true, $get_items=false){
	    
	    /* if($get_items){
	        $items_suffix = '_with_child_items';
	    }else{ */
	        $items_suffix = '';
	    // }
	    
	    if(SmartestCache::hasData('site_pages_tree_'.$this->getId().$items_suffix, true)){
			
			$tree = SmartestCache::load('site_pages_tree_'.$this->getId().$items_suffix, true);
			
		}else{
		
			$home_page = $this->getHomePage();
		    
		    // print_r($home_page);
		    
	        $tree = array();
			$tree[0]["info"] = $home_page->__toArray();
			$tree[0]["treeLevel"] = 0;
			$tree[0]["children"] = $home_page->getPagesSubTree(1, $draft_mode, $get_items);
			
			// if($get_items){
			$tree[0]["child_items"] = array();
		    // }
			
			SmartestCache::save('site_pages_tree_'.$this->getId().$items_suffix, $tree, -1, true);
		
		}
		
		// print_r($tree);
		
		return $tree;
	    
	}
	
	public function getPagesList($draft_mode=false, $get_items=false){
	    
	    // $tree = $this->getPagesTree();
	    // print_r($tree);
	    // $page = new SmartestPage;
	    $this->displayPages = array();
	    $this->displayPagesIndex = 0;
	    $list = $this->getSerializedPageTree($this->getPagesTree($draft_mode, $get_items));
	    // echo count($list);
	    // print_r($list);
	    return $list;
	    
	}
	
	public function getSerializedPageTree($tree){
		
		foreach($tree as $key => $page){
			
			// echo $key;
			
			// print_r($page);
			
			$this->displayPages[$this->displayPagesIndex]['info'] = $page['info'];
			$this->displayPages[$this->displayPagesIndex]['treeLevel'] = $page['treeLevel'];
			$children = $page['children'];
			$this->displayPages[$this->displayPagesIndex]['child_items'] = $page['child_items'];
			
			$this->displayPagesIndex++;
			
			if(count($children) > 0){
				$this->getSerializedPageTree($children);
			}
	
		}
		
		return $this->displayPages;
		
	}
	
	public function getSearchResults($query){
	    
	    $search_query_words = preg_split('/[^\w]+/', $query);
	    
	    $pages = array();
	    $pages_sql = "SELECT * FROM Pages WHERE page_site_id='".$this->getId()."' AND page_deleted != 'TRUE' AND page_id !='".$this->getSearchPageId()."' AND page_id !='".$this->getTagPageId()."'";
	    
	    foreach($search_query_words as $word){
	    
	        $pages_sql .= " AND page_search_field LIKE '%".$word."%'";
	    
        }
        
        if(count($search_query_words)){
            $pages_result = $this->database->queryToArray($pages_sql);
        }else{
            $pages_result = array();
        }
        
        foreach($pages_result as $array){
            $page = new SmartestPage;
            $page->hydrate($array);
            $pages[] = $page;
        }
        
        $items = array();
	    $items_sql = "SELECT * FROM Items WHERE item_site_id='".$this->getId()."' AND item_deleted != 'TRUE'";
	    
	    foreach($search_query_words as $word){
	    
	        $pages_sql .= " AND item_search_field LIKE '%".$word."%'";
	    
        }
        
        if(count($search_query_words)){
            $items_result = $this->database->queryToArray($items_sql);
        }else{
            $items_result = array();
        }
        
        foreach($items_result as $array){
            $item = new SmartestItem;
            $item->hydrate($array);
            $items[] = $item;
        }
        
        $master_array = array();
        
        foreach($pages as $p){
            
            if($draft){
                $key = $p->getCreated();
            }else{
                $key = $p->getLastPublished();
            }
            
            if(in_array($key, array_keys($master_array))){
                // $master_array[$key] = $i;
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $p;
            
        }
        
        foreach($items as $i){
            
            if($draft){
                $key = $i->getCreated();
            }else{
                $key = $i->getLastPublished();
            }
            
            if(in_array($key, array_keys($master_array))){
                // $master_array[$key] = $i;
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $i;
            
        }
        
        ksort($master_array);
        
        $final_list = array();
        
        foreach($master_array as $thing){
            $generic_object = new SmartestGenericListedObject($thing);
            $final_list[] = $generic_object;
        }
        
        return $final_list;
	    
	}
	
	public function getModels(){
	    
	}
	
	public function getModelsAsArrays(){
	    
	}
	
	public function getDataSets(){
	    
	    $sql = "SELECT Sets. * , ItemClasses.itemclass_id FROM Sets, ItemClasses WHERE Sets.set_itemclass_id = ItemClasses.itemclass_id AND ItemClasses.itemclass_site_id = '".$this->getId()."'";
	    $result = $this->database->queryToArray($sql);
	    $sets = array();
	    
	    // print_r($result);
	    
	    if(count($result)){
	        
	        foreach($result as $s_array){
	            $s = new SmartestCmsItemSet;
	            $s->hydrate($s_array);
	            $sets[] = $s;
	        }
	        
	        return $sets;
	    }else{
	        return array();
	    }
	}
	
	public function getDataSetsAsArrays(){
	    
	    $sets = $this->getDataSets();
	    $arrays = array();
	    
	    foreach($sets as $s){
	        $arrays[] = $s->__toArray(false);
	    }
	    
	    return $arrays;
	    
	}
	
	public function getContainers(){
	    
	}
	
	public function getContainersAsArrays(){
	    
	}
	
	public function getPlaceholders(){
	    
	}
	
	public function getPlaceholdersAsArrays(){
	    
	}
	
}