<?php

class SmartestPage extends SmartestDataObject{

	protected $_save_url = true;
	protected $_fields_retrieval_attempted = false;
	protected $_child_pages = array();
	protected $_child_pages_retrieved = false;
	protected $_child_web_pages = array();
	protected $_child_web_pages_retrieved = false;
	protected $_grandparent_page;
	protected $_parent_page;
	protected $_section_page;
	protected $_urls = array();
	
	protected $_fields = array();
	protected $_containers = array();
	protected $_placeholders = array();
	protected $_itemspaces = array();
	
	protected $_new_urls = array();
	protected $displayPagesIndex = 0;
	protected $displayPages = array();
	protected $_site;
	protected $_parent_site;
	
	const NOT_CHANGED = 100;
	const AWAITING_APPROVAL = 101;
	const CHANGES_APPROVED = 102;
	const NOT_PUBLIC = 103;
	
	const HIERARCHY_DEPTH_LIMIT = 32;
	
	protected function __objectConstruct(){
		$this->_table_prefix = 'page_';
		$this->_table_name = 'Pages';
		$this->addPropertyAlias('WebId', 'webid');
	}
	
	public function hydrate($id){
		// determine what kind of identification is being used
		
		if(is_array($id)){
		    
		    return parent::hydrate($id);
		
		}else{
		    
		    $this->_save_url = false;
		    
		    
		    
    		if(is_numeric($id)){
    			// numeric_id
    			$field = 'page_id';
    		}else if(preg_match('/[a-zA-Z0-9]{32}/', $id)){
    			// 'webid'
    			$field = 'page_webid';
    		}else if(preg_match('/[a-zA-Z0-9_-]+/', $id)){
    			// name
    			$field = 'page_name';
    		}
    		
    		if($field && $id){
		    
    		    $sql = "SELECT * FROM Pages WHERE $field='$id'";
		
        		$result = $this->database->queryToArray($sql);
		
        		if(count($result)){
			
        			foreach($result[0] as $name => $value){
        				if (substr($name, 0, 5) == $this->_table_prefix) {
        					$this->_properties[substr($name, 5)] = $value;
        					$this->_properties_lookup[SmartestStringHelper::toCamelCase(substr($name, 5))] = substr($name, 5);
        				}
        			}
			
        			$this->_came_from_database = true;
			
        			return true;
        		}else{
        			return false;
        		}
		    }
	    }
	}
	
	public function save(){
		
		parent::save();
		
		if($this->_properties['id']){
		    $sql = "SELECT pageurl_id FROM PageUrls WHERE pageurl_page_id='".$this->_properties['id']."'";
		    $result = $this->database->queryToArray($sql);
		    $num_existing_urls = count($result);
	    }else{
	        $num_existing_urls = 0;
	    }
		
		$i = 0;
		
		// Add URL
		foreach($this->_new_urls as $url_string){
		    
		    $url = new SmartestPageUrl;
    	    $url->setUrl($url_string);
    	    $url->setPageId($this->_properties['id']);
    	    
    	    if($i < 1){
    	        if($num_existing_urls == 0){
    	            $url->setIsDefault(1);
	            }
	        }
	        
    	    $url->save();
    	    $i++;
		}
		
		// Add definitions from preset, if any
		if($this->getPreset() && $this->_properties['id']){
			
			$preset = new SmartestPageLayoutPreset;
			$preset->hydrate($this->getPreset());
			$defs = $preset->getDefinitions();
			
			foreach($defs as $definition){
				$sql = "INSERT INTO AssetIdentifiers (assetidentifier_draft_asset_id, assetidentifier_assetclass_id, assetidentifier_page_id) VALUES ('".$definition['plpd_asset_id']."', '".$definition['plpd_assetclass_id']."', '".$this->_properties['id']."')";
			}
		}
	}
	
	public function delete($remove=false){
	    if($remove){
		    $sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->_properties['id']."' LIMIT 1";
		    $this->database->rawQuery($sql);
		    $this->_came_from_database = false;
	    }else{
	        $this->setField('Deleted', 1);
	        $this->save();
	    }
	}
	
	public function addUrl($url_string){
	    if(!in_array($url_string, $this->_new_urls)){
	        $this->_new_urls[] = $url_string;
	    }
	}
	
	public function clearDefaultUrl(){
	    $sql = "UPDATE PageUrls SET pageurl_is_default='0' WHERE pageurl_page_id='".$this->_properties['id']."'";
	    $this->database->rawQuery($sql);
	}
	
	public function setDefaultUrl($url){
	    if($url == (int) $url){
	        // we are dealing with a url id
	        $u = new SmartestPageUrl;
	        if($u->hydrate($url)){
	            $u->setIsDefault(1);
	            $this->clearDefaultUrl();
                $u->save();
                return true;
	        }else{
	            return false;
	        }
	    }else{
	        $sql = "SELECT PageUrls.*, Pages.page_id FROM PageUrls, Pages WHERE PageUrls.pageurl_page_id=Pages.page_id AND PageUrls.pageurl_url='".SmartestStringHelper::sanitize($url)."' AND Pages.page_site_id='".$this->getParentSite()->getId()."'";
	        $result = $this->database->queryToArray();
	        if(count($result)){
	            // url exists
	            $url_record = $result[0];
	            
	            if($url_record['page_id'] == $this->_properties['id']){
	                // the url is already in use for this page - just make it the default
	                $u = new SmartestPageUrl;
	                $u->hydrate($result[0]);
	                $u->setIsDefault(1);
	                $this->clearDefaultUrl();
	                $u->save();
	                return true;
	            }else{
	                // the url is in use for another page
	                // record to log
	                return false;
	            }
	            
	        }else{
	            // url doesn't exist
	            $u = new SmartestPageUrl;
	            $u->setUrl(SmartestStringHelper::sanitize($url));
	            $u->setPageId($this->_properties['id']);
	            $u->setIsDefault(1);
	            $this->clearDefaultUrl();
	            $u->save();
	            return true;
	        }
	    }
	}
	
	public function getAssetIdentifiers(){
	    
	    $sql = "SELECT * FROM AssetIdentifiers, AssetClasses WHERE assetidentifier_page_id='".$this->_properties['id']."' AND assetidentifier_assetclass_id=assetclass_id";
	    $result = $this->database->queryToArray($sql);
	    $ais = array();
	    
	    foreach($result as $r){
	        
	        $ai = new SmartestAssetIdentifier;
	        $ai->hydrateFromGiantArray($r);
	        // print_r($ai);
	        $ais[] = $ai;
	        
	    }
	    
	    return $ais;
	    
	}
	
	public function clearCachedCopies(){
	    
	    $cache_files = SmartestFileSystemHelper::load(SM_ROOT_DIR."System/Cache/Pages/");
		
		// removes all cache versions related to this page to keep the cache nice and tidy
		$cf_start = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id'];
		
		$result = false;
		
		foreach($cache_files as $f){
		    if(substr($f, 0, strlen($cf_start)) == $cf_start){
		        $result = unlink(SM_ROOT_DIR."System/Cache/Pages/".$f);
		    }
		}
		
		return $result;
	    
	}
	
	public function publish(){
	    
	    // update database defs
		$sql = "UPDATE Lists SET list_live_set_id=list_draft_set_id, list_live_template_file=list_draft_template_file, list_live_header_template=list_draft_header_template, list_live_footer_template=list_draft_footer_template WHERE list_page_id='".$this->_properties['id']."'";
		$this->database->rawQuery($sql);
		
		// $sql = "UPDATE AssetIdentifiers INNER JOIN AssetClasses USING (assetidentifier_assetclass_id,assetclass_id) SET assetidentifier_live_asset_id=assetidentifier_draft_asset_id, assetidentifier_live_render_data=assetidentifier_draft_render_data WHERE assetidentifier_page_id='".$this->_properties['id']."' AND AssetClasses.assetclass_update_on_page_publish='1'";
		// echo $sql;
		// $this->database->rawQuery($sql);
		
		// delete files in page cache
		$this->clearCachedCopies();
		
		$asset_identifiers = $this->getAssetIdentifiers();
		
		// new way of publishing asset identifiers that takes account of items and itemspaces
		foreach($asset_identifiers as $ai){
		    
		    if($ai->getAssetClass()->getType() == 'SM_ASSETCLASS_ITEM_SPACE'){
		        
		        $item = $ai->getSimpleItem(true);
		        
		        if($item){
		            $item_published = ($item->getPublic() == 'TRUE') ? true : false;
		        }else{
		            // echo 'no item <br />';
		        }
	        }
		    
		    if($ai->getAssetClass()->getUpdateOnPagePublish() == 1 || $item_published){
		        $ai->publish();
		    }
		}
		
		$sql = "UPDATE PagePropertyValues SET pagepropertyvalue_live_value=pagepropertyvalue_draft_value WHERE pagepropertyvalue_page_id='".$this->_properties['id']."'";
		$this->database->rawQuery($sql);
		
		$this->setLiveTemplate($this->getDraftTemplate());
		$this->setLastPublished(time());
		$this->setIsPublished('TRUE');
		$this->save();
		
		// publish all textfragments on the page
		foreach($this->getParsableTextFragments() as $tf){
		    $tf->publish();
		}
		
		// finally, request the page to force the system to build and cache the new copy
		// unfortunately this function is stil in need of errors, and still causes some preg_match errors
		// SmartestHttpRequestHelper::getContent(SM_CONTROLLER_DOMAIN.$this->getDefaultUrl());
		
	}
	
	public function unpublish($auto_save=true){
		$this->setIsPublished('FALSE');
		if($auto_save){
		    $this->save();
	    }
	}
	
	public function getTextFragments(){
	    
	    $sql = "SELECT TextFragments.* FROM Pages, Assets, AssetIdentifiers, TextFragments WHERE Pages.page_id=AssetIdentifiers.assetidentifier_page_id AND Assets.asset_id=AssetIdentifiers.assetidentifier_live_asset_id AND TextFragments.textfragment_id=Assets.asset_fragment_id AND Pages.page_id='".$this->_properties['id']."'";
		$result = $this->database->queryToArray($sql);
		$objects = array();
		
		foreach($result as $tfarray){
		    $tf = new SmartestTextFragment;
		    $tf->hydrate($tfarray);
		    $objects[] = $tf;
		}
		
		return $objects;
	}
	
	public function getParsableTextFragments(){
	    
	    $helper = new SmartestAssetsLibraryHelper;
		$codes = $helper->getParsableAssetTypeCodes();
		
		$sql = "SELECT TextFragments.* FROM Pages, Assets, AssetIdentifiers, TextFragments WHERE Pages.page_id=AssetIdentifiers.assetidentifier_page_id AND Assets.asset_id=AssetIdentifiers.assetidentifier_live_asset_id AND TextFragments.textfragment_id=Assets.asset_fragment_id AND Pages.page_id='".$this->_properties['id']."' AND Assets.asset_type IN ('".implode("', '", $codes)."')";
		$result = $this->database->queryToArray($sql);
		$objects = array();
		
		foreach($result as $tfarray){
		    $tf = new SmartestTextFragment;
		    $tf->hydrate($tfarray);
		    $objects[] = $tf;
		}
		
		return $objects;
		
	}
	
	public function getNextChildOrderIndex(){
	    $children = $this->getPageChildren();
	    if(count($children)){
	        $bottom_child_array = end($children);
	        $bottom_child = new SmartestPage;
	        $bottom_child->hydrate($bottom_child_array);
	        $oi = $bottom_child->getOrderIndex();
	        $index = (int) $oi;
	        return $index+1;
        }else{
            return 0;
        }
	}
	
	public function fixChildPageOrder($recursive=false){
	    
	    $children = $this->getPageChildren(true);
	    
	    if(count($children)){
	        
	        $order_index = 0;
	        
	        foreach($children as $p){
	            
	            // print_r($child_array);
	            
	            // $p = new SmartestPage;
	            // $p->hydrate($child_array);
	            
	            if(!SmartestStringHelper::toRealBool($p->getDeleted())){
	            
	                $p->setOrderIndex($order_index);
	                $p->save();
	                
	                $order_index++;
	                
	                if($recursive){
	                    $p->fixChildPageOrder();
	                }
                }
	        }
	    }
	}
	
	public function moveUp(){
	    
	    $this->getParentPage()->fixChildPageOrder();
	    
	    $order_index = $this->getOrderIndex();
	    $order_index = (int) $order_index;
	    
	    if($pp = $this->getPreviousPage()){
	        $this->setOrderIndex($pp->getOrderIndex());
            $this->save();
            $pp->setOrderIndex($order_index);
            $pp->save();
	    }
	}
	
	public function moveDown(){
	    
	    $this->getParentPage()->fixChildPageOrder();
	    
	    $order_index = $this->getOrderIndex();
	    $order_index = (int) $order_index;
	    
	    if($np = $this->getNextPage()){
	        $this->setOrderIndex($np->getOrderIndex());
            $this->save();
            $np->setOrderIndex($order_index);
            $np->save();
	    }
	    
	}
	
	public function getPreviousPage(){
	    $sql  = "SELECT * FROM Pages WHERE page_order_index < '".$this->getOrderIndex()."' AND page_parent='".$this->getParent()."' AND page_id !='".$this->_properties['id']."' AND page_deleted !='TRUE' ORDER BY page_order_index DESC LIMIT 1";
	    // echo $sql;
	    $result = $this->database->queryToArray($sql);
	    if(count($result)){
	        $pp = $result[0];
	        $page = new SmartestPage;
	        $page->hydrate($pp);
	        return $page;
	    }else{
	        return null;
	    }
	}
	
	public function getNextPage(){
	    $sql  = "SELECT * FROM Pages WHERE page_order_index > '".$this->getOrderIndex()."' AND page_parent='".$this->getParent()."' AND page_id !='".$this->_properties['id']."' AND page_deleted !='TRUE' ORDER BY page_order_index ASC LIMIT 1";
	    $result = $this->database->queryToArray($sql);
	    if(count($result)){
	        $np = $result[0];
	        $page = new SmartestPage;
	        $page->hydrate($np);
	        return $page;
	    }else{
	        return null;
	    }
	}
	
	public function getElementsAsList(){
		
	}

	public function getElementsAsTree(){
		
	}
	
	public function getPagesSubTree($level=1, $draft_mode=false, $get_items){
	
		$working_array = array();
		$index = 0;
		
		$_children = $this->getPageChildren($draft_mode);
		
		$int_level = (int) $level;
		
		foreach($_children as $child){
			
			/* if($child_page_record['page_type'] == 'ITEMCLASS'){
			    
			    $child = new SmartestItemPage;
			    
		    }else{
		        
		        $child = new SmartestPage;
		        
		    } */
		    
		    $child->hydrate($child_page_record);
			
			$working_array[$index]["info"] = $child->__toArray();
			$working_array[$index]["treeLevel"] = $int_level;
			$new_level = $int_level + 1; 
			$working_array[$index]["children"] = $child->getPagesSubTree($new_level, $draft_mode, $get_items);
			
			/* if($child->getType() == "ITEMCLASS" && $get_items){
			    $set = $child->getDataSet();
			    $working_array[$index]["child_items"] = $set->getMembersAsArrays();
			}else{
			    $working_array[$index]["child_items"] = array();
			} */
			
			$index++;
			
		}
		
		return $working_array;
	}
	
	public function getSerializedPageTree($level=1, $get_items=false, $use_passed_array=false, $passed_array='', $draft_mode){
		
		if($use_passed_array && is_array($passed_array)){
		    $pagesArray = $passed_array;
		}else{
		    $pagesArray = $this->getPagesSubTree($level, $draft_mode, $get_items);
	    }
		
		$new_level = (int) $level + 1;
		
		foreach($pagesArray as $page){
			
			$this->displayPages[$this->displayPagesIndex]['info'] = $page['info'];
			$this->displayPages[$this->displayPagesIndex]['treeLevel'] = $page['treeLevel'];
			$children = $page['children'];
			
			$this->displayPagesIndex++;
			
			// print_r($children);
			
			if(count($children) > 0){
			    
			    foreach($children as $child_array){
				    
				    $this->getSerializedPageTree($new_level, $get_items, true, array($child_array), $draft_mode);
			        
		        }
		        
			}
	
		}
		
		return $this->displayPages;
		
	}
	
	public function getAvailableIconImageFilenames(){
	    
	    $sql = "SELECT * FROM Assets WHERE asset_type IN ('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE') AND (asset_site_id='".$this->_properties['site_id']."' OR asset_shared=1) AND asset_deleted!=1 ORDER BY asset_url";
	    $result = $this->database->queryToArray($sql);
	    
	    // print_r($this->getSite());
	    
	    // echo $sql;
	    
	    $filenames = array();
	    
	    foreach($result as $a){
	        $filenames[] = $a['asset_url'];
	    }
	    
	    return $filenames;
	}
	
	public function getDefaultUrl(){
	    
	    $urls = $this->getUrls();
	    
	    // if($this->isTagPage()){
	        
	        // echo get_class($this);
	        // return 'tags/'.$this->_tag->getName().'.html';
	        
	    // }else{
	    
	        if(count($urls)){
    	        // If there are actually urls for this page:
    	        foreach($urls as $u){
    	            if($u->getIsDefault()){
    	                return $u->getUrl();
    	            }
    	        }
	        
    	        return $urls[0]->getUrl();
	        
    	    }else{
    	        // No urls have been defined.
    	        if($this->isHomePage()){
    	            // Return "/"
    	            $url = '';
    	        }else{
    	            // Return a dynamic one.
    	            $url = 'website/renderPageFromId?page_id='.$this->getWebid();
	            }
    	    }
	    
        // }
	    
	    return $url;
	}
	
	public function getUrls(){
		
		if(!count($this->_urls)){
		
		    $sql = "SELECT * FROM PageUrls WHERE pageurl_page_id ='".$this->_properties['id']."'";
		    // echo $sql;
		    $pageUrls = $this->database->queryToArray($sql);
		
		    foreach($pageUrls as $key => $url){
		        
		        $urlObj = new SmartestPageUrl;
		        $urlObj->hydrate($url);
		        $this->_urls[$key] = $urlObj;
		        
		    }
		
	    }
	    
	    return $this->_urls;

	}
	
	public function getUrlsAsArrays(){
	    
	    $urls = $this->getUrls();
	    $urls_array = array();
	    
	    foreach($urls as $u){
	        $urls_array[] = $u->__toArray();
	    }
	    
	    return $urls_array;
	    
	}
	
	public function getParentPage(){
	    
	    if(!$this->_parent_page){
	    
	        $parent = new SmartestPage;
	        $parent->hydrate($this->getParent());
	        $this->_parent_page = $parent;
	        
        }
        
        return $this->_parent_page;
        
	}
	
	public function getGrandParentPage(){
	    
	    if(!$this->_grandparent_page){
	    
	        $grandparent = new SmartestPage;
	        $grandparent->hydrate($this->getParentPage()->getParent());
	        $this->_grandparent_page = $grandparent;
	        
        }
        
        return $this->_grandparent_page;
        
	}

	public function getPageChildren($draft_mode=false, $sections_only=false){
	    
	    $sql = "SELECT DISTINCT * FROM Pages WHERE page_parent='".$this->_properties['id']."' AND page_site_id='".$this->_properties['site_id']."' AND page_deleted != 'TRUE'";
		
		if(!$draft_mode){
		    $sql .= " AND page_is_published = 'TRUE'";
		}
		
		if($sections_only){
		    $sql .= " AND page_is_section = '1'";
		}
		
		$sql .= " ORDER BY page_order_index, page_id ASC";
		
		$result = $this->database->queryToArray($sql);
	    $i = 0;
	    
	    if(is_array($result)){
	    
	        foreach($result as $page_record){
	            $child_page = new SmartestPage;
	            $child_page->hydrate($page_record);
	            $this->_child_pages[$i] = $child_page;
	            $i++;
	        }
	    
	        $this->_child_pages_retrieved = true;
	    
        }
	        
	    return $this->_child_pages;
	}
	
	public function getPageChildrenAsArrays($draft_mode=false, $sections_only=false){
	    
	    $children = $this->getPageChildren($draft_mode, $sections_only);
	    $array = array();
	    
	    foreach($children as $child_page){
	        $array[] = $child_page->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getPageChildrenForWeb($draft_mode=false, $sections_only=false){
	    
	    if($this->getParentSite()->getTagPageId()){
	        $special_page_ids = array($this->getParentSite()->getTagPageId());
	    }
        
        
        
        // these values should not be the same as the ids of the other special pages, 
        // but just in case they are, prevent them from being in the SQL query twice:
        if($this->getParentSite()->getErrorPageId() && !in_array($this->getParentSite()->getErrorPageId(), $special_page_ids)){
            $special_page_ids[] = $this->getParentSite()->getErrorPageId();
        }
        
        if($this->getParentSite()->getSearchPageId() && !in_array($this->getParentSite()->getSearchPageId(), $special_page_ids)){
            $special_page_ids[] = $this->getParentSite()->getSearchPageId();
        }
        
        $sql = "SELECT DISTINCT * FROM Pages WHERE page_parent='".$this->_properties['id']."' AND page_site_id='".$this->_properties['site_id']."' AND page_deleted != 'TRUE'";
		
		if(!$draft_mode){
		    $sql .= " AND page_is_published = 'TRUE'";
		}
		
		if($sections_only){
		    $sql .= " AND page_is_section = '1'";
		}
		
		if(count($special_page_ids)){
		    $sql .= " AND page_id NOT IN('".implode("', '", $special_page_ids)."')";
	    }
		
		$sql .= " ORDER BY page_order_index, page_id ASC";
		
		$result = $this->database->queryToArray($sql);
	    $i = 0;
	    
	    if(is_array($result)){
	    
	        foreach($result as $page_record){
	            if($page_record['page_type'] == 'NORMAL'){
	                $child_page = new SmartestPage;
	                $child_page->hydrate($page_record);
	                $this->_child_web_pages[$i] = $child_page;
	                $i++;
                }
                
                /* else if($page_record['page_type'] == 'ITEMCLASS'){
                    
                    $model = new SmartestModel;
                    
                    if($model->hydrate($page_record['page_dataset_id'])){
                        foreach($model->getSimpleItems() as $item){
                            
                            $child_page = new SmartestItemPage;
                            $child_page->hydrate($page_record);
                            $child_page->setSimpleItem($item);
                            $child_page->setIdentifyingFieldName('id');
                            $child_page->setIdentifyingFieldValue($item->getId());
                            $child_page->assignPrincipalItem();
                            
                            $is_acceptable = $child_page->isAcceptableItem($draft_mode);
                            
                            // var_dump($is_acceptable);
                            
                            if($is_acceptable){
                                $this->_child_web_pages[$i] = $child_page;
        	                    $i++;
    	                    }
                        }
                    }
                } */
	        }
	    
	        $this->_child_web_pages_retrieved = true;
	    
        }
        
        return $this->_child_web_pages;
        
	}
	
	
	public function getPageChildrenForWebAsArrays($draft_mode=false, $sections_only=false){
	    
	    $children = $this->getPageChildrenForWeb($draft_mode, $sections_only);
	    $array = array();
	    
	    foreach($children as $child_page){
	        $array[] = $child_page->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getPageFields(){
	    
	    // if(!$this->_fields_retrieval_attempted){
	    
	    $sql = "SELECT * FROM `PageProperties` WHERE pageproperty_site_id='".$this->_properties['site_id']."'";
	    $result = $this->database->queryToArray($sql);
        
        // print_r($result);
        // var_dump($this->_properties['site_id']);
        // echo $sql;
        
	    foreach($result as $p){
	        $property = new SmartestPageField;
	        $property->hydrate($p);
	        $this->_fields[$property->getId()] = $property;
	    }
    
	    $sql = "SELECT * FROM `PagePropertyValues` WHERE pagepropertyvalue_page_id='".$this->_properties['id']."'";
	    $result = $this->database->queryToArray($sql);
        
        // echo $sql;
        // print_r($result);
        
        // print_r($this->_fields);
        
        foreach($result as $pfda){
            // $property = new SmartestPageFieldDefinition;
            // $property->hydrate($pfda);
            // print_r($pfda);
            $fid = $pfda['pagepropertyvalue_pageproperty_id'];
            
            if(is_object($this->_fields[$fid])){
                $this->_fields[$fid]->setContextualPageId($this->_properties['id']);
                $this->_fields[$fid]->hydrateValueFromPpdArray($pfda);
            }
        }
        
        // print_r($this->_fields);
        
        $this->_fields_retrieval_attempted = true;
	    
        // }
        
        return $this->_fields;
	    
	}
	
	public function getPageFieldsAsArrays($numeric_keys=false){
	    
	    $fields = $this->getPageFields();
	    $arrays = array();
	    
	    foreach($fields as $id => $field){
	        
	        if($numeric_keys){
	            $key = $id;
	        }else{
	            $key = $field->getName();
	        }
	        
	        $arrays[$key] = $field->__toArray();
	        
	    }
	    
	    return $arrays;
	    
	}
	
	public function getPageFieldValuesAsAssociativeArray($draft_mode=false){
	    
	    $fields = $this->getPageFields();
	    $array = array();
	    
	    // print_r($fields);
	    
	    foreach($fields as $f){
	        
	        $key = $f->getName();
	        
	        if($draft_mode){
	            $data = $f->getData()->getDraftValue();
	        }else{
	            $data = $f->getData()->getLiveValue();
	        }
	        
	        $array[$key] = $data;
	        
	    }
	    
	    return $array;
	    
	}
	
	public function fetchRenderingData($draft_mode=false){
	    
	    $data = array();
	    $data['page'] = $this->__toArray();
	    
	    $data['tags'] = $this->getTagsAsArrays();
	    
	    if($this instanceof SmartestItemPage){
	        if($this->getPrincipalItem()){
	            
	            $data['principal_item'] = $this->getPrincipalItem()->__toArray();
	            
	            // $data['sibling_items'] = $this->getDataSet()->getMembersAsArrays();
	            // $data['data_set'] = $this->getDataSet()->__toArray();
	            
	            $data['is_item'] = true;
	            
            }else{
                
                $data['principal_item'] = array();
                
                /* if($this->getDataSet() instanceof SmartestCmsItemSet){
                    $data['sibling_items'] = $this->getDataSet()->getMembersAsArrays();
                    $data['data_set'] = $this->getDataSet()->__toArray();
                }else{
                    $data['sibling_items'] = array();
                    $data['data_set'] = array();
                } */
                
                $data['is_item'] = true;
            }
	    }else{
	        $data['is_item'] = false;
	    }
	    
	    $du = new SmartestDataUtility;
	    $tags = $du->getTagsAsArrays();
	    $data['all_tags'] = $tags;
	    
	    $data['authors'] = $this->getAuthorsAsArrays();
	    
	    $data['fields'] = $this->getPageFieldValuesAsAssociativeArray($draft_mode);
	    $data['navigation'] = $this->getNavigationStructure($draft_mode);
	    
	    return $data;
	}
	
	public function isTagPage(){
	    return ($this->getParentSite()->getTagPageId() == $this->_properties['id']);
	}
	
	public function isSearchPage(){
	    return ($this->getParentSite()->getSearchPageId() == $this->_properties['id']);
	}
	
	public function isHomePage(){
	    return ($this->getParentSite()->getTopPageId() == $this->_properties['id']);
	}
	
	public function isApproved(){
	    return (bool) $this->_properties['changes_approved'];
	}
	
	public function touch(){
	    if($this->_came_from_database == true){
	        $sql = "UPDATE Pages SET page_modified = '".time()."' WHERE Pages.page_id = '".$this->_properties['id']."'";
	        $this->database->rawQuery($sql);
	    }
	}
	
	public function __toArray($getChildren=false, $require_site_lookup=true){
	    
	    $array = parent::__toArray();
	    
	    $array['title'] = $this->getTitle();
	    $array['url'] = $this->getDefaultUrl();
	    $array['formatted_title'] = $this->getFormattedTitle();
	    $array['is_tag_page'] = $this->isTagPage();
	    
	    if($this->getType() == 'ITEMCLASS'){
	        if(is_object($this->getSimpleItem())){
	            $array['link_contents'] = 'metapage:'.$this->getName().':id='.$this->getSimpleItem()->getId();
            }else{
                $array['link_contents'] = 'page:'.$this->getName();
            }
	    }else{
	        $array['link_contents'] = 'page:'.$this->getName();
        }
	    
	    if($getChildren){
	        $array['_child_pages'] = $this->getPageChildrenAsArrays();
        }
        
        return $array;
        
	}
	
	public function getWorkflowStatus(){
	    
	    if($this->getIsPublished() == 'TRUE'){
	    
	        if($this->getModified() > $this->getLastPublished()){
	        
	            // page has changed since it was last published
	            if($this->getChangesApproved()){
	                return self::CHANGES_APPROVED;
	            }else{
	                return self::AWAITING_APPROVAL;
	            }
	        
	        }else{
	            // page hasn't been modified
	            return self::NOT_CHANGED;
	        }
	    
        }else{
            return self::NOT_PUBLISHED;
        }
	}

	public function getOkParentPages(){
		
		//// CODE TO GET LIST OF PAGES THAT ARE ACCEPTABLE AS PARENTS
		//// FOR THE CURRENT PAGE. I.E. NOT ITSELF OR ANY OF ITS CHILDREN
		
		// $site_id = $this->manager->database->specificQuery("page_site_id", "page_id", $page_id, "Pages");
		$site_id = $this->_properties['site_id'];
		
		// FIRST GET A LIST OF ALL PAGES
		$all_pages = $this->getParentSite()->getPagesList(true);
		
		// print_r($all_pages);
		
		// echo "\n\n\n<br />\n\n";
		
		// 
		$this->displayPages = array();
		$this->displayPagesIndex = 0;
		
		// THEN GET A LIST OF ALL CHILD PAGES
		
		// $sub_pages = $this->getPagesSubTree(1);
		
		$sub_pages_list = $this->getSerializedPageTree(1, false, false, '', true);
		
		// print_r($sub_pages_list);
		
		/* $this->manager->displayPages = array();
		$this->manager->displayPagesIndex = 0; */
		
		$all_page_ids = array();
		$sub_page_ids = array();
		
		// print_r($sub_pages_list);
		
		// MAKE A SIMPLE ARRAY OF ALL THE CHILD PAGE IDS
		foreach($sub_pages_list as $child_page_array){
			if(!in_array($child_page_array["info"]["id"], $sub_page_ids)){
				$sub_page_ids[] = $child_page_array["info"]["id"];
				// print_r($child_page_array);
			}
		}
		
		// print_r($sub_page_ids);
		
		// REMOVE THOSE PAGES FROM THE MAIN LIST
		foreach($all_pages as $key=>$page_array){
			if(in_array($page_array["info"]["id"], $sub_page_ids) || $page_array["info"]["id"] == $this->_properties['id']){
				unset($all_pages[$key]);
			}
		}
		
		return $all_pages;
	}
	
	public function clearTags(){
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_type='SM_PAGE_TAG_LINK'";
	    $this->database->rawQuery($sql);
	}
	
	public function getTagIdsArray(){
	    
	    $sql = "SELECT taglookup_tag_id FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_type='SM_PAGE_TAG_LINK'";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    
	    foreach($result as $tl){
	        if(!in_array($ta['taglookup_tag_id'], $ids)){
	            $ids[] = $tl['taglookup_tag_id'];
	        }
	    }
	    
	    return $ids;
	    
	}
	
	public function getTags(){
	    
	    $sql = "SELECT * FROM Tags, TagsObjectsLookup WHERE TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id='".$this->_properties['id']."' AND TagsObjectsLookup.taglookup_type='SM_PAGE_TAG_LINK' ORDER BY Tags.tag_name";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    $tags = array();
	    
	    foreach($result as $ta){
	        if(!in_array($ta['taglookup_tag_id'], $ids)){
	            $ids[] = $ta['taglookup_tag_id'];
	            $tag = new SmartestTag;
	            $tag->hydrate($ta);
	            $tags[] = $tag;
	        }
	    }
	    
	    return $tags;
	    
	}
	
	public function getTagsAsArrays(){
	    
	    $arrays = array();
	    $tags = $this->getTags();
	    
	    foreach($tags as $t){
	        $arrays[] = $t->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function tag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it of if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                // create tag
    	        $tag->setLabel($tag_identifier);
    	        $tag->setName($tag_name);
    	        $tag->save();
    	    }
	    }
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->_properties['id']."', 'SM_PAGE_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function untag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                return false;
    	    }
	    }
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_PAGE_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function isRelatedToPage($page_id){
	    
	}
	
	public function getRelatedPages($draft_mode=false){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_PAGES');
	    $q->setCentralNodeId($this->_properties['id']);
	    $q->addSortField('Pages.page_title');
	    
	    if(!$draft_mode){
	        $q->addForeignTableConstraint('Pages.page_is_published', 'TRUE');
	    }
	    
	    $related_pages = $q->retrieve();
	    // print_r($related_pages);
	    return $related_pages;
	}
	
	public function getRelatedPagesAsArrays($draft_mode=false){
	    
	    $pages = $this->getRelatedPages($draft_mode);
	    $arrays = array();
	    
	    foreach($pages as $page){
	        $arrays[] = $page->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedPageIds($draft_mode=false){
	    
	    $pages = $this->getRelatedPages($draft_mode);
	    $ids = array();
	    
	    foreach($pages as $page){
	        $ids[] = $page->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function addRelatedPage($page_id){
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_PAGES');
	    $q->createNetworkLinkBetween($this->_properties['id'], $page_id);
	}
	
	public function removeRelatedPage($page_id){
	    $page_id = (int) $page_id;
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_PAGES');
	    $q->deleteNetworkLinkBetween($this->_properties['id'], $page_id);
	}
	
	public function removeAllRelatedPages(){
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_PAGES');
	    $q->deleteNetworkNodeById($this->_properties['id']);
	}
	
	public function isRelatedToItem($page_id){
	    
	}
	
	public function getRelatedItems($draft_mode=false, $model_id=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGES_ITEMS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    
	    $q->addForeignTableConstraint('Items.item_deleted', 1, SmartestQuery::NOT_EQUAL);
	    
	    if($model_id && (int) $model_id == $model_id){
	        $q->addForeignTableConstraint('Items.item_itemclass_id', $model_id);
	    }
	    
	    if(!$draft_mode){
	        $q->addForeignTableConstraint('Items.item_public', 'TRUE');
	    }
	    
	    $q->addSortField('Items.item_created');
	    
	    $result = $q->retrieve();
	    
	    /* if($draft_mode){
	        print_r($result);
	    } */
	    
	    /* $big_objects = array();
	    
	    foreach($result as $i){
	        $big_objects[] = SmartestCmsItem::retrieveByPk($i->getId());
	    } */
	    
	    return $result;
	    
	}
	
	public function getRelatedItemsAsArrays($draft_mode=false, $model_id=''){
	    
	    $items = $this->getRelatedItems($draft_mode, $model_id);
	    $arrays = array();
	    
	    foreach($items as $i){
	        $arrays[] = $i->__toArray(true);
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedItemIds($draft_mode=false, $model_id=''){
	    
	    $items = $this->getRelatedItems($draft_mode, $model_id);
	    $ids = array();
	    
	    foreach($items as $i){
	        $ids[] = $i->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function addRelatedItem($item_id){
	    
	    $item_id = (int) $item_id;
	    
	    $link = new SmartestManyToManyLookup;
	    $link->setEntityForeignKeyValue(2, $this->_properties['id']);
	    $link->setEntityForeignKeyValue(1, $item_id);
	    $link->setType('SM_MTMLOOKUP_PAGES_ITEMS');
	    
	    $link->save();
	}
	
	public function removeRelatedItem($item_id){
	    
	    $item_id = (int) $item_id;
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGES_ITEMS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    $q->addForeignTableConstraint('Items.item_id', $item_id);
	    
	    $q->delete();
	}
	
	public function removeAllRelatedItems($model_id=''){
	    
	    $model_id = (int) $model_id;
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGES_ITEMS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    
	    if($model_id > 0){
	        $q->addForeignTableConstraint('Items.item_itemclass_id', $model_id);
        }
        
	    $q->delete();
	}
	
	public function getNavigationStructure($draft_mode=false){
		
		$home_page_id = $this->getParentSite()->getTopPageId();
		$home_page = new SmartestPage;
		$home_page->hydrate($home_page_id);
		
		$this->getGrandParentPage();
		
		return array(
		    // TODO: add code here that will fetch "related" pages.
			"parent"=>$this->getParentPage()->compile(), 
			"section"=>$this->getSectionPage()->compile(), 
			"breadcrumbs"=>$this->getPageBreadCrumbs(), 
			"sibling_level_lages"=>$this->getParentPage()->getPageChildrenForWebAsArrays($draft_mode), 
			"parent_level_pages"=>$this->getGrandParentPage()->getPageChildrenForWebAsArrays($draft_mode),
			"child_pages"=>$this->getPageChildrenForWebAsArrays($draft_mode),
			"main_sections"=>$home_page->getPageChildrenForWebAsArrays($draft_mode, true),
			"related"=>$this->getRelatedContentForRender($draft_mode)
		);
	}
	
	public function loadAssetClassDefinitions($draft_mode=false){
	    
	    if($draft_mode){
	        $sql = "SELECT * FROM Assets, AssetClasses, AssetIdentifiers WHERE AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetIdentifiers.assetidentifier_page_id='".$this->_properties['id']."' AND AssetIdentifiers.assetidentifier_draft_asset_id=Assets.asset_id";
        }else{
            $sql = "SELECT * FROM Assets, AssetClasses, AssetIdentifiers WHERE AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetIdentifiers.assetidentifier_page_id='".$this->_properties['id']."' AND AssetIdentifiers.assetidentifier_live_asset_id=Assets.asset_id";
        }
        
        $result = $this->database->queryToArray($sql);
        
        // echo $sql;
        
        // print_r($result);
        
        foreach($result as $def_array){
            if($def_array['assetclass_type'] == 'SM_ASSETCLASS_CONTAINER'){
                $def = new SmartestContainerDefinition;
                $def->hydrateFromGiantArray($def_array);
                $this->_containers[$def_array['assetclass_name']] = $def;
            }else{
                $def = new SmartestPlaceholderDefinition;
                $def->hydrateFromGiantArray($def_array);
                $this->_placeholders[$def_array['assetclass_name']] = $def;
            }
        }
	    
	}
	
	public function loadItemSpaceDefinitions($draft_mode=false){
	    
	    if($draft_mode){
	        $sql = "SELECT * FROM Items, AssetClasses, AssetIdentifiers WHERE AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetIdentifiers.assetidentifier_page_id='".$this->_properties['id']."' AND AssetIdentifiers.assetidentifier_draft_asset_id=Items.item_id";
	    }else{
	        $sql = "SELECT * FROM Items, AssetClasses, AssetIdentifiers WHERE AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetIdentifiers.assetidentifier_page_id='".$this->_properties['id']."' AND AssetIdentifiers.assetidentifier_live_asset_id=Items.item_id";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    foreach($result as $def_array){
            $def = new SmartestItemSpaceDefinition;
            $def->hydrateFromGiantArray($def_array);
            // print_r($def);
            $this->_itemspaces[$def_array['assetclass_name']] = $def;
        }
	    
	}
	
	public function hasContainerDefinition($container_name, $draft_mode=false){
	    
	    return array_key_exists($container_name, $this->_containers);
	    
	}
	
	public function hasPlaceholderDefinition($placeholder_name, $draft_mode=false){
	    
	    return array_key_exists($placeholder_name, $this->_placeholders);
	    
	}
	
	public function hasItemSpaceDefinition($itemspace_name, $draft_mode=false){
	    
	    return array_key_exists($itemspace_name, $this->_itemspaces);
	    
	}
	
	public function getItemSpaceDefinitionNames(){
	    return array_keys($this->_itemspaces);
	}
	
	//// Authors and page credit
	
	public function getAuthors(){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGE_AUTHORS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    
	    $q->addSortField('Users.user_lastname');
	    
	    $result = $q->retrieve();
	    
	    return $result;
	    
	}
	
	public function getAuthorsAsArrays(){
	    
	    $authors = $this->getAuthors();
	    $arrays = array();
	    
	    foreach($authors as $a){
	        $arrays[] = $a->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getAuthorIds(){
	    
	    $authors = $this->getAuthors();
	    $ids = array();
	    
	    foreach($authors as $a){
	        $ids[] = $a->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function addAuthorById($user_id){
	    
	    $user_id = (int) $user_id;
	    
	    $link = new SmartestManyToManyLookup;
	    $link->setEntityForeignKeyValue(2, $this->_properties['id']);
	    $link->setEntityForeignKeyValue(1, $user_id);
	    $link->setType('SM_MTMLOOKUP_PAGE_AUTHORS');
	    
	    $link->save();
	    
	}
	
	public function removeAuthorById($item_id){
	    
	    $user_id = (int) $user_id;
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGE_AUTHORS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    $q->addForeignTableConstraint('Users.user_id', $user_id);
	    
	    $q->delete();
	    
	}
	
	public function getContainerDefinition($container_name, $draft_mode=false){
	    
	    if(array_key_exists($container_name, $this->_containers)){
	        
	        $container = $this->_containers[$container_name];
	        return $container;
	        
	    }else{
	    
	        $container = new SmartestContainerDefinition;
            $container->load($container_name, $this, $draft_mode);
            return $container;
        
        }
	    
	}
	
	public function getContainerDefinitionNames(){
	    return array_keys($this->_containers);
	}
	
	public function getPlaceholderDefinition($placeholder_name, $draft_mode=false){
	    
	    if(array_key_exists($placeholder_name, $this->_placeholders)){
	        
	        $placeholder = $this->_placeholders[$placeholder_name];
	        return $placeholder;
	        
	    }else{
	    
	        $placeholder = new SmartestPlaceholderDefinition;
            $placeholder->load($placeholder_name, $this, $draft_mode);
            return $placeholder;
        
        }
	    
	}
	
	public function getPlaceholderDefinitionNames(){
	    return array_keys($this->_placeholders);
	}
	
	public function getItemSpaceDefinition($itemspace_name, $draft_mode=false){
	    
	    // print_r($this->_itemspaces);
	    // echo $itemspace_name;
	    
	    if(array_key_exists($itemspace_name, $this->_itemspaces)){
	        
	        $itemspace = $this->_itemspaces[$itemspace_name];
	        // print_r($itemspace);
	        return $itemspace;
	        
	    }else{
	    
	        $itemspace = new SmartestItemSpaceDefinition;
            $itemspace->load($itemspace_name, $this, $draft_mode);
            return $itemspace;
            
            // throw new SmartestException("itemspace name '".$itemspace_name."' not recognized.");
        
        }
	    
	}
	
	public function getRelatedContentForRender($draft_mode=false){
	    
	    $content = array();
	    
	    $du = new SmartestDataUtility;
        $models = $du->getModels();
    
        foreach($models as $m){
            $key = SmartestStringHelper::toVarName($m->getPluralName());
            $content[$key] = $this->getRelatedItemsAsArrays($draft_mode, $m->getId());
        }
        
        $content['pages'] = $this->getRelatedPagesAsArrays($draft_mode);
        
        return $content;
        
	}
	
	public function getPageBreadCrumbs(){
		
		$home_page = $this->getParentSite()->getHomePage();
		$breadcrumbs = array();
		
		$limit = self::HIERARCHY_DEPTH_LIMIT;
		
		$page_id = $this->_properties['id'];
		
		while($home_page->getId() != $page_id && $limit > 0){
			$page = new SmartestPage;
			$page->hydrate($page_id);
			$breadcrumbs[] = $page->__toArray();
			$page_id = $page->getParent();
			$limit--;
		}
		
		$breadcrumbs[] = $home_page->__toArray();
		
		krsort($breadcrumbs);
		$result = array_values($breadcrumbs);
		
		return $result;
		
	}
	
	public function getSectionPage(){
	    if(SmartestStringHelper::isFalse($this->getIsSection()) && !$this->isHomePage()){
	        if(!$this->_section_page){
	            
	            $page = $this->getParentPage();
	            
	            $limit = self::HIERARCHY_DEPTH_LIMIT;
	            
	            while($limit > 0){
	                
	                if(SmartestStringHelper::toRealBool($page->isHomePage()) || SmartestStringHelper::toRealBool($page->getIsSection())){
	                    $section_page = $page;
	                    break;
	                }else{
	                    $page = $page->getParentPage();
	                }
	                
	                $limit--;
	            }
	            
	            $this->_section_page = $section_page;
	            return $section_page;
	            
	        }else{
	            return $this->_section_page;
	        }
        }else{
            return $this;
        }
	}
	
	public function getSite(){
	    
	    if(!SmartestPersistentObject::get('__current_host_site')){
	        $sql = "SELECT * FROM Sites WHERE site_id='".$this->_properties['site_id']."'";
	        $result = $this->database->queryToArray($sql);
	        $s = new SmartestSite;
	        $s->hydrate($result[0]);
	        SmartestPersistentObject::set('__current_host_site', $s);
	    }
	    
	    return SmartestPersistentObject::get('__current_host_site');
	    
	}
	
	public function getParentSite(){
	    
	    if(!$this->_parent_site){
	        $sql = "SELECT * FROM Sites WHERE Sites.site_id='".$this->_properties['site_id']."'";
            $result = $this->database->queryToArray($sql);
            $s = new SmartestSite;
            $s->hydrate($result[0]);
            $this->_parent_site = $s;
        }
        
        return $this->_parent_site;
	}
	
	public function getFormattedTitle(){
		
		$format = $this->getParentSite()->getTitleFormat();
		
		$title = str_replace('$page', $this->getTitle(), $format);
		
		$separator = $this->getParentSite()->getTitleFormatSeparator();
		
		if(SmartestStringHelper::isFalse($this->getIsSection())){
		    
		    $section_page = $this->getSectionPage();
		    
		    if($section_page->isHomePage()){
		        $title = preg_replace(SmartestStringHelper::toRegularExpression($separator.' $section', true), '', $title);
		    }else{
		        $title = preg_replace(SmartestStringHelper::toRegularExpression($separator.' $section', true), $separator.' '.$section_page->getTitle(), $title);
	        }
	        
		}else{
		    $title = preg_replace(SmartestStringHelper::toRegularExpression($separator.' $section', true), '', $title);
		}
		
		if($this->isTagPage() && is_object($this->_tag)){
		    $title .= ' '.$separator.' '.$this->_tag->getLabel();
	    }
	    
	    $title = str_replace('$site', $this->getParentSite()->getName(), $title);
	    
	    return $title;
	}
	
	public function getCacheFileName(){
	    
	    switch($this->getCacheInterval()){
	        
			case "MONTHLY":
			$page_cache_name = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id']."_m".date("m");
			break;
			
			case "DAILY":
			$page_cache_name = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id']."_m".date("m")."_d".date("d");
			break;
			
			case "HOURLY":
			$page_cache_name = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id']."_m".date("m")."_d".date("d")."_H".date("H");
			break;
			
			case "MINUTE":
			$page_cache_name = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id']."_m".date("m")."_d".date("d")."_H".date("H")."_i".date("i");
			break;
			
			case "SECOND":
			$page_cache_name = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id']."_m".date("m")."_d".date("d")."_H".date("H")."_i".date("i")."_s".date("s");
			break;
			
			case "PERMANENT":
			default:
			$page_cache_name = "site".$this->_properties['site_id']."_cms_page_".$this->_properties['id'];
			break;
			
		}
		
		if($this->getType() == "ITEMCLASS" && $this->_principal_item){
			$page_cache_name .= "__id".$this->_principal_item->getId();
		}
		
		return $page_cache_name.'.html';
		
	}

}