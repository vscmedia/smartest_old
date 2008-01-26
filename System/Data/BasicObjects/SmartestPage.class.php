<?php

class SmartestPage extends SmartestDataObject{

	protected $_save_url = true;
	protected $_fields_retrieval_attempted = false;
	protected $_child_pages = array();
	protected $_child_pages_retrieved = false;
	protected $_grandparent_page = null;
	protected $_parent_page = null;
	protected $_urls = array();
	protected $_fields = array();
	protected $displayPagesIndex = 0;
	protected $displayPages = array();
	protected $_site;
	
	const NOT_CHANGED = 100;
	const AWAITING_APPROVAL = 101;
	const CHANGES_APPROVED = 102;
	const NOT_PUBLIC = 103;
	
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
	
	public function save(){
		
		parent::save();
		
		// Add URL
		if(!$this->_save_url){
			// TODO: Figure out how to handle the multiple URLS. which one is geing saved? See Bug #75
		}else{
			// for new pages it's not an issue
			$sql = "INSERT INTO PageUrls (pageurl_page_id, pageurl_url) VALUES ('".$this->getId()."', '".$this->getUrl()."')";
			$this->database->rawQuery($sql);
			$this->_save_url = false;
		}
		
		// Add definitions from preset, if any
		if($this->getPreset() && $this->getId()){
			
			$preset = new SmartestPageLayoutPreset;
			$preset->hydrate($this->getPreset());
			$defs = $preset->getDefinitions();
			
			foreach($defs as $definition){
				$sql = "INSERT INTO AssetIdentifiers (assetidentifier_draft_asset_id, assetidentifier_assetclass_id, assetidentifier_page_id) VALUES ('".$definition['plpd_asset_id']."', '".$definition['plpd_assetclass_id']."', '".$this->getId()."')";
			}
		}
	}
	
	public function delete($remove=false){
	    if($remove){
		    $sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->getId()."' LIMIT 1";
		    $this->database->rawQuery($sql);
		    $this->_came_from_database = false;
	    }else{
	        $this->setField('Deleted', 1);
	        $this->save();
	    }
	}
	
	public function addUrl($url_string){
	    $url = new SmartestPageUrl;
	    $url->setUrl($url_string);
	    $url->setPageId($this->getId());
	    $url->save();
	}
	
	public function getAssociatedObjects(){
	    $sql = "SELECT pageitemlookup_item_id FROM PagesItemsLookup WHERE pageitemlookup_page_id=".$this->getId()."";
	}
		
	public function publish(){
	    
	    // update database defs
		$sql = "UPDATE Lists SET list_live_set_id=list_draft_set_id, list_live_template_file=list_draft_template_file, list_live_header_template=list_draft_header_template, list_live_footer_template=list_draft_footer_template WHERE list_page_id='".$this->getId()."'";
		$this->database->rawQuery($sql);
		
		$sql = "UPDATE AssetIdentifiers SET assetidentifier_live_asset_id=assetidentifier_draft_asset_id, assetidentifier_live_render_data=assetidentifier_draft_render_data WHERE assetidentifier_page_id='".$this->getId()."'";
		$this->database->rawQuery($sql);
		
		$sql = "UPDATE PagePropertyValues SET pagepropertyvalue_live_value=pagepropertyvalue_draft_value WHERE pagepropertyvalue_page_id='".$this->getId()."'";
		$this->database->rawQuery($sql);
		
		$this->setLiveTemplate($this->getDraftTemplate());
		$this->setLastPublished(time());
		$this->setIsPublished('TRUE');
		$this->save();
		
		// now delete files in page cache?
		$cache_files = SmartestFileSystemHelper::load(SM_ROOT_DIR."System/Cache/Pages/");
		
		$cf_start = "site".$this->getSiteId()."_cms_page_".$this->getId();
		
		foreach($cache_files as $f){
		    if(substr($f, 0, strlen($cf_start)) == $cf_start){
		        unlink(SM_ROOT_DIR."System/Cache/Pages/".$f);
		    }
		}
		
	}
	
	public function unpublish(){
		$this->setIsPublished('FALSE');
		$this->save();
	}
	
	public function getElementsAsList(){
		
	}

	public function getElementsAsTree(){
		
	}
	
	public function getPagesSubTree($level=1, $draft_mode=false, $get_items){
	
		$working_array = array();
		$index = 0;
		
		// print_r(get_class_methods($this));
		
		// var_dump($draft_mode);
		
		$_children = $this->getPageChildren($draft_mode);
		
		// echo 'called';
		
		// print_r($_children);
		
		// echo count($_children).":\n";
		
		$int_level = (int) $level;
		// echo $int_level;
		
		foreach($_children as $child_page_record){
			
			if($child_page_record['page_type'] == 'ITEMCLASS'){
			    
			    $child = new SmartestItemPage;
			    
		    }else{
		        
		        $child = new SmartestPage;
		        
		    }
		    
		    $child->hydrate($child_page_record);
			// echo $child->getId().', ';
			// echo $child['page_id'].', ';
			// print_r($child->__toArray());
			$working_array[$index]["info"] = $child->__toArray();
			$working_array[$index]["treeLevel"] = $int_level;
			$new_level = $int_level + 1; // echo $new_level;
			$working_array[$index]["children"] = $child->getPagesSubTree($new_level, $draft_mode, $get_items);
			
			if($child->getType() == "ITEMCLASS" && $get_items){
			    $set = $child->getDataSet();
			    // print_r($set);
			    $working_array[$index]["child_items"] = $set->getMembersAsArrays();
			}else{
			    $working_array[$index]["child_items"] = array();
			}
			
			$index++;
			
		}
		
		// echo "finished";
		
        //	echo '<br />';
		
		// print_r($working_array);
		
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
	    
	    $sql = "SELECT * FROM Assets WHERE asset_type IN ('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE') AND (asset_site_id='".$this->getSiteId()."' OR asset_shared=1) AND asset_deleted!=1 ORDER BY asset_url";
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
    	        // No urls have been defined. Return a dynamic one.
    	        $url = 'website/renderPageFromId?page_id='.$this->getWebid();
    	    }
	    
        // }
	    
	    return $url;
	}
	
	public function getUrls(){
		
		if(!count($this->_urls)){
		
		    $sql = "SELECT * FROM PageUrls WHERE pageurl_page_id = '".$this->getId()."'";
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

	public function getPageChildren($draft_mode=false){
	    
	    $sql = "SELECT DISTINCT * FROM Pages WHERE page_parent='".$this->getId()."' AND page_site_id='".$this->getSiteId()."' AND page_deleted != 'TRUE'";
		
		if(!$draft_mode){
		    $sql .= " AND page_is_published = 'TRUE'";
		}
		
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
	        
	    return $result;
	}
	
	public function getPageChildrenAsArrays($draft_mode=false){
	    
	    $children = $this->getPageChildren($draft_mode);
	    $array = array();
	    
	    foreach($children as $child_page_record){
	        $child_page = new SmartestPage;
	        $child_page->hydrate($child_page_record);
	        $array[] = $child_page->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getPageFields(){
	    
	    // if(!$this->_fields_retrieval_attempted){
	    
	    $sql = "SELECT * FROM `PageProperties` WHERE pageproperty_site_id='".$this->getSiteId()."'";
	    $result = $this->database->queryToArray($sql);
        
        // print_r($result);
        // var_dump($this->getSiteId());
        // echo $sql;
        
	    foreach($result as $p){
	        $property = new SmartestPageField;
	        $property->hydrate($p);
	        $this->_fields[$property->getId()] = $property;
	    }
    
	    $sql = "SELECT * FROM `PagePropertyValues` WHERE pagepropertyvalue_page_id='".$this->getId()."'";
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
                $this->_fields[$fid]->setContextualPageId($this->getId());
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
	    
	    if($this instanceof SmartestItemPage){
	        if($this->getPrincipalItem()){
	            $data['principal_item'] = $this->getPrincipalItem()->__toArray();
	            $data['sibling_items'] = $this->getDataSet()->getMembersAsArrays();
	            $data['data_set'] = $this->getDataSet()->__toArray();
	            $data['is_item'] = true;
            }else{
                $data['principal_item'] = array();
                
                if($this->getDataSet() instanceof SmartestCmsItemSet){
                    $data['sibling_items'] = $this->getDataSet()->getMembersAsArrays();
                    $data['data_set'] = $this->getDataSet()->__toArray();
                }else{
                    $data['sibling_items'] = array();
                    $data['data_set'] = array();
                }
                
                $data['is_item'] = true;
            }
	    }else{
	        $data['is_item'] = false;
	    }
	    
	    $du = new SmartestDataUtility;
	    $tags = $du->getTagsAsArrays();
	    
	    $data['tags'] = $tags;
	    $data['fields'] = $this->getPageFieldValuesAsAssociativeArray($draft_mode);
	    $data['navigation'] = $this->getNavigationStructure($draft_mode);
	    
	    return $data;
	}
	
	public function isTagPage(){
	    // print_r($this->getSite());
	    // echo 'tpid:'.$this->getSite()->getTagPageId();
	    // echo 'id:'.$this->getId();
	    return ($this->getSite()->getTagPageId() == $this->getId());
	}
	
	public function isSearchPage(){
	    return ($this->getSite()->getSearchPageId() == $this->getId());
	}
	
	public function touch(){
	    if($this->_came_from_database == true){
	        $sql = "UPDATE Pages SET page_modified = '".time()."' WHERE Pages.page_id = '".$this->getId()."'";
	        $this->database->rawQuery($sql);
	    }
	}
	
	public function __toArray($getChildren=false, $require_site_lookup=true){
	    
	    $array = parent::__toArray();
	    
	    $array['url'] = $this->getDefaultUrl();
	    $array['formatted_title'] = $this->getFormattedTitle();
	    $array['is_tag_page'] = $this->isTagPage();
	    
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
		$site_id = $this->getSiteId();
		
		// FIRST GET A LIST OF ALL PAGES
		$all_pages = $this->getSite()->getPagesList(true);
		
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
			if(in_array($page_array["info"]["id"], $sub_page_ids) || $page_array["info"]["id"] == $this->getId()){
				unset($all_pages[$key]);
			}
		}
		
		return $all_pages;
	}
	
	public function clearTags(){
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->getId()."' AND taglookup_type='SM_PAGE_TAG_LINK'";
	    $this->database->rawQuery($sql);
	}
	
	public function getTagIdsArray(){
	    
	    $sql = "SELECT taglookup_tag_id FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->getId()."' AND taglookup_type='SM_PAGE_TAG_LINK'";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    
	    foreach($result as $tl){
	        if(!in_array($tl['taglookup_object_id'], $ids)){
	            $ids[] = $tl['taglookup_tag_id'];
	        }
	    }
	    
	    return $ids;
	    
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
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->getId()."', 'SM_PAGE_TAG_LINK')";
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
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->getId()."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_PAGE_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	function getNavigationStructure($draft_mode=false){
		
		// $site_id = $this->getSiteId();
		// $sql = "SELECT site_top_page_id FROM Sites WHERE site_id='$site_id'";
		// $result = $this->database->queryToArray($sql);
		// $home_page_id = $result[0]['site_top_page_id'];
		$home_page_id = $this->getSite()->getTopPageId();
		$home_page = new SmartestPage;
		$home_page->hydrate($home_page_id);
		// $page_parent_id = $this->database->specificQuery("page_parent", "page_id", $page_id, "Pages");
		
		$this->getGrandParentPage();
		
		return array(
			"parent"=>$this->getParentPage()->compile(), 
			"breadcrumbs"=>$this->getPageBreadCrumbs(), 
			"sibling_level_lages"=>$this->getParentPage()->getPageChildrenAsArrays($draft_mode), 
			"parent_level_pages"=>$this->getGrandParentPage()->getPageChildrenAsArrays($draft_mode),
			"child_pages"=>$this->getPageChildrenAsArrays($draft_mode),
			"section_pages"=>$home_page->getPageChildrenAsArrays($draft_mode)
		);
	}
	
	public function getPageBreadCrumbs(){
		
		$home_page_id = $this->getSite()->getHomePage()->getId();
		$breadcrumbs = array();
		
		$limit = 20;
		
		$page_id = $this->getId();
		
		while($home_page_id != $page_id && $limit > 0){
			// $page = $this->getPageById($page_id);
			// echo $home_page_id.'/'.$page_id.'<br />';
			$page = new SmartestPage;
			$page->hydrate($page_id);
			$breadcrumbs[] = $page->__toArray();
			$page_id = $page->getParent();
			$limit--;
		}
		
		$breadcrumbs[] = $this->getSite()->getHomePage()->__toArray();
		
		krsort($breadcrumbs);
		$result = array_values($breadcrumbs);
		
		return $result;
		
	}
	
	public function getSite(){
	    
	    if(!SmartestPersistentObject::get('__current_host_site')){
	        $sql = "SELECT * FROM Sites WHERE site_id='".$this->getSiteId()."'";
	        $result = $this->database->queryToArray($sql);
	        $s = new SmartestSite;
	        $s->hydrate($result[0]);
	        SmartestPersistentObject::set('__current_host_site', $s);
	    } // $this->_site
	    
	    return SmartestPersistentObject::get('__current_host_site');
	    
	}
	
	public function getFormattedTitle(){
		
		$format = $this->getSite()->getTitleFormat();
		
		if($this->isTagPage() && is_object($this->_tag)){
		    $half_way = str_replace('$page', $this->getTitle().' | '.$this->_tag->getLabel(), $format);
	    }else{
	        $half_way = str_replace('$page', $this->getTitle(), $format);
	    }
	    
	    $title = str_replace('$site', $this->getSite()->getName(), $half_way);
	    
	    /* if($this->isTagPage() && is_object($this->_tag())){
		    $title .= ' | '.$this->_tag->getLabel();
		} */
		
		return $title;
	}
	
	public function getCacheFileName(){
	    
	    switch($this->getCacheInterval()){
	        
			case "MONTHLY":
			$page_cache_name = "site".$this->getSiteId()."_cms_page_".$this->getId()."_m".date("m");
			break;
			
			case "DAILY":
			$page_cache_name = "site".$this->getSiteId()."_cms_page_".$this->getId()."_m".date("m")."_d".date("d");
			break;
			
			case "HOURLY":
			$page_cache_name = "site".$this->getSiteId()."_cms_page_".$this->getId()."_m".date("m")."_d".date("d")."_H".date("H");
			break;
			
			case "MINUTE":
			$page_cache_name = "site".$this->getSiteId()."_cms_page_".$this->getId()."_m".date("m")."_d".date("d")."_H".date("H")."_i".date("i");
			break;
			
			case "SECOND":
			$page_cache_name = "site".$this->getSiteId()."_cms_page_".$this->getId()."_m".date("m")."_d".date("d")."_H".date("H")."_i".date("i")."_s".date("s");
			break;
			
			case "PERMANENT":
			default:
			$page_cache_name = "site".$this->getSiteId()."_cms_page_".$this->getId();
			break;
			
		}
		
		if($this->getType() == "ITEMCLASS" && $this->_principal_item){
			$page_cache_name .= "__id".$this->_principal_item->getId();
		}
		
		return $page_cache_name.'.html';
		
	}

}
