<?php

require_once(SM_ROOT_DIR.'System/Applications/Assets/AssetsManager.class.php');

class PagesManager{

	private $database;
	private $displayPages;
	private $displayPagesIndex;
	private $assetsManager;
	private $setsManager;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		$this->displayPages = array();
		$this->displayPagesIndex = 0;
		$this->displayAssetClasses = array();
		$this->displayAssetClassesIndex = 0;
		$this->assetsManager = new AssetsManager();
		// $this->setsManager = new SetsManager();
		
	}
	
	public function getAssetsManager(){
	    return $this->assetsManager;
	}
	
	public function getPageChildren($parent_page_id, $site_id){
		$sql = "SELECT DISTINCT * FROM Pages WHERE page_parent='$parent_page_id' AND page_site_id='$site_id' AND page_deleted != 'TRUE'";
		$results = $this->database->queryToArray($sql);
		return $results;
	}
	
	public function getPageIdFromPageWebId($page_webid){
		if(!is_numeric($page_webid)){
			return $this->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
		}else{
			return $page_webid;
		}
	}
	
	public function getAssetsByType(){
	    
	    $args = func_get_args();
	    
	    if(count($args)){
	        
	        // detect whether we are being passed an array of types, or a list of arguments
	        if(is_array($args[0][0])){
	            $types = $args[0][0];
	        }else if(is_array($args[0])){
	            $types = $args[0];
            }else{
                $types = $args;
            }
        }else{
            // no types were selected
            $types = array();
        }
        
        if(count($types)){
            
            $sql = "SELECT * FROM Assets WHERE asset_type IN (";
            
            foreach($types as $key => $t){
                
                if($key > 0){
                    $sql .= ', ';
                }
                
                $sql .= "'".$t."'";
                
            }
            
            $sql .= ') ORDER BY asset_stringid';
            
            $result = $this->database->queryToArray($sql);
            $official_types = SmartestDataUtility::getAssetTypes();
            $assets = array();
            
            foreach($result as $raw_asset){
                
                if(in_array($raw_asset['asset_type'], array_keys($official_types))){
                    
                    $try_class = $official_types[$raw_asset['asset_type']]['class'];
                    
                    if(class_exists($try_class)){
                        $class = $try_class;
                    }else{
                        $class = 'SmartestAsset';
                    }
                    
                }else{
                    $class = 'SmartestAsset';
                }
                
                $asset = new $class;
                $asset->hydrate($raw_asset);
                $assets[] = $asset;
                
            }
            
            return $assets;
            
        }else{
            
            return array();
            
        }
        
	}
	
	public function getAssetsByTypeAsArrays(){
	    
	    $types = func_get_args();
	    $objects = $this->getAssetsByType($types);
	    $array = array();
	    
	    foreach($objects as $asset){
	        $array[] = $asset->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getAssetsByTypeAsJson($separate=false){
	    
	    $types = func_get_args();
	    $objects = $this->getAssetsByType($types);
	    $array = array();
	    
	    foreach($objects as $asset){
	        $array[] = $asset->__toJson();
	    }
	    
	    return $separate ? $array : json_encode($array);
	    
	}
	
	public function getPagesTree($site_id, $startPageId='', $level=0){
		
		if(SmartestCache::hasData('site_pages_tree_'.$site_id, true)){
			
			$pageTree = SmartestCache::load('site_pages_tree_'.$site_id, true);
			
		}else{
		
			$_homepage_id = $this->database->specificQuery("site_top_page_id", "site_id", $site_id, "Sites");
		
			if(!@$startPageId){
				$startPageId = $_homepage_id;
			}
	
			$pageTree[0]["info"] = $this->getPage($startPageId);
			$pageTree[0]["treeLevel"] = $level;
			$pageTree[0]["children"] = $this->getPagesSubTree($startPageId, $site_id, $level+1);
			SmartestCache::save('site_pages_tree_'.$site_id, $pageTree, -1, true);
		
		}
		
		return $pageTree;
	}
	
	public function getPagesSubTree($startPageId, $site_id, $level=0){
	
		$workArray = array();
		$index = 0;
		
		$_children = $this->getPageChildren($startPageId, $site_id, $level);
		
		// print_r($_children);
		
		foreach($_children as $child){
			
			$workArray[$index]["info"] = $child;
			$workArray[$index]["treeLevel"] = $level;
			$workArray[$index]["children"] = $this->getPagesSubTree($child["page_id"], $site_id, $level+1);
			$index++;
			
		}
		
		return $workArray;
	}
	
	public function getSerialisedPageTree($pagesArray){
		
		
		
		foreach($pagesArray as $page){
			$this->displayPages[$this->displayPagesIndex]['info'] = $page['info'];
			$this->displayPages[$this->displayPagesIndex]['treeLevel'] = $page['treeLevel'];
			$children = $page['children'];
			
			$this->displayPagesIndex++;
			
			if(count($children) > 0){
				$this->getSerialisedPageTree($children);
			}
	
		}
		
		return $this->displayPages;
		
	}
	
	public function getSiteInfoFromId($site_id){
		$sql = "SELECT * FROM Sites WHERE Sites.site_id='".$site_id."'";
		$siteInfo = $this->database->queryToArray($sql);
		return $siteInfo[0];
	}
	
	public function getPagesList($site_id){
	    
	    $tree = $this->getPagesTree($site_id);
	    $raw_list = $this->getSerialisedPageTree($tree);
	    $list = array();
	    
	    foreach($raw_list as $page_array){
	        $p = $page_array['info'];
	        $p['tree_level'] = $page_array['treeLevel'];
	        $list[] = $p;
	    }
	    
	    return $list;
	    
	}
	
	function getOkParentPages($page_id){
		
		//// CODE TO GET LIST OF PAGES THAT ARE ACCEPTABLE AS PARENTS
		//// FOR THE SPECIFIED PAGE. I.E. NOT ITSELF OR ANY OF ITS CHILDREN
		
		$site_id = $this->database->specificQuery("page_site_id", "page_id", $page_id, "Pages");
		
		// FIRST GET A LIST OF ALL PAGES
		$all_pages = $this->getSerialisedPageTree($this->getPagesTree($site_id));
		$this->displayPages = array();
		$this->displayPagesIndex = 0;
		
		// THEN GET A LIST OF ALL CHILD PAGES
		$sub_pages = $this->getSerialisedPageTree($this->getPagesSubTree($page_id, $site_id));
		$this->displayPages = array();
		$this->displayPagesIndex = 0;
		
		$all_page_ids = array();
		$sub_page_ids = array();
		
		// MAKE A SIMPLE ARRAY OF ALL THE CHILD PAGE IDS
		foreach($sub_pages as $page){
			if(! in_array($page["info"]["id"], $sub_page_ids)){
				$sub_page_ids[] = $page["info"]["id"];
			}
		}
		
		// REMOVE THOSE PAGES FROM THE MAIN LIST
		foreach($all_pages as $key=>$page){
			if(in_array($page["info"]["id"], $sub_page_ids) || $page["info"]["id"] == $page_id){
				unset($all_pages[$key]);
			}
		}
		
		return $all_pages;
	}
	
	public function insertPage($postData, $newId ){
		
		$page_name = $this->getPageNameFromTitle($postData['page_title']);
		$page_preset = $postData['page_preset'];

		if($page_preset){
			
			$query = "SELECT * FROM PageLayoutPresets, PageLayoutPresetDefinitions WHERE PageLayoutPresets.plp_id = '$page_preset' AND PageLayoutPresets.plp_id=PageLayoutPresetDefinitions.plpd_preset_id ";
			$presetInfo = $this->database->queryToArray($query);
			$master_template_name = $presetInfo[0]['plp_master_template_name'];

			$sql = "INSERT INTO Pages (page_name, page_webid, page_title, page_parent, page_draft_template, page_site_id, page_created, page_description, page_keywords, page_createdby_userid) VALUES ('$page_name', '$newId', '".$postData['page_title']."', '".$postData['page_parent']."', '".$master_template_name."', '".$postData['site_id']."', '".time()."', '".$postData['page_description']."', '".$postData['page_keywords']."', '".$postData['user_id']."')";
    		
    		$this->database->rawQuery($sql);

			$id = $this->database->getInsertId();

			foreach($presetInfo as $preset){	
				$sql = "INSERT INTO AssetIdentifiers (assetidentifier_draft_asset_id, assetidentifier_assetclass_id, assetidentifier_page_id) VALUES ('".$preset['plpd_asset_id']."', '".$preset['plpd_container_id']."', '$id')";
    			$this->database->rawQuery($sql);
			}
			
		}else{
		
			$sql = "INSERT INTO Pages (page_name, page_webid, page_title, page_parent, page_draft_template, page_site_id, page_created, page_description, page_keywords,  page_createdby_userid) VALUES ('$page_name', '$newId', '".$postData['page_title']."', '".$postData['page_parent']."', '".$postData['page_template']."', '".$postData['site_id']."', '".time()."', '".$postData['page_description']."', '".$postData['page_keywords']."', '".$postData['user_id']."')";
    		$this->database->rawQuery($sql);
			$id = $this->database->getInsertId();
		
		}
		
		$url_count = $this->checkUrl($postData['page_url']);
		if($url_count<=0){
			$sql = "INSERT INTO `PageUrls` ( `pageurl_id` , `pageurl_page_id` , `pageurl_url` ) VALUES (NULL , '$id', '".$postData['page_url']."');";
    			$this->database->rawQuery($sql);
			$saved_url = "true";
		}else{
			$saved_url = "false";
		}
    		return $saved_url;
	}
	
	public function getAutomaticUrl($page_id){
		
	}
	
	public function getPageNameFromTitle($page_title){
		$page_name = strtolower($page_title);
    	$page_name = trim($page_name, " ?!%$#&*|/\\");
    	$page_name = preg_replace("/['\"]+/", "", $page_name);
    	$page_name = preg_replace("/[^\w-_]+/", "-", $page_name);
    	return $page_name;
	}
	
	public function updateSiteDetails($siteData){
		$site_id = $siteData['site_id'];
   		$sql = "UPDATE Sites SET site_name = '".$siteData['site_name']."', site_title_format = '".$siteData['site_title_format']."', site_domain = '".$siteData['site_domain']."', site_root = '".$siteData['site_root']."',site_error_title = '".$siteData['site_error_title']."', site_error_tpl = '".$siteData['site_error_tpl']."',site_admin_email = '".$siteData['site_admin_email']."', site_top_page_id = '".$siteData['site_top_page_id']."' WHERE site_id = '".$siteData['site_id']."' LIMIT 1";
   		$result = $this->database->rawQuery($sql);
	}
	
	public function updatePage($pageData){
		
		$page_id = isset($pageData['page_id']) ? $pageData['page_id'] : null;
    	$page_parent = $pageData['page_id'] > 0 ? $pageData['page_parent'] : -1;

    	// $page_name = $this->getPageNameFromTitle($pageData['page_title']);
		
		if($pageData['page_url'] && $page_parent != -1){
    		$page_url = $pageData['page_url'];
    	}else{
    		$page_url = $this->getAutomaticUrl($page_id);
    	}
    	
    	$page_keywords = mysql_real_escape_string($pageData['page_keywords']);
    	$page_title = mysql_real_escape_string($pageData['page_title']);
    	$page_description = mysql_real_escape_string($pageData['page_description']);
    	$page_cache_as_html = mysql_real_escape_string($pageData['page_cache_as_html']);
    	$page_cache_interval = mysql_real_escape_string($pageData['page_cache_interval']);
    	
    	$sql = "UPDATE `Pages` SET `page_keywords` = '".$page_keywords."', `page_title` = '".$page_title."', `page_description` = '".$page_description."', `page_parent` = '".$page_parent."', page_cache_as_html='".$page_cache_as_html."', page_cache_interval='".$page_cache_interval."', page_modified='".time()."' WHERE `page_id` = '".$page_id."' LIMIT 1";
    	$result = $this->database->rawQuery($sql);
	}
	
	public function getPage($page_id){
		if(is_numeric($page_id)){
			$idField = "page_id";
		}else{
			$idField = "page_webid";
		}
		
		$sql = "SELECT * FROM Pages, Sites WHERE Pages.".$idField."='".$page_id."' AND Pages.page_type != 'SUBPAGE' AND Sites.site_id=Pages.page_site_id LIMIT 1";
		$pageInfo = $this->database->queryToArray($sql);
		
		return $pageInfo[0];
	}
	
	public function getPageAssetClasses($page_id){
		$pageAssetClasses = array();
		$sql = "SELECT DISTINCT assetidentifier_assetclass_id FROM AssetIdentifiers, Pages WHERE assetidentifier_page_id = page_id AND page_webid='$page_id'";
		$results = $this->database->queryToArray($sql);
		foreach ($results as $assetClass){
			$pageAssetClasses[] = $assetClass["assetidentifier_assetclass_id"];
		}
		
		return $pageAssetClasses;
	}
	
	
	////////////////////////////////// OLD CODE (STILL IN USE) /////////////////////////////////////////
	
	
	public function getAssetClassType($assetclass_name){
		$info = $this->database->queryToArray("SELECT AssetTypes.assettype_code FROM AssetClasses, AssetTypes WHERE assetclass_assettype_id=assettype_id AND assetclass_name='$assetclass_name'");
		
		if(count($info) > 0){
			return $info[0]['assettype_code'];
		}else{
			return false;
		}
	}
	
	
	////////////////////////////////////// NEW CODE //////////////////////////////////////////////
	
	
	public function getPageTemplateAssetClasses($page_id, $version="draft", $item_id=false){
		
		$page_id = $this->getPageIdFromPageWebId($page_id);
		$tree = $this->getPageAssetClassTree($page_id, $version, $item_id);
		return array("tree"=>$tree, "list"=>array());
		
	}
	
	public function getPageAssetClassTree($page_id, $version, $item_id=false){
	
		// $field = ($version == "live") ? "page_live_template" : "page_draft_template";
		
		// $template = $this->database->specificQuery($field, "page_id", $page_id, "Pages");
		$page = new SmartestPage;
		
		if($page->find($page_id)){
		    
		    $template = ($version == "live") ? $page->getLiveTemplate() : $page->getDraftTemplate();
    		$draft = ($version == "live") ? false : true;
    		
    		$template_file = SM_ROOT_DIR."Presentation/Masters/".$template;
            
            $assetClasses = $this->getTemplateAssetClasses($template_file, $page, 0, $version, $item_id);

    		return $assetClasses;
		
	    }else{
	        
	        return array();
	        
	    }
	
	}
	
	public function getPageElements($page_id, $version){
		$page_id = $this->getPageIdFromPageWebId($page_id);
		$tree = $this->getPageElementsTree($page_id, $version);
		// $list = $this->getSerialisedAssetClassTree($tree);
		return array("tree"=>$tree, "list"=>$list);
	}
	
	public function getPageElementsTree($page_id, $version){
		
		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		
		$template = $this->database->specificQuery($field, "page_id", $page_id, "Pages");
		$site_id = $this->database->specificQuery("page_site_id", "page_webid", $page_id, "Pages");
		$site_root = $this->database->specificQuery("site_root", "site_id", $site_id, "Sites");
		
		$template_file = "Presentation/Masters/".$template;
		$elements = $this->getTemplateElements($template_file, $page_id, 0, $version);
		
	}
	
	public function getTemplateElements(){
		
	}
	
	public function getSerialisedAssetClassTree($assetClassesArray){
		
		foreach($assetClassesArray as $assetClass){
			
			$this->displayAssetClasses[$this->displayAssetClassesIndex]['info'] = $assetClass['info'];
			
			if(!empty($assetClass['level'])){
				$this->displayAssetClasses[$this->displayAssetClassesIndex]['level'] = $assetClass['level'];
			}
			
			// print_r($this->displayAssetClasses[$this->displayAssetClassesIndex]);
			
			$this->displayAssetClassesIndex++;
			
			if(!empty($assetClass['children'])){
				
				$this->getSerialisedAssetClassTree($assetClass['children']);
				
			}
			
			
		}
		
		return $this->displayAssetClasses;
		
	}
		
    public function getTemplateAssetClasses($template_file_path, SmartestPage $page, $level=0, $version="draft", $item_id=false){
		
		$i = 0;
		$info = array();
		$site_id = $page->getSiteId();
		$version = ($version == 'live') ? 'live' : 'draft';
		$draft = ($version == 'draft') ? true : false;
		
		$fieldNames = $this->getTemplateFieldNames($template_file_path);
			
		if(is_array($fieldNames)){
			
			foreach($fieldNames as $fieldName){
                
                $field = new SmartestPageField;
                // a simple 'hydrateBy' did not take into account that fields are not cross-site and multiple fields may exist of the same name (one for each site)
                $correct_sql = "SELECT * FROM PageProperties WHERE pageproperty_name='".$fieldName."' AND pageproperty_site_id='".$site_id."'";
                $result = $this->database->queryToArray($correct_sql);
                $field->hydrate($result[0]);
                
				$info[$i]['info']['exists'] = (count($result) > 0) ? 'true' : 'false';
				$info[$i]['info']['defined'] = $this->getFieldDefinedOnPage($fieldName, $page->getId());
				$info[$i]['info']['assetclass_name'] = $fieldName;
				$info[$i]['info']['assetclass_id'] = 'field_'.$field->getId();
				
				// beware - hack
				$info[$i]['info']['asset_id'] = $field->getId();
				$info[$i]['info']['type'] = "field";
				$info[$i]['info']['level'] = $level;
				
				$i++;
			}

		}
		
		$placeholderNames = $this->getTemplatePlaceholderNames($template_file_path);
			
		if(is_array($placeholderNames)){
		
			foreach($placeholderNames as $placeholderName){
			    
			    $info[$i]['info'] = $this->getAssetClassInfo($placeholderName);
				
				$info[$i]['info']['exists'] = $this->getAssetClassExists($placeholderName);
				$info[$i]['info']['defined'] = $this->getAssetClassDefinedOnPage($placeholderName, $page->getId(), $item_id);
				
				$info[$i]['info']['assetclass_name'] = $placeholderName;
				$info[$i]['info']['assetclass_id'] = 'placeholder_'.$placeholderName;
				$info[$i]['info']['type'] = "placeholder";
				
				if($version == "live"){
					$asset = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), false, $item_id);
				}else{
					$asset = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), true, $item_id);
				}
				
				$info[$i]['info']['asset_id'] = $asset;
				$assetObj = new SmartestAsset();
				
				if($assetObj->hydrate($asset)){
				    
				    $child = $assetObj->getArrayForElementsTree($level);
				    
				    if($assetObj->isParsable()){
				        
				        $child['children'] = $assetObj->getTextFragment()->getAttachmentsForElementsTree($level+2, $version);
				        
				        foreach($child['children'] as $key => $attachment){
				            
				            $child['children'][$key]['info']['assetclass_id'] = $assetObj->getStringid().'_'.$attachment['info']['assetclass_name'];
				            $child['children'][$key]['info']['assetclass_name'] = $assetObj->getStringid().':'.$attachment['info']['assetclass_name'];
				            
				            if(isset($child['children'][$key]['asset_object']) && is_object($child['children'][$key]['asset_object'])){
				                $child_asset = $child['children'][$key]['asset_object'];
				                $child_asset_array = $child_asset->getArrayForElementsTree($level+2);
				                $child['children'][$key]['children'] = array($child_asset_array);
				            }
				        }
			        }
				    
				    $info[$i]['children'] = array($child);
				}
				
				$info[$i]['level'] = $level;
				
				$i++;
			}
		
		}
		
		$itemspace_names = $this->getTemplateItemSpaceNames($template_file_path);
		
		if(is_array($itemspace_names)){
			
			foreach($itemspace_names as $itemspace_name){
                
                $item_space = new SmartestItemSpace;
                
                $info[$i]['info']['type'] = "itemspace";
                $info[$i]['info']['assetclass_name'] = $itemspace_name;
                
                if($item_space->exists($itemspace_name, $site_id)){
                    
                    $info[$i]['info']['exists'] = 'true';
                    $info[$i]['info']['assetclass_id'] = $item_space->getId();
                    
                    $definition = new SmartestItemSpaceDefinition;
                    
                    if($definition->load($itemspace_name, $page, $draft)){
                        
                        $info[$i]['info']['defined'] = $definition->hasChanged() ? 'DRAFT' : 'PUBLISHED';
                        $item = $definition->getSimpleItem($draft);
                        $info[$i]['children'][] = $item->getInfoForPageTree($draft);
                        
                    }else{
                        $info[$i]['info']['defined'] = 'UNDEFINED';
                    }
                    
                    if($item_space->getUsesTemplate()){
                        $template = new SmartestTemplateAsset;
                        
                        if($template->find($item_space->getTemplateAssetId())){
                            
                            $template_array = $template->getArrayForElementsTree($level+1);
                            
                            $info[$i]['info']['asset_id'] = $asset;
    					    $info[$i]['info']['asset_webid'] = $template->getWebid();
                            $info[$i]['info']['filename'] = '';
                            
    					    $child_template_file = SM_ROOT_DIR."Presentation/Layouts/".$template->getUrl();
    					    
                            $template_array['children'] = $this->getTemplateAssetClasses($child_template_file, $page, $level+2, $version, $item_id);
    	                    
    	                    $info[$i]['children'][] = $template_array;
                            
                        }
                        
                    }
                    
                }else{
                    $info[$i]['info']['exists'] = 'false';
                }
                
                $info[$i]['info']['level'] = $level;
                
                $i++;
			}
		}
		
		$listNames = $this->getTemplateListNames($template_file_path);
			
		if(is_array($listNames)){
			
			foreach($listNames as $listName){
                
				$list = new SmartestCmsItemList;
				
				if($list->exists($listName, $page->getId())){
				    $info[$i]['info'] = $list->getInfoForPageTree($level);
				}else{
				    $info[$i]['info']['exists'] = 'true';
				    $info[$i]['info']['defined'] = 'UNDEFINED';
				    $info[$i]['info']['assetclass_name'] = $listName;
    				$info[$i]['info']['type'] = "list";
    				$info[$i]['info']['level'] = $level;
				}
				
				$i++;
				
			}

		}
		
		/* $templateNames = $this->getTemplateIncludedTemplateNames($template_file_path);
		
		if(is_array($templateNames)){
		    
		    foreach($templateNames as $templateName){
		        
		        if(is_file(SM_ROOT_DIR."Presentation/Layouts/".$templateName)){
		            
		            $assetObj = new SmartestTemplateAsset();
		            
		            $info[$i]['info']['asset_id'] = $asset;
    			    // $info[$i]['info']['asset_webid'] = $this->database->specificQuery("asset_webid", "asset_id", $asset, "Assets");
    			    $info[$i]['info']['asset_webid'] = $assetObj->getWebid();

    			    $child_template_file = SM_ROOT_DIR."Presentation/Layouts/".$assetObj->getUrl();
    			    $info[$i]['info']['filename'] = '';

    			    // $info[$i]['info']['filename'] = $this->database->specificQuery("asset_url", "asset_id", $asset, "Assets");
                    $child = $assetObj->getArrayForElementsTree($level+1);
                    $child['children'] = $this->getTemplateAssetClasses($child_template_file, $page, $level+2, $version);
                    $info[$i]['children'] = array($child);

                    $i++;
		            
		        }
		        
		    }
		    
		} */

		$containerNames = $this->getTemplateContainerNames($template_file_path);
		
		if(is_array($containerNames)){
		
			foreach($containerNames as $containerName){
			    
			    $container = new SmartestContainer;
			    
			    if($container->hydrateBy('name', $containerName)){
			    
				    $info[$i]['info'] = $this->getAssetClassInfo($containerName);
				    $info[$i]['info']['exists'] = 'true';
				    $info[$i]['info']['defined'] = $this->getAssetClassDefinedOnPage($containerName, $page->getId(), $item_id);
				
			    }else{
			        $info[$i]['info']['exists'] = 'false';
			        $info[$i]['info']['defined'] = 'UNDEFINED';
			        
			    }
				
				$info[$i]['info']['assetclass_name'] = $containerName;
				$info[$i]['info']['assetclass_id'] = 'container_'.$containerName;
				$info[$i]['info']['type'] = "container";
				$info[$i]['level'] = $level;
				
				if($info[$i]['info']['assetclass_type'] == 'SM_ASSETCLASS_CONTAINER'){
					
					if($version == "live"){
						$asset_id = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), false, $item_id);
					}else{
						$asset_id = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), true, $item_id);
					}
					
					$assetObj = new SmartestTemplateAsset();
					
					if($assetObj->find($asset_id)){
					    
					    // This is new code, and works. It was tested here but will be implemented in SmartestTemplateAsset.class.php
					    // $tags = $assetObj->getTagsByName(array('placeholder', 'field'));
					    
					    $info[$i]['info']['asset_id'] = $asset_id;
					    $info[$i]['info']['asset_webid'] = $assetObj->getWebid();
					    
					    $child_template_file = SM_ROOT_DIR."Presentation/Layouts/".$assetObj->getUrl();
					    $info[$i]['info']['filename'] = '';
					    
					    $child = $assetObj->getArrayForElementsTree($level+1);
	                    $child['children'] = $this->getTemplateAssetClasses($child_template_file, $page, $level+2, $version, $item_id);
	                    $info[$i]['children'] = array($child);
					
				    }
				
				}
			    
			    $i++;
				
			}
		
		}
		
		$templateNames = $this->getTemplateHardCodedTemplateInclusions($template_file_path);
		$tlh = new SmartestTemplatesLibraryHelper;
		// print_r($templateNames);
		if(is_array($templateNames)){
		
    		foreach($templateNames as $templateName){
		    
    		    /* $container = new SmartestContainer;
		    
    		    if($container->hydrateBy('name', $containerName)){
		    
    			    $info[$i]['info'] = $this->getAssetClassInfo($containerName);
    			    $info[$i]['info']['exists'] = 'true';
    			    $info[$i]['info']['defined'] = $this->getAssetClassDefinedOnPage($containerName, $page->getId(), $item_id);
			
    		    }else{
    		        $info[$i]['info']['exists'] = 'false';
    		        $info[$i]['info']['defined'] = 'UNDEFINED';
		        
    		    } */
		    
    		    if(substr($templateName, -4, 4) != '.tpl'){
    		        $templateName .= '.tpl';
    		    }
		    
    		    if(is_file(SM_ROOT_DIR.'Presentation/Layouts/'.$templateName)){
		        
    		        if($tlh->getTemplateIsImported($templateName, 'SM_ASSETTYPE_CONTAINER_TEMPLATE')){
		            
    		            $tmpl = new SmartestTemplateAsset;
    		            if($tmpl->findBy('url', $templateName)){
    		                $info[$i] = $tmpl->getArrayForElementsTree($level);
    		                $child_template_file = SM_ROOT_DIR."Presentation/Layouts/".$tmpl->getUrl();
    					    /* $info[$i]['info']['filename'] = '';
					    
    					    $child = $assetObj->getArrayForElementsTree($level+1);
    	                    $child['children'] = $this->getTemplateAssetClasses($child_template_file, $page, $level+2, $version, $item_id); */
	                    
    		                $info[$i]['children'] = $this->getTemplateAssetClasses($child_template_file, $page, $level+2, $version, $item_id);
    		                $i++;
    		            }
		            
    		        }
		        
    		    }
			
    			/* $info[$i]['info']['assetclass_name'] = $containerName;
    			$info[$i]['info']['assetclass_id'] = 'container_'.$containerName;
    			$info[$i]['info']['type'] = "container";
    			$info[$i]['level'] = $level;
			
    			if($info[$i]['info']['assetclass_type'] == 'SM_ASSETCLASS_CONTAINER'){
				
    				if($version == "live"){
    					$asset_id = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), false, $item_id);
    				}else{
    					$asset_id = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), true, $item_id);
    				}
				
    				$assetObj = new SmartestTemplateAsset();
				
    				if($assetObj->find($asset_id)){
				    
    				    // This is new code, and works. It was tested here but will be implemented in SmartestTemplateAsset.class.php
    				    // $tags = $assetObj->getTagsByName(array('placeholder', 'field'));
				    
    				    $info[$i]['info']['asset_id'] = $asset_id;
    				    $info[$i]['info']['asset_webid'] = $assetObj->getWebid();
				    
    				    $child_template_file = SM_ROOT_DIR."Presentation/Layouts/".$assetObj->getUrl();
    				    $info[$i]['info']['filename'] = '';
				    
    				    $child = $assetObj->getArrayForElementsTree($level+1);
                        $child['children'] = $this->getTemplateAssetClasses($child_template_file, $page, $level+2, $version, $item_id);
                        $info[$i]['children'] = array($child);
				
    			    }
			
    			} */
			
    		}
	    }
		
		return $info;
	}
	
	public function publishPageContainersConfirm($page_webid, $version){
		$assetClasses = $this->getPageTemplateAssetClasses($page_webid, $version);
		$assetClasseslist = $this->getSerialisedAssetClassTree($assetClasses);
		foreach($assetClasseslist as $assetClass){
			
			if($assetClass['info']['type']=="container" && $assetClass['info']['defined']=="UNDEFINED" && $assetClass['info']['exists']=="true" ){
				$undefinedContainerClasses[$i]=$assetClass['info']['assetclass_name'];
				$i++;	
			}
			
		}
		return $undefinedContainerClasses;
	}
		
	public function publishPageContainers($page_webid){

		$page_id = $this->getPageIdFromPageWebId($page_webid);

		$sql = "SELECT * FROM AssetClasses, AssetIdentifiers WHERE AssetClasses.assetclass_id=AssetIdentifiers.assetidentifier_assetclass_id AND AssetIdentifiers.assetidentifier_page_id='$page_id' AND AssetClasses.assetclass_assettype_id='4'";
		$assetClasses = $this->database->queryToArray($sql);
		
		foreach($assetClasses as $assetClass){
			$assetClassId=$assetClass['assetclass_id'];
			$sql = "UPDATE AssetIdentifiers SET assetidentifier_live_asset_id=assetidentifier_draft_asset_id WHERE assetidentifier_page_id='$page_id' and assetidentifier_assetclass_id='$assetClassId'";
			$this->database->rawQuery($sql);
		}
		
	}
	
	public function publishPagePlaceholdersConfirm($page_webid,$version){
		
		$assetClasses = $this->getPageTemplateAssetClasses($page_webid, $version);
		$assetClasseslist = $this->getSerialisedAssetClassTree($assetClasses);
		
		foreach($assetClasseslist as $assetClass){
			if($assetClass['info']['type']=="placeholder" && $assetClass['info']['defined']=="UNDEFINED" && $assetClass['info']['exists']=="true" ){
				$undefinedPlaceholderClasses[$i]=$assetClass['info']['assetclass_name'];
				$i++;	
			}
		}
		
		return $undefinedPlaceholderClasses;
	}
	
	public function publishPagePlaceholders($page_webid){
		
		$page_id = $this->getPageIdFromPageWebId($page_webid);
		$sql = "SELECT * FROM AssetClasses, AssetIdentifiers WHERE  AssetClasses.assetclass_id=AssetIdentifiers.assetidentifier_assetclass_id AND AssetIdentifiers.assetidentifier_page_id='$page_id' AND AssetClasses.assetclass_assettype_id !='4'";
		$assetClasses = $this->database->queryToArray($sql);

		foreach($assetClasses as $assetClass){

			$assetClassId=$assetClass['assetclass_id'];
			$sql = "UPDATE AssetIdentifiers SET assetidentifier_live_asset_id=assetidentifier_draft_asset_id WHERE assetidentifier_page_id='$page_id' and assetidentifier_assetclass_id=$assetClassId";
			$this->database->rawQuery($sql);
			
		}
		
		$this->touchPage($page_id);
		$this->clearCacheForPlaceholders($page_id);
	}
	
	public function getUndefinedElements($page_webid, $version='draft', $item_id=false){
		
		$assetClasses = $this->getPageTemplateAssetClasses($page_webid, $version, $item_id);
		$assetClasseslist = $this->getSerialisedAssetClassTree($assetClasses['tree']);
        
        // print_r($assetClasseslist);
        
		$i = 0;

		foreach($assetClasseslist as $assetClass){
			if($assetClass['info']['defined']=="UNDEFINED" && $assetClass['info']['exists']=="true"){
				$undefinedAssetsClasses[$i]=$assetClass;
				$i++;	
			}
		}
		
		return $undefinedAssetsClasses;
	}
	
	public function publishPage($page_webid){
		$page_id = $this->getPageIdFromPageWebId($page_webid);
		$sql = "UPDATE AssetIdentifiers SET assetidentifier_live_asset_id = assetidentifier_draft_asset_id WHERE assetidentifier_page_id='$page_id'";
		$this->database->rawQuery($sql);
		
		// Insert code to publish subpages here:
		
		$sql = "UPDATE Pages SET page_live_template = page_draft_template, page_last_published='".time()."', page_is_published='TRUE' WHERE page_id='$page_id'";
		$this->database->rawQuery($sql);
		$sql = "UPDATE PagePropertyValues SET pagepropertyvalue_live_value = pagepropertyvalue_draft_value WHERE pagepropertyvalue_page_id = '$page_id'";
		$this->database->rawQuery($sql);
		
		$this->touchPage($page_id);
		$this->clearPageCache($page_id);
			
	}
	
	public function clearPageCache($page_id){
		$this->clearCacheForPlaceholders($page_id);
		$this->clearFullPageFromCache($page_id);
	}
	
	public function clearFullPageFromCache($page_id){
		
		$cache_filename = "System/Cache/Pages/smartest_page_".$page_id.".html";
		
		if(is_file($cache_filename)){
			unlink($cache_filename);
		}
	}
	
	public function clearCacheForPlaceholders($page_id){
		
		$version="live";
		
		$assetClasses = $this->getPageTemplateAssetClasses($page_id, $version);
		$assetClasseslist = $this->getSerialisedAssetClassTree($assetClasses);
		
		foreach($assetClasseslist as $assetClass){
			if($assetClass['info']['type']=="placeholder" ){
				
				if(is_numeric($get['assetclass_id']) && @$get['assetclass_id']){
					$assetclass = $this->manager->database->specificQuery("assetclass_name", "assetclass_id", $assetClass['info']['assetclass_id'], "AssetClasses");
				}else{
					$assetclass = $assetClass['info']['assetclass_name'];
				}
				
				$cache_filename = "System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".$page_id.".tmp";
				
				if(is_file($cache_filename)){
					unlink($cache_filename);
				}
			}
		}
	}
	
	public function getUnDefinedContainers($page_id){
		
		
		
	}
	
	public function getAssetClassInfo($assetClassName){
		$sql="SELECT AssetClasses.* FROM AssetClasses WHERE assetclass_name='$assetClassName'";
		$info = $this->database->queryToArray($sql);
		return @$info[0];
	}
	
	public function getTemplatePlaceholderNames($template_file_path){
		if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){
				$regexp = preg_match_all("/<\?sm:placeholder.+name=\"([\w-_]+)\".*:\?>|with=\"placeholder:([\w-_]+)\"/i", $template_contents, $matches);
				
				// loop through two different syntaxes to get all named placeholders
				
				// $foundClasses = array_merge($matches[1], $matches[2]);
				// print_r($foundClasses);
				$foundClasses = $matches[1];
				
				return $foundClasses;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function getTag($template_file_path){
		if(is_file($template_file_path)){
			
			if($template_contents = file_get_contents($template_file_path)){
				$regexp = preg_match("/<\?sm:placeholder.+name=\"([\w-_]+)\".*:\?>|with=\"placeholder:([\w-_]+)\"/i", $template_contents);
				$tag='placeholder';
			}else{
				$tag='container';
			}
			
		}
		return $tag;
	}
	
	public function getTemplateContainerNames($template_file_path){
		if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){

				$regexp = preg_match_all("/\<\?sm:container.+name=\"([\w-_]+)\".*(instance=\"([\w-_]+)\")?:\?>/i", $template_contents, $matches);
				$foundClasses = $matches[1];
				return $foundClasses;

			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function getTemplateHardCodedTemplateInclusions($template_file_path){
	    if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){

				$regexp = preg_match_all("/\<\?sm:template.+name=\"([\w-_]+)\".*:\?>/i", $template_contents, $matches);
				$foundClasses = $matches[1];
				return $foundClasses;

			}else{
				return false;
			}
		}else{
			return array();
		}
	}
	
	public function getTemplateIncludedTemplateNames(){
	    if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){

				$regexp = preg_match_all("/\<\?sm:template.+name=\"([\w-_]+)\".*?:\?>/i", $template_contents, $matches);
				$foundClasses = $matches[1];
				
				foreach($foundClasses as &$t){
				    if(substr($t, -4, 4) != '.tpl'){
				        $t .= '.tpl';
				    }
				}
				
				return $foundClasses;

			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function getTemplateTags($template_file_path){
		
		if(is_file($template_file_path)){
			
			if($template_contents = file_get_contents($template_file_path)){

				$regexp = preg_match_all("/<\?sm:([\w_]{2,})([^\}]+)?:\?>/i", $template_contents, $matches);
				$completeTags = $matches[0];
				$foundTags = $matches[1];
				$tags = array();
				
				if(is_array($completeTags)){
					
					foreach($completeTags as $key=>$complete_tag){
						
						$tag = array();
						$tag_name = $foundTags[$key];
						$tag['type'] = $tag_name;
						$tag['attributes'] = array();
						
						$expression = '/((\w+)=(\$[\w_\.]+|"([^"]+)"))/i';
						
						$regexp2 = preg_match_all($expression, $complete_tag, $matches2);
						
						for($i=0;$i<count($matches2[0]);$i++){
							$tag['attributes'][$matches2[2][$i]] = array();
							if($matches2[4][$i]){
								$tag['attributes'][$matches2[2][$i]]['type'] = "string";
								$tag['attributes'][$matches2[2][$i]]['value'] = $matches2[4][$i];
							}else{
								$tag['attributes'][$matches2[2][$i]]['type'] = "variable";
								$tag['attributes'][$matches2[2][$i]]['value'] = $matches2[3][$i];
							}
						}
						
						$tags[$key] = $tag;
					}
				}
				
				return $tags;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function getAvailableAssets($assetclass_name){

		$finalArray = array();
		$assetclass_assettype_id = $this->database->specificQuery("assetclass_assettype_id", "assetclass_name", $assetclass_name, "AssetClasses");

		$assets = $this->database->queryToArray("SELECT * FROM Assets, AssetTypes WHERE assettype_id=asset_assettype_id AND asset_assettype_id='$assetclass_assettype_id'");
		$i=0;
		foreach($assets as $asset){
			$finalArray[$i] = $asset;
			if(in_array($asset["assettype_code"], array("TEXT", "LINE", "HTML", "CSS", "JSCR"))){
				$finalArray[$i]["asset_contents"] = $this->database->specificQuery("textfragment_content", "textfragment_id", $asset["asset_fragment_id"], "TextFragments");
			}
			$i++;
		}
		return $finalArray;
	}
	
	public function getAssetClassExists($assetclass_name){
	
		$assetclass = $this->database->specificQuery("assetclass_id", "assetclass_name", $assetclass_name, "AssetClasses");
		
		if($assetclass){
			return 'true';
		}else{
			return 'false';
		}
	}
	
	public function getAssetClassDefinedOnPage($assetclass_name, $page_id, $item_id=false){

		$sql = "SELECT assetidentifier_draft_asset_id, assetidentifier_live_asset_id FROM AssetIdentifiers, AssetClasses WHERE assetidentifier_assetclass_id=assetclass_id AND assetidentifier_page_id='$page_id' AND assetclass_name='$assetclass_name'";
		
		$result = $this->database->queryToArray($sql." AND assetidentifier_item_id IS NULL");
		
		if(!empty($result[0]) && $result[0]["assetidentifier_draft_asset_id"] > 0 && $result[0]["assetidentifier_live_asset_id"] > 0 && $result[0]["assetidentifier_draft_asset_id"] == $result[0]["assetidentifier_live_asset_id"] && $result[0]["assetidentifier_draft_render_data"] == $result[0]["assetidentifier_live_render_data"]){
			$status = "PUBLISHED";
		}else if(!empty($result[0]) && $result[0]["assetidentifier_draft_asset_id"] > 0 && ($result[0]["assetidentifier_live_asset_id"] != $result[0]["assetidentifier_draft_asset_id"] || $result[0]["assetidentifier_draft_render_data"] != $result[0]["assetidentifier_live_render_data"])){
            $status = "DRAFT";
		}else{
			$status = "UNDEFINED";
		}
		
		if($item_id != false){
		    
		    $result = $this->database->queryToArray($sql." AND assetidentifier_item_id='".$item_id."'");
		    
		    $item_sql = $sql." AND assetidentifier_item_id='".$item_id."'";
		    $result = $this->database->queryToArray($item_sql);
		    
		    if(!empty($result[0]) && $result[0]["assetidentifier_draft_asset_id"] > 0 && $result[0]["assetidentifier_live_asset_id"] > 0 && $result[0]["assetidentifier_draft_asset_id"] == $result[0]["assetidentifier_live_asset_id"] && $result[0]["assetidentifier_draft_render_data"] == $result[0]["assetidentifier_live_render_data"]){
    			$status = "PUBLISHED";
    		}else if(!empty($result[0]) && $result[0]["assetidentifier_draft_asset_id"] > 0 && ($result[0]["assetidentifier_live_asset_id"] != $result[0]["assetidentifier_draft_asset_id"] || $result[0]["assetidentifier_draft_render_data"] != $result[0]["assetidentifier_live_render_data"])){
                $status = "DRAFT";
    		}
		}

		return $status;
	}
	
	public function getAssetClassDefinition($assetclass_name, $page_id, $draft=false, $item_id=false){
		
		$field = $draft ? 'assetidentifier_draft_asset_id' : 'assetidentifier_live_asset_id';
		
		$sql = "SELECT $field, assetidentifier_id FROM AssetIdentifiers, AssetClasses WHERE assetidentifier_assetclass_id=assetclass_id AND assetidentifier_page_id='$page_id' AND assetclass_name='$assetclass_name'";
		
		if($item_id != false){
		    
		    $item_sql = $sql." AND assetidentifier_item_id='".$item_id."'";
		    $result = $this->database->queryToArray($item_sql);
		    
		    if(!empty($result[0])){
    			return $result[0][$field];
    		}
		}
		
		$sql .= " AND assetidentifier_item_id IS NULL";
		$result = $this->database->queryToArray($sql);
		
		if(!empty($result[0])){
		    return $result[0][$field];
		}else{
			return false;
		}
	}
	
	/* public function getMasterTemplates($site_id){
		// $siteRoot = $this->database->specificQuery("site_root", "site_id", $site_id, "Sites");
		// $templates_dir = $siteRoot."Presentation/Masters/";
		$templates_dir = "Presentation/Masters/";
		if ($handle = @opendir($templates_dir)) {

			$i = 0;
			
			while (false !== ($file = readdir($handle))) {
				if(preg_match("/([^\s\/]+)\.tpl/i", $file, $matches)){
					$templates[$i]["filename"] = $matches[0];
					$templates[$i]["menuname"] = $matches[1];
					
					$i++;
				}
			}

			closedir($handle);
		}
		
		return($templates);
	} */
	
	/* public function setDraftAsset($page_webid, $assetclass_name, $asset_id){
		
		$page_id = $this->getPageIdFromPageWebId($page_webid);
		$assetclass_id = $this->assetsManager->getAssetClassIdFromAssetClassName($assetclass_name);
		
		if($this->database->howMany("SELECT * FROM AssetIdentifiers WHERE assetidentifier_page_id='$page_id' AND assetidentifier_assetclass_id='$assetclass_id'")){
			// definition exists - update it with an UPDATE query
			$sql = "UPDATE AssetIdentifiers SET assetidentifier_draft_asset_id='$asset_id' WHERE assetidentifier_assetclass_id='$assetclass_id' AND assetidentifier_page_id='$page_id'";
		}else{
			// no definition exists - create it with an INSERT query
			$sql = "INSERT INTO AssetIdentifiers (assetidentifier_draft_asset_id, assetidentifier_assetclass_id, assetidentifier_page_id) VALUES ('$asset_id', '$assetclass_id', '$page_id')";
		}
		
		$this->touchPage($page_webid);
		
		$this->database->rawQuery($sql);
		
	}
	
	public function setLiveAsset($page_webid, $assetclass_name){
		
		$page_id = $this->getPageIdFromPageWebId($page_webid);
		$assetclass_id = $this->assetsManager->getAssetClassIdFromAssetClassName($assetclass_name);
		
		$sql = "UPDATE AssetIdentifiers SET assetidentifier_live_asset_id=assetidentifier_draft_asset_id WHERE assetidentifier_assetclass_id='$assetclass_id' AND assetidentifier_page_id='$page_id'";
		
		$this->database->rawQuery($sql);
		
	} */

	public function getDefinedPageAssetsList($page_id){
		
		$assetClasses = $this->getPageAssetClasses($page_id);
		
		$finalAssetClassList = array();
		
		foreach($assetClasses as $assetClass){
			$sql = "SELECT * FROM AssetIdentifiers, AssetClasses, Pages WHERE assetidentifier_assetclass_id=assetclass_id AND assetclass_id='$assetClass' AND assetidentifier_page_id=page_id AND page_webid='$page_id'";
			
			$result = $this->database->queryToArray($sql);
			$finalAssetClassList[] = $result[0];
		}
		
		return $finalAssetClassList;
	}
	
	public function managePageData($result){

		foreach($result as $list){

			$set_id=$list['list_draft_set_id'];				
			$set = $this->setsManager->getSet($set_id);
			$set_type = $set['set_type'];
			$model_id = $set['itemclass_id'];
			
			if(is_numeric($model_id)){
				$items=$this->setsManager->getDataSetItemProperties($set_id,$set_type,$model_id);
				$count=count($items);	
			}
		}

		return $items;
	}
	
	public function getPageData($page_webid){
		$page_id=$this->getPageIdFromPageWebId($page_webid);
		$sql="SELECT * FROM Lists where list_page_id = '$page_id'";
		$result = $this->database->queryToArray($sql);
		return $result;	
	}
	
	public function getPageTitleFromPageId($page_webid){
		return $this->database->specificQuery("page_title", "page_webid", $page_webid, "Pages");
	}

	public function getPageLists($page_webid, $version){

		$tree = $this->getPageListsTree($page_webid, $version);

		return $tree;
	}
	
	public function getPageListsTree($page_webid, $version){
		
		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		
		$template = $this->database->specificQuery($field, "page_webid", $page_webid, "Pages");
		
		$template_file = "Presentation/Masters/".$template;
		$lists = $this->getTemplateLists($template_file, $page_webid, 0, $version);

		return $lists;
	}	

	public function getTemplateLists($template_file_path, $page_webid, $level=0, $version="draft"){
		
		$listNames = $this->getTemplateListNames($template_file_path);

		$i = 0;
			
		if(is_array($listNames)){
			
			foreach($listNames as $listName){

				$page_pk = $this->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
				$info[$i]['exists'] = $this->getListExists($listName);
				$info[$i]['defined'] = $this->getListDefinedOnPage($listName, $page_pk);
				$info[$i]['list_name'] = $listName;
				$info[$i]['type'] = "list";
				$info[$i]['level'] = $level;
				$i++;
			}

		}
		
		$assetClasses = $this->getPageTemplateAssetClasses($page_webid, $version);
		
		if(is_array($assetClasses)){
	
			$assetClasseslist = $this->getSerialisedAssetClassTree($assetClasses);
	
			foreach($assetClasseslist as $assetClass){

				if($assetClass['info']['type']=="container"&& $assetClass['info']['exists']=='true' ){
					$template=$assetClass['info']['filename'];
					$template_file = "Presentation/Layouts/".$template;
					$listNames = $this->getTemplateListNames($template_file);
					
					if(is_array($listNames)){

						foreach($listNames as $listName){

							$info[$i]['exists'] = $this->getListExists($listName);
							$info[$i]['defined'] = $this->getListDefinedOnPage($listName, $page_pk);
							$info[$i]['list_name'] = $listName;
							$info[$i]['type'] = "list";

							$info[$i]['level'] = $level;
							$i++;
						}

					}
				}
			}
		}
		return $info;
	}
	
	public function getTemplateListNames($template_file_path){
		if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){
				$regexp = preg_match_all("/<\?sm:list.+name=\"([\w-_]+?)\".*:\?>/i", $template_contents, $matches);

				$foundClasses = $matches[1];

				return $foundClasses;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function getListExists($list_name){
		
		$list = $this->database->specificQuery("list_id", "list_name", $list_name, "Lists");
		
		if($list){
			return 'true';
		}else{
			return 'false';
		}
	}
	
	public function getListDefinedOnPage($list_name, $page_pk){

		$sql = "SELECT * FROM Lists WHERE list_page_id='".$page_pk."' AND list_name='".$list_name."'";
		$result = $this->database->queryToArray($sql);

		if(count($result)){
		    
		    $list_array = $result[0];
		    
		    // print_r($list_array);
		    
		    // if((($list_array["list_draft_template_file"] == "default_list.tpl") &&  ($result[0]["list_live_template_file"] != "default_list.tpl") &&  ($result[0]["list_draft_template_file"]== $result[0]["list_live_template_file"])) && (($result[0]["list_draft_set_id"] > 0 ) && ($result[0]["list_live_set_id"] > 0) && ($result[0]["list_draft_set_id"] == $result[0]["list_live_set_id"]))){
			if(is_numeric($list_array['list_live_set_id']) && $list_array['list_draft_set_id'] == $list_array['list_live_set_id'] && $list_array['list_draft_template_file'] == $list_array['list_live_template_file'] && $list_array['list_draft_header_template'] == $list_array['list_live_header_template']  && $list_array['list_draft_footer_template'] == $list_array['list_live_footer_template']){
				$status = "PUBLISHED";
			// }else if((($result[0]["list_draft_template_file"] !="default_list.tpl" ) && ($result[0]["list_live_template_file"] != $result[0]["list_draft_template_file"]) )|| (($result[0]["list_draft_set_id"] >0) && ($result[0]["list_draft_set_id"] != $result[0]["list_live_set_id"]))){
			}else{
				$status = "DRAFT";
			}
		
		}else{
			$status = "UNDEFINED";
		}

		return $status;
	}
	
	public function getListNameFromListId($list_id){
		return $this->database->specificQuery("list_name", "list_id", $list_id, "Lists");
	}
	
	public function getListIdFromlistName($list_name){
		return $this->database->specificQuery("list_id", "list_name", $list_name, "Lists");
	}
	
	public function insertList($page_id,$list_name,$set_id,$list_template,$header_template,$footer_template){

		$defined = $this->getListDefinedOnPage($list_name, $page_id);

		if($defined != "UNDEFINED"){
			$listId = $this->getListIdFromlistName($list_name);
			$sql = "UPDATE Lists SET list_draft_set_id='$set_id',list_draft_template_file='$list_template',list_draft_header_template='$header_template',list_draft_footer_template='$footer_template' WHERE list_id = '$listId'";
		}else{
			$sql = "INSERT INTO Lists (list_name,list_draft_set_id,list_draft_template_file,list_draft_header_template,list_draft_footer_template,list_page_id) VALUES ('$list_name', '$set_id','$list_template','$header_template','$footer_template','$page_id')";	
		}
		
		$this->database->rawQuery($sql);
	}
	
	public function publishListsConfirm($page_webid,$version){
		$lists = $this->getPageLists($page_webid, $version);
		foreach($lists as $list){
			if($list['defined']=="UNDEFINED" ){
			$undefinedLists[$i]=$list['list_name'];
			$i++;	
			}
		}
		return $undefinedLists;
	}
	
	public function publishPageLists($page_webid){
		$page_id = $this->getPageIdFromPageWebId($page_webid);
		$sql="SELECT * FROM Lists WHERE list_page_id='$page_id'";
		$lists = $this->database->queryToArray($sql);

		foreach($lists as $list){
			$listId=$list['list_id'];
			$sql = "UPDATE Lists SET list_live_set_id=list_draft_set_id,list_live_template_file=list_draft_template_file WHERE list_id='$listId'";
			$this->database->rawQuery($sql);
		}
	}
	
	
	public function getTemplateFieldNames($template_file_path){
		if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){
				$regexp = preg_match_all("/<\?sm:(edit_)?field.+name=\"([\w-_]+?)\".*:\?>/i", $template_contents, $matches);

				$foundClasses = $matches[2];

				return $foundClasses;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function getTemplateItemSpaceNames($template_file_path){
		if(is_file($template_file_path)){
			if($template_contents = file_get_contents($template_file_path)){
				$regexp = preg_match_all("/<\?sm:itemspace.+name=\"([\w-_]+?)\".*:\?>/i", $template_contents, $matches);

				$foundClasses = $matches[1];

				return $foundClasses;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function getFieldExists($field_name){
		
		$list = $this->database->specificQuery("pageproperty_id", "pageproperty_name", $field_name, "PageProperties");
		
		if($list){
			return 'true';
		}else{
			return 'false';
		}
	}
	
	function getFieldDefinedOnPage($field_name, $page_pk){

		$sql = "SELECT pagepropertyvalue_live_value, pagepropertyvalue_draft_value FROM PageProperties, PagePropertyValues
WHERE PagePropertyValues.pagepropertyvalue_page_id='$page_pk'
AND PageProperties.pageproperty_name='$field_name'
AND PageProperties.pageproperty_id = PagePropertyValues.pagepropertyvalue_pageproperty_id";

		// echo $sql;

		$result = $this->database->queryToArray($sql);
		
		if(count($result)){
			if($result[0]["pagepropertyvalue_live_value"] && ($result[0]["pagepropertyvalue_live_value"] == $result[0]["pagepropertyvalue_draft_value"])){
				$status = "PUBLISHED";
			}else{
				$status = "DRAFT";
			}
		}else{
			$status = "UNDEFINED";
		}

		return $status;
	}
	
	
	public function insertNewUrl($page_id,$page_url){
		$sql = "INSERT INTO PageUrls ( pageurl_id , pageurl_page_id , pageurl_url ) VALUES (NULL , '$page_id', '$page_url');";
    	$this->database->rawQuery($sql);
    	$this->touchPage($page_id);
	}
	
	public function getPageUrls($page_id){
		$sql = "SELECT * FROM PageUrls WHERE pageurl_page_id = '$page_id'";
		$pageUrls = $this->database->queryToArray($sql);
		return $pageUrls;

	}
	
	public function checkUrl($page_url){
		$sql="SELECT * from  PageUrls WHERE pageurl_url = '$page_url'";
		$url_count = $this->database->howMany($sql);
		return $url_count;
	}
	
	public function updatePageUrl($page_id,$pageurl_id,$page_url){
		$sql = "UPDATE PageUrls SET pageurl_url = '$page_url' WHERE pageurl_id = '$pageurl_id' ";
    	$result = $this->database->rawQuery($sql);
    	$this->touchPage($page_id);
	}
	
	public function deletePageUrl($page_id,$pageurl_id,$page_url){
		$sql = "SELECT * FROM PageUrls WHERE pageurl_page_id = '$page_id'";
		$pageUrls = $this->database->queryToArray($sql);
		if(count($pageUrls)>1){
			$query = "DELETE FROM PageUrls WHERE pageurl_id='$pageurl_id' LIMIT 1";
		$this->database->query($query);
 		}
		
	}

	
	
	
	
	public function touchPage($page_id){
		
		$page = new SmartestPage;
		$page->hydrate($page_id);
		$page->touch();
		
	}
	
	public function getPageProperties($page_id){

		$site_id = $this->database->specificQuery("page_site_id", "page_id", $page_id, "Pages");
		$sql = "SELECT * FROM PageProperties WHERE pageproperty_site_id ='$site_id' ";
		$pageproperties = $this->database->queryToArray($sql);
		
		foreach($pageproperties as $key=>$pageproperty){
			$propertyid=$pageproperty['pageproperty_id'];
			// $value = $this->database->specificQuery("pagepropertyvalue_id", "pagepropertyvalue_pageproperty_id", $id, "PagePropertyValues");
			$definedPagePropertyValues = $this->getDefinedProperties($propertyid,$page_id);

			if($definedPagePropertyValues){
				$pageproperty['draft_value']=$definedPagePropertyValues['pagepropertyvalue_draft_value'];
				$pageproperty['live_value']=$definedPagePropertyValues['pagepropertyvalue_live_value'];
				$define[]=$pageproperty;
			}else{
				$undefine[]=$pageproperty;
			}
		}
		
		return array('define'=>$define,"definedPagePropertyValues"=>$definedPagePropertyValues,'undefine'=>$undefine);
	}
	
	public function getDefinedProperties($propertyid, $page_id){
		$sql = "SELECT pagepropertyvalue_draft_value, pagepropertyvalue_live_value FROM PagePropertyValues WHERE pagepropertyvalue_page_id ='$page_id' AND pagepropertyvalue_pageproperty_id ='$propertyid' ";
		$definedPageProperties = $this->database->queryToArray($sql);//print_r($definedPageProperties[0]);
		return $definedPageProperties[0];
	}
	
	public function getDefinedPageProperties($PageProperties,$definedPagePropertyValues){
			
		if(is_array($PageProperties)){
			
			foreach($PageProperties as $key=>$definedPageProperty){
				foreach($definedPagePropertyValues as $definedPagePropertyValue){
					if($definedPagePropertyValue['pagepropertyvalue_pageproperty_id']==$definedPageProperty['pageproperty_id']){
						
						// don't understand wtf this was supposed to be for - Sereen code:
						
						// $PageProperties[$key]['pagepropertyvalue_live_value'] = $definedPagePropertyValue['pagepropertyvalue_live_value'];
						// $PageProperties[$key]['pagepropertyvalue_draft_value'] = $definedPagePropertyValue['pagepropertyvalue_draft_value'];
					}
				}
			}
		}
		
		return $PageProperties;
	}

	public function setupLayoutPreset($plp_name,$assets,$master_template,$user_id,$page_id){
		
		$sql = "INSERT INTO  PageLayoutPresets (plp_label, plp_master_template_name, plp_created_by_user_id, plp_orig_from_page_id) VALUES ( '$plp_name', '$master_template', '$user_id', '$page_id');";
    	$this->database->rawQuery($sql);
		
		$id = $this->database->getInsertId();
		$count = count($assets);
		
		for($i=0;$i<$count;$i++){
			$asse = explode(',',$assets[$i]);

			$sql = "INSERT INTO  PageLayoutPresetDefinitions (plpd_preset_id, plpd_container_id, plpd_asset_id) VALUES ( '$id', '$asse[1]', '$asse[0]' );";
    		$this->database->rawQuery($sql);	
		}
	}
	
	public function getPagePresets($site_id=''){
		
		$sql = "SELECT * FROM PageLayoutPresets";
		
		if(is_numeric($site_id)){
		    $sql .= " WHERE plp_site_id='".$site_id."'";
		}
		
		$results = $this->database->queryToArray($sql);
		return $results;
		
	}
}