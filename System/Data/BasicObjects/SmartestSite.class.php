<?php

class SmartestSite extends SmartestBaseSite{
    
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
	
	public function getHomePage($draft_mode=false){
	    
	    $page = new SmartestPage;
	    $page->find($this->getTopPageId());
	    $page->setDraftMode($draft_mode);
	    $this->_home_page = $page;
	    
	    return $this->_home_page;
	    
	}
	
	public function getPagesTree($draft_mode=true, $get_items=false, $normal_pages_only=false){
	    
	    /* if($get_items){
	        $items_suffix = '_with_child_items';
	    }else{ */
	        $items_suffix = '';
	    // }
	    
	    if(SmartestCache::hasData('site_pages_tree_'.$this->getId().$items_suffix, true)){
			
			$tree = SmartestCache::load('site_pages_tree_'.$this->getId().$items_suffix, true);
			
		}else{
		
			$home_page = $this->getHomePage();
		    
		    $home_page->setDraftMode($draft_mode);
		    
		    $tree = array();
			$tree[0]["info"] = $home_page->__toArray();
			$tree[0]["treeLevel"] = 0;
			$tree[0]["children"] = $home_page->getPagesSubTree(1, $get_items);
			
			$tree[0]["child_items"] = array();
			
			SmartestCache::save('site_pages_tree_'.$this->getId().$items_suffix, $tree, -1, true);
		
		}
		
		return $tree;
	    
	}
	
	public function getPagesList($draft_mode=false, $get_items=false, $normal_pages_only=false){
	    
	    $this->displayPages = array();
	    $this->displayPagesIndex = 0;
	    $list = $this->getSerializedPageTree($this->getPagesTree($draft_mode, $get_items, $normal_pages_only));
	    return $list;
	    
	}
	
	public function getSerializedPageTree($tree){
		
		foreach($tree as $key => $page){
			
			$this->displayPages[$this->displayPagesIndex]['info'] = $page['info'];
			$this->displayPages[$this->displayPagesIndex]['treeLevel'] = $page['treeLevel'];
			$children = $page['children'];
			if(isset($page['child_items'])){
			    $this->displayPages[$this->displayPagesIndex]['child_items'] = $page['child_items'];
		    }
			
			$this->displayPagesIndex++;
			
			if(count($children) > 0){
				$this->getSerializedPageTree($children);
			}
	
		}
		
		return $this->displayPages;
		
	}
	
	public function getSpecialPageIds($include_home=false){
	    $ids = array("search_page_id"=>$this->getSearchPageId(), "tag_page_id"=>$this->getTagPageId(), "error_page_id"=>$this->getErrorPageId());
	    return $ids;
	}
	
	public function getNormalPagesList(){
	    $list = $this->getPagesList();
	    foreach($list as $k=>$page){
            if($page['info']['type'] != 'NORMAL' || in_array($page['info']['id'], $this->getSpecialPageIds())){
	            unset($list[$k]);
	        }
	    }
	    
	    return array_values($list);
	}
	
	public function getSearchResults($query){
	    
	    $search_query_words = preg_split('/[^\w]+/', $query);
	    
	    $pages = array();
	    $pages_sql = "SELECT * FROM Pages WHERE page_site_id='".$this->getId()."' AND page_deleted != 'TRUE' AND page_id !='".$this->getSearchPageId()."' AND page_id !='".$this->getTagPageId()."' AND page_type='NORMAL'";
	    
	    if(count($search_query_words) > 0){
	        $pages_sql .= ' AND ';
	    }
	    
	    foreach($search_query_words as $key=>$word){
	        
	        if($key > 0){
	            $pages_sql .= "OR ";
	        }
	        
	        $pages_sql .= "(page_search_field LIKE '%".$word."%' OR page_title LIKE '%".$word."%') ";
	        
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
	    $items_sql = "SELECT * FROM Items WHERE item_site_id='".$this->getId()."' AND item_deleted=0 AND item_public='TRUE'";
	    
	    if(count($search_query_words) > 0){
	        $items_sql .= ' AND ';
	    }
	    
	    foreach($search_query_words as $key=>$word){
	        
	        if($key > 0){
	            $items_sql .= "OR ";
	        }
	        
	        $items_sql .= "(item_search_field LIKE '%".$word."%' OR item_name LIKE '%".$word."%') ";
	        
        }
        
        // echo $items_sql;
        
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
	
	public function getPublicComments(){
	    
	    
	    
	}
	
	public function getTitleFormatSeparator(){
	    
	    $found = preg_match_all('/[\/\|\>›\xBB-]+/', $this->getTitleFormat(), $matches);
	    
	    if(count($matches)){
	        $symbols = $matches[0];
	        return $symbols[0];
        }
	}
	
	public function getModels(){
	    
	    $sql = "SELECT * FROM ItemClasses WHERE ItemClasses.itemclass_type='SM_ITEMCLASS_MODEL' AND (ItemClasses.itemclass_shared='1' OR ItemClasses.itemclass_site_id = '".$this->getId()."') ORDER BY itemclass_name";
	    $result = $this->database->queryToArray($sql);
	    $models = array();
	    
	    if(count($result)){
	        
	        foreach($result as $m_array){
	            $m = new SmartestModel;
	            $m->hydrate($m_array);
	            $models[] = $m;
	        }
	        
	        return $models;
	        
	    }else{
	        return array();
	    }
	    
	}
	
	public function getDataSets(){
	    
	    $sql = "SELECT * FROM Sets WHERE (Sets.set_type='DYNAMIC' || Sets.set_type='STATIC') AND (Sets.set_shared='1' OR Sets.set_site_id = '".$this->getId()."') ORDER BY set_name";
	    $result = $this->database->queryToArray($sql);
	    $sets = array();
	    
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
	    
	    $sql = "SELECT * FROM AssetClasses WHERE (assetclass_site_id='".$this->getId()."' OR assetclass_shared='1') AND assetclass_type='SM_ASSETCLASS_CONTAINER'";
	    $result = $this->database->queryToArray($sql);
	    
	    $containers = array();
	    
	    foreach($result as $r){
	        $c = new SmartestContainer;
	        $c->hydrate($r);
	        $containers[] = $c;
	    }
	    
	    return $containers;
	    
	}
	
	public function getPlaceholders(){
	    
	    $sql = "SELECT * FROM AssetClasses WHERE (assetclass_site_id='".$this->getId()."' OR assetclass_shared='1') AND assetclass_type NOT IN ('SM_ASSETCLASS_CONTAINER', 'SM_ASSETCLASS_ITEM_SPACE')";
	    $result = $this->database->queryToArray($sql);
	    
	    $placeholders = array();
	    
	    foreach($result as $r){
	        $p = new SmartestPlaceholder;
	        $p->hydrate($r);
	        $placeholders[] = $p;
	    }
	    
	    return $placeholders;
	    
	}
	
	public function getFullDirectoryPath(){
	    return SM_ROOT_DIR.'Sites/'.$this->getDirectoryName().'/';
	}
	
	public function getUniqueId(){
	    // TODO: Make a field to store this once it has been initially generated
	    $site_id = implode(':', str_split(substr(md5($this->getId()), 0, 6), 2));
	    $install_id = implode(':', str_split(substr(md5(SM_ROOT_DIR), 0, 6), 2));
	    $id = $install_id.':'.$site_id;
	    return $id;
	}
	
	public function testDirectoryStructure(){
	    // $directory = 
        /* $d = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/system.yml');
        $structure = */
    }
    
    public function getUsersThatHaveAccess(){
        
        $sql = "SELECT Users.* FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_token_id='21' AND UsersTokensLookup.utlookup_user_id=Users.user_id AND (UsersTokensLookup.utlookup_site_id='".$this->getId()."' OR UsersTokensLookup.utlookup_is_global=1) ORDER BY Users.user_firstname";
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $r){
            $u = new SmartestSystemUser;
            $u->hydrate($r);
            $users[] = $u;
        }
        
        return $users;
        
    }
	
}