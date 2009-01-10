<?php

//////////////// REQUIRED FOR CMS OPERATION - DO NOT EDIT //////////////////

class CmsFrontEndManager{

	private $database;
	
	function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
	}
	
	function getSiteByDomain($domain){
	    
	    $sql = "SELECT * FROM Sites WHERE site_domain='".$domain."'";
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result)){
	        $site = new SmartestSite;
	        $site->hydrate($result[0]);
	        return $site;
	    }else{
	        return false;
	    }
	    
	}
	
	function getNormalPageByUrl($url, $site_id){
		
		$sql = "SELECT Pages.* FROM Pages, PageUrls WHERE Pages.page_id=PageUrls.pageurl_page_id AND page_type='NORMAL' AND Pages.page_site_id='".$site_id."' AND PageUrls.pageurl_url='$url' AND Pages.page_is_published='TRUE' AND Pages.page_deleted !='TRUE'";
		$page = $this->database->queryToArray($sql);
		
		$p = new SmartestPage;
		
		if(count($page) > 0){
			$p->hydrate($page[0]);
			return $p;
		}else{
			return null;
		}
		
	}
	
	function getNormalPageByWebId($web_id, $site_id, $draft_mode=false){
	    
	    $sql = "SELECT * FROM Pages WHERE page_webid='".$web_id."' AND page_type='NORMAL'";
	    
	    if(!$draft_mode){
	        $sql .= " AND page_is_published='TRUE'";
        }
	    
	    $sql .= " AND page_deleted !='TRUE'";
	    
	    $page = $this->database->queryToArray($sql);
	    
	    $pageObj = new SmartestPage;
	    
	    if(count($page) > 0){
			// print_r($page[0]);
			$pageObj->hydrate($page[0]);
			if($draft_mode){
			    $pageObj->setDraftMode(true);
			}
			return $pageObj;
		}else{
			return null;
		}
	    
	}
	
	function getItemClassPageByWebId($web_id, $item_id, $site_id, $draft_mode=false){
	    
	    $sql = "SELECT * FROM Pages WHERE page_webid='".$web_id."' AND page_type='ITEMCLASS'";
	    
	    if(!$draft_mode){
	        $sql .= " AND page_is_published='TRUE'";
        }
	    
	    $sql .= " AND page_deleted !='TRUE'";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result) > 0){
			
			$page = new SmartestItemPage;
			$page->hydrate($result[0]);
			
			if($draft_mode){
			    $page->setDraftMode(true);
			}
			
			if(is_numeric($item_id)){
			    $page->setIdentifyingFieldName("id");
			}else{
			    $page->setIdentifyingFieldName("webid");
		    }
		    
			$page->setIdentifyingFieldValue($item_id);
			
			if($page->isAcceptableItem($draft_mode)){
			    // the item id was ok. get the item
			    $page->assignPrincipalItem();
			    return $page;
			    
			}else{
			    // the item was not in the set, so I guess it's a 404
			    SmartestLog::getInstance('system')->log("Unacceptable item ID: $item_id requested while trying to build Page ID: ".$page->getId());
			}
		    
		}else{
			return null;
		}
	    
	}
	
	function getItemClassPageByUrl($url, $site_id){
		
		$sql = "SELECT Pages.page_id, Pages.page_webid, Pages.page_name, PageUrls.pageurl_url FROM Pages, PageUrls WHERE Pages.page_type='ITEMCLASS' AND Pages.page_site_id='".$site_id."' AND Pages.page_id = PageUrls.pageurl_page_id AND Pages.page_is_published='TRUE' AND Pages.page_deleted !='TRUE'";
		
		// echo $sql;
		
		$dataset_pages = $this->database->queryToArray($sql);
		
		if(is_array($dataset_pages)){
			
			$found_page = false;
			
			// loop through dataset pages and urls and check the urls against the current one
			foreach($dataset_pages as $page_record){
			    
			    $page_url_regexp = $this->convertPageUrlToRegExp($page_record["pageurl_url"]);
			    
			    // if the stored url being checked matches the current one
			    if(preg_match($page_url_regexp, $url, $matches)){
				    
				    // create the page object
				    $page = new SmartestItemPage;
				    
				    // hydrate it
				    if($page->hydrate($page_record['page_id'])){
				        
				        // $template_url_parts = explode("/", $page_record["pageurl_url"]);
				        $template_url_parts = preg_split("/[\.\/]/", $page_record["pageurl_url"]);
				        
				        // print_r($template_url_parts);
				        
    					$actual_url_parts = $matches;
    					
    					array_shift($actual_url_parts);
    					
    					$i = 0;

    					foreach($template_url_parts as $key => $url_placeholder){
    					    
    					    // print_r($url_placeholder.'<br />');
    					    
    					    // if($i = count($template_url_parts) - 1){
    					    //     $regex = '/^(\$|:)([\w_]+)(\.\w+)?/';
    					    // }else{
    					        $regex = '/^(\$|:)([\w_]+)/';
    					    // }
    					    
    						if(preg_match($regex, $url_placeholder, $url_var_matches)){
    							// unset($template_url_parts[$key]);
    							
    							if($url_placeholder == ":id"){
    							    $page->setIdentifyingFieldName("id");
    							    $page->setIdentifyingFieldValue($actual_url_parts[$i]);
    							}else if($url_placeholder == ":name"){
    							    $page->setIdentifyingFieldName("slug");
    							    $page->setIdentifyingFieldValue($actual_url_parts[$i]);
    							}else if($url_placeholder == ":long_id"){
        							$page->setIdentifyingFieldName("webid");
        							$page->setIdentifyingFieldValue($actual_url_parts[$i]);
        						}
        						
        						// echo 
        						
        						$page->setUrlNameValuePair($url_var_matches[2], $actual_url_parts[$i]);

        						$i++;
    							
    						}
    						
    					}
    					
    					// print_r($page);
    					
    					if($page->isAcceptableItem()){
    					    // the item id was ok. get the item
    					    // print_r($page);
    					    $page->assignPrincipalItem();
    					    return $page;
    					}else{
    					    // print_r($page);
    					    // the item was not in the set, so I guess it's a 404
    					    return false;
    					}
    					
    					
    					
    					if(!$page->getIdentifyingFieldName()){
    					    // error 404
    					    return false;
    					}
				        
				    }else{
				        // the page id attached to the stored page url doesn't exist or couldn't be hydrated
				        // 404
				        return false;
				    }
					
				} 
	
			} 
			
			return false;
		}else{
			return false;
		}
	}
	
	function getSiteId(){
		$sql = "SELECT site_id FROM Sites ORDER BY site_id ASC LIMIT 1";
		$site = $this->database->queryToArray($sql);
		$site_id = $site[0]["site_id"];
		return $site_id;
	}
	
	function getHomePage(){
		
		$site_id = $this->getSiteId();
		$sql = "SELECT site_top_page_id FROM Sites WHERE site_id='$site_id' ORDER BY site_id ASC LIMIT 1";
		$site = $this->database->queryToArray($sql);
		
		$home_page_id = $site[0]["site_top_page_id"];
		$sql = "SELECT * FROM Pages WHERE page_id='$home_page_id' LIMIT 1";
		$page = $this->database->queryToArray($sql);
		$page = $page[0];
		return $page;
		
	}
	
	protected function convertPageUrlToRegExp($url){
		
		$url = str_replace("/", "\/", $url);
		// $url = preg_replace('/(\$[\w_-]+)/', "([^\/\s]+)", $url);
		$url = str_replace(':id', "(\d+)", $url);
		// $url = str_replace(':item_id', "(\d+)", $url);
		// $url = str_replace(':itemid', "(\d+)", $url);
		$url = str_replace(':long_id', "(\w{32})", $url);
		// $url = str_replace(':item_webid', "(\w{32})", $url);
		// $url = str_replace(':item_web_id', "(\w{32})", $url);
		// $url = str_replace(':webid', "(\w{32})", $url);
		// $url = str_replace(':web_id', "(\w{32})", $url);
		$url = str_replace(':name', "([^\/\s]+)", $url);
		// $url = preg_replace('/(:[\w_-]+)/', "([^\/\s]+)", $url);
		
		// $url .= '(\.[\w]+)?';
		$url = "/^".$url."\/?$/i";
		
		// print_r($url);
		
		return $url;
	}
	
	function getPageFields($page_id, $version="live"){
		$sql = "SELECT DISTINCT PagePropertyValues.*, Pages.page_id, PageProperties.pageproperty_id, PageProperties.pageproperty_name FROM Pages, PageProperties, PagePropertyValues, Sites
WHERE page_id='$page_id' 
AND pageproperty_site_id = page_site_id 
AND site_id = page_site_id 
AND pagepropertyvalue_pageproperty_id = pageproperty_id
AND pagepropertyvalue_page_id = page_id";
		
		$field = ($version == "draft") ? "pagepropertyvalue_draft_value" : "pagepropertyvalue_live_value";
		$raw_properties = $this->database->queryToArray($sql);
		$properties = array();
		
		foreach($raw_properties as $property){
			$properties[$property['pageproperty_name']] = $property[$field];
		}
		
		// print_r($properties);
		
		return $properties;
		
	}
	
	function getPageById($page_id){
		
		if(is_numeric($page_id) && strlen($page_id) < 11){
			$field = "page_id";
		}else{
			$field = "page_webid";
		}
		
		$home_page = $this->getHomePage();
		$home_page_id = $home_page['page_id'];
		
		$sql = "SELECT * FROM Pages WHERE $field='$page_id'";
		$page = $this->database->queryToArray($sql);
		$page = $page[0];
		
		if($page['page_id'] != $home_page_id){
			$page['page_url'] = $this->getPageUrl($page['page_id']);
		}
		
		return $page;
	}
	
	function getPageUrl($page_id){
	
		if(is_numeric($page_id) && strlen($page_id) < 11){
			$field = "page_id";
		}else{
			$field = "page_webid";
		}
		
		$sql = "SELECT pageurl_url FROM Pages, PageUrls WHERE Pages.$field='$page_id' AND PageUrls.pageurl_page_id=Pages.page_id";
		$page = $this->database->queryToArray($sql);
		
		if(count($page) > 0){
			return $page[0]['pageurl_url'];
		}else{
			return "website/renderPageFromId?page_id=".$this->database->specificQuery("page_webid", $field, $page_id, "Pages");
		}
	
	}
	
	function getSiteInfo($site_id){
		$sql = "SELECT * FROM Sites WHERE site_id='$site_id'";
		$site = $this->database->queryToArray($sql);
		return $site[0];
	}
	
	function getNavigationStructure($page_id){
		
		$site_id = $this->getSiteId();
		$sql = "SELECT site_top_page_id FROM Sites WHERE site_id='$site_id'";
		$result = $this->database->queryToArray($sql);
		$home_page_id = $result[0]['site_top_page_id'];
		$page_parent_id = $this->database->specificQuery("page_parent", "page_id", $page_id, "Pages");
		$page_grandparent_id = $this->database->specificQuery("page_parent", "page_id", $page_parent_id, "Pages");
		
		return array(
			"parent"=>$this->getPageById($page_parent_id), 
			"breadcrumbs"=>$this->getPageBreadCrumbs($page_id), 
			"siblingLevelPages"=>$this->getChildLevelPages($page_parent_id, $page_id), 
			"parentLevelPages"=>$this->getChildLevelPages($page_grandparent_id, $page_parent_id),
			"childPages"=>$this->getChildLevelPages($page_id),
			"sectionPages"=>$this->getChildLevelPages($home_page_id)
		);
	}
	
	function getChildLevelPages($page_id, $selection=""){
		
		$sql = "SELECT Pages.page_id FROM Pages WHERE Pages.page_parent='$page_id' AND Pages.page_is_published = 'TRUE'";
		$result = $this->database->queryToArray($sql);
		$pages = array();
		
		foreach($result as $page){
			$pages[] = $this->getPageById($page['page_id']);
		}
		
		if(is_numeric($selection)){
			foreach($pages as $key=>$page){
				if($page['page_id'] == $selection){
					$pages[$key]['selected'] = 'TRUE';
				}else{
					$pages[$key]['selected'] = 'FALSE';
				}
			}
		}
		
		return $pages;
	}
	
	function getPageBreadCrumbs($page_id){
		
		$site_id = $this->getSiteId();
		$sql = "SELECT site_top_page_id FROM Sites WHERE site_id='$site_id'";
		$result = $this->database->queryToArray($sql);
		$home_page_id = $result[0]['site_top_page_id'];
		$breadcrumbs = array();
		
		$limit = 20;
		
		while($home_page_id != $page_id && $limit > 0){
			$page = $this->getPageById($page_id);
			$breadcrumbs[] = $page;
			$page_id = $page['page_parent'];
			$limit--;
		}
		
		$breadcrumbs[] = $this->getPageById($home_page_id);
		
		krsort($breadcrumbs);
		$result = array_values($breadcrumbs);
		
		return $result;
		
	}
	
	function getPageTitle($page, $site){
		$format = $site["site_title_format"];
		$half_way = str_replace('$page', $page["page_title"], $format);
		$title = str_replace('$site', $site["site_name"], $half_way);
		return $title;
	}
	
	function defineCurrentPage($page){
		if(@$page['page_name'] && !defined("SM_PAGE_NAME")){
			define('SM_PAGE_NAME', $page['page_name']);
			define('SM_PAGE_ID', $page['page_id']);
			define('SM_PAGE_WEBID', $page['page_webid']);
			define('SM_PAGE_CACHE_AS_HTML', $page['page_cache_as_html']);
			define('SM_PAGE_TYPE', $page['page_type']);
			define('SM_PAGE_TITLE', $page['page_title']);
			define('SM_PAGE_FULL_TITLE', $this->getPageTitle($page, $this->getSiteInfo($page['page_site_id'])));
			define('SM_PAGE_PARENT_ID', $page['page_parent']);
			define('SM_PAGE_PARENT_WEBID', $this->database->specificQuery("page_webid", "page_id", $page['page_parent'], "Pages"));
			
			switch($page['page_cache_interval']){
				case "PERMANENT":
				$page_cache_name = "smartest_page_".SM_PAGE_ID;
				break;
				case "MONTHLY":
				$page_cache_name = "smartest_page_".SM_PAGE_ID."_m".date("m");
				break;
				case "DAILY":
				$page_cache_name = "smartest_page_".SM_PAGE_ID."_m".date("m")."_d".date("d");
				break;
				case "HOURLY":
				$page_cache_name = "smartest_page_".SM_PAGE_ID."_m".date("m")."_d".date("d")."_H".date("H");
				break;
				case "MINUTE":
				$page_cache_name = "smartest_page_".SM_PAGE_ID."_m".date("m")."_d".date("d")."_H".date("H")."_i".date("i");
				break;
				case "SECOND":
				$page_cache_name = "smartest_page_".SM_PAGE_ID."_m".date("m")."_d".date("d")."_H".date("H")."_i".date("i")."_s".date("s");
				break;
				// 'MONTHLY', 'DAILY', 'HOURLY', 'MINUTE', 'SECOND'
			}// .".html"
			
			if($page['page_type'] == "ITEMCLASS"){
				$page_cache_name .= "__id".SM_PAGE_ITEM_ID;
			}
			
			$page_cache_name .= ".html";
			
			define('SM_PAGE_CACHE_NAME', $page_cache_name);
		}
	}

}