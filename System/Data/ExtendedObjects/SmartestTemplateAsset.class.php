<?php 

class SmartestTemplateAsset extends SmartestAsset{
    
    protected $_template_file;
    protected $_base_dir = '';
    
    public function getFile(){
        
        $this->_base_dir = SM_ROOT_DIR.$this->getStorageLocation();
        
        if(!$this->_template_file){
            
            $this->_template_file = new SmartestFile();
            
            if($this->_template_file->loadFile($this->_base_dir.$this->getUrl())){
                    
            }else{
                // file doesn't exist or isn't readable
            }
            
        }
        
        return $this->_template_file;
        
    }
    
    public function find($id){
        
        if(parent::find($id)){
            $this->getFile();
            return true;
        }else{
            return false;
        }
        
    }
    
    public function findBy($field, $value, $site_id=''){
	    
	    $sql = $this->getRetrievalSqlQuery($value, $field, $site_id);
	    $h = new SmartestTemplatesLibraryHelper;
	    $sql .= " AND asset_type IN ('".implode("', '", $h->getTypeCodes())."')";
	    
	    $result = $this->database->queryToArray($sql);
	    $this->_last_query = $sql;
	    
	    if(count($result)){
	
		    foreach($result[0] as $name => $value){
			    if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
				    $this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
			    }else if(isset($this->_no_prefix[$name])){
				    $this->_properties[$name] = $value;
			    }
		    }
	
		    $this->_came_from_database = true;
		    
		    return true;
	    }else{
		    return false;
	    }
	    
	}
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "status":
            return "imported";
            
            case "action_url":
            return $this->getRequest()->getDomain()."templates/editTemplate?template=".$this->getId();
            
            case "label":
            return $this->getUrl();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function delete(){
        if(unlink($this->getFullPathOnDisk())){
            $this->database->rawQuery("DELETE FROM Assets WHERE asset_id='".$this->_properties['id']."' LIMIT 1");
            return true;
        }else{
            return false;
        }
    }
    
    public function getTypeConvertOptions(){
        
        $type = $this->getTypeInfo();
        
        if(isset($type['allow_convert_to']) && is_array($type['allow_convert_to'])){
		    return $type['allow_convert_to'];
		}else{
		    return array();
		}
        
    }
    
    public function isInUse(){
        
        switch($this->getType()){
            case "SM_ASSETTYPE_MASTER_TEMPLATE":
                $sql = "SELECT DISTINCT Sites.*, page_site_id FROM Pages, Sites WHERE (page_draft_template='".$base_name."' OR page_live_template='".$base_name."') AND Pages.page_site_id=Sites.site_id";
                $result = $this->database->queryToArray($sql);
                break;
            case "SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE":
                $sql = "SELECT DISTINCT Sites.*, page_site_id, list_page_id FROM Pages, Sites, Lists WHERE (list_draft_template_file='".$base_name."' OR list_live_template_file='".$base_name."') AND Lists.list_type='SM_LIST_SIMPLE' AND Lists.list_page_id=Pages.page_site_id AND Pages.page_site_id=Sites.site_id";
                $result = $this->database->queryToArray($sql);
                break;
            case "SM_ASSETTYPE_ART_LIST_TEMPLATE":
                $sql = "SELECT DISTINCT Sites.*, page_site_id, list_page_id FROM Pages, Sites, Lists WHERE ((list_draft_template_file='".$base_name."' OR list_live_template_file='".$base_name."') OR (list_draft_header_template='".$base_name."' OR list_live_header_template='".$base_name."') OR (list_draft_footer_template='".$base_name."' OR list_live_footer_template='".$base_name."')) AND Lists.list_type='SM_LIST_ARTICULAED' AND Lists.list_page_id=Pages.page_site_id AND Pages.page_site_id=Sites.site_id";
                $result = $this->database->queryToArray($sql);
                break;
            case "SM_ASSETTYPE_CONTAINER_TEMPLATE":
                $sql = "SELECT Assets.asset_id FROM Assets, AssetClasses, AssetIdentifiers WHERE AssetClasses.assetclass_type='SM_ASSETCLASS_CONTAINER' AND AssetClasses.assetclass_id=AssetIdentifiers.assetidentifier_assetclass_id AND ((AssetIdentifiers.assetidentifier_draft_asset_id=Assets.asset_id AND AssetIdentifiers.assetidentifier_draft_asset_id='".$this->getId()."') OR (AssetIdentifiers.assetidentifier_live_asset_id=Assets.asset_id AND AssetIdentifiers.assetidentifier_live_asset_id='".$this->getId()."'))";
                $result = $this->database->queryToArray($sql);
                break;
        }
        
        return (bool) count($result);
        
    }
    
    public function isConvertable(){
        
        if(count($this->getTypeConvertOptions()) && !$this->isInUse()){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function getArrayForElementsTree($level){
	    
	    $info = array();
	    $info['asset_id'] = $this->getId();
	    $info['asset_webid'] = $this->getWebid();
	    $info['asset_type'] = $this->getType();
	    $info['assetclass_name'] = $this->getStringid();
	    $info['assetclass_id'] = 'asset_'.$this->getId();
	    $info['defined'] = 'PUBLISHED';
	    $info['exists'] = 'true';
	    $info['filename'] = $this->getUrl();
	    $info['type'] = 'template';
	    $level++;
	    return array('info'=>$info, 'level'=>$level);
	}
	
	public function getContent(){
	    
	    $file = $this->getFullPathOnDisk();
	    
	    if(is_file($file)){
		    $contents = SmartestFileSystemHelper::load($file, true);
		    return $contents;
	    }
	    
	}
	
	public function getContentForEditor(){
	    return htmlentities($this->getContent(), ENT_COMPAT, 'UTF-8');
	}
	
	public function setContent($content){
	    
	    $file = $this->getFullPathOnDisk();
	    return SmartestFileSystemHelper::save($file, $content, true);
	    
	    // if(is_file($file)){
		//    $contents = SmartestFileSystemHelper::load($file, true);
		//    return $contents;
	    // }
	    
	}
	
	public function getTags($tagnames, SmartestPage $page, $level=0, $version="draft", $item_id=false){
		
		// This function is not yet finished. It will replace functionality in PagesManager.
		$i = 0;
		$c = 0;
		$info = array();
		$elements = array();
		$site_id = $page->getSiteId();
		$version = ($version == 'live') ? 'live' : 'draft';
		$draft = ($version == 'draft') ? true : false;
		$potential_tagnames = array('placeholder', 'container', 'itemspace', 'field', 'template');
		
		if(!is_array($tagnames)){
		    if(is_string($tagnames)){
		        if(in_array($tagnames, $potential_tagnames)){
		            $tagnames = array($tagnames);
		        }
	        }
		}
		
		$tags = $this->parseTagsByName($tagnames);
		
		foreach($tags as $t){
		    
		    $elements[$c] = new SmartestParameterHolder($t->getParameter('type').' element on page \''.$page->getTitle().'\'.');
		    $info = new SmartestParameterHolder('element info');
		    
		    switch($t->getParameter('type')){
		        
		        case "placeholder":
		        
		        // $info[$i]['info'] = $this->getAssetClassInfo($placeholderName);
		        
		        $placeholder = new SmartestPlaceholder;
		        $info->setParameter('assetclass_name', $t->getParameter('name'));
		        $info->setParameter('type', 'placeholder');
		        
		        if($placeholder->hydrateBy('name', $t->getParameter('name'))){
		            
		            $info->setParameter('exists', true);
		            $info->setParameter('defined', $placeholder->isDefinedOnPage($page->getId()));
		            $info->setParameter('assetclass_id', 'placeholder_'.$t->getParameter('name'));
		            
		            if($info->getParameter('defined')){
		                
		                $asset_id = $placeholder->getDefinitionOnPage($page->getId(), $draft)->getAssetId();
		                $info->setParameter('asset_id', $asset_id);
		                
		                $asset = new SmartestAsset();
		                
		                // Left off here!!!!
		                if($asset->find($asset_id)){

        				    $child = $asset->getArrayForElementsTree($level);

        				    if($asset->isParsable()){

        				        $child['children'] = $asset->getTextFragment()->getAttachmentsForElementsTree($level+2, $version);

        				        foreach($child['children'] as $key => $attachment){
        				            $child['children'][$key]['info']['assetclass_id'] = $assetObj->getStringid().'_'.$attachment['info']['assetclass_name'];
        				            $child['children'][$key]['info']['assetclass_name'] = $assetObj->getStringid().'_'.$attachment['info']['assetclass_name'];

        				            if(isset($child['children'][$key]['asset_object']) && is_object($child['children'][$key]['asset_object'])){
        				                $child_asset = $child['children'][$key]['asset_object'];
        				                $child_asset_array = $child_asset->getArrayForElementsTree($level+2);
        				                $child['children'][$key]['children'] = array($child_asset_array);
        				            }
        				        }
        			        }

        				    $info[$i]['children'] = array($child);
        				}
		                
	                }
		            
		            /* if($version == "live"){
    					// $asset = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), false, $item_id);
    				}else{
    					// $asset = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), true, $item_id);
    				} */
		            
		        }else{
		            $info->setParameter('exists', false);
		        }
		        
				$info[$i]['info']['asset_id'] = $asset;
				$assetObj = new SmartestAsset();
				
				/* if($assetObj->hydrate($asset)){
				    
				    $child = $assetObj->getArrayForElementsTree($level);
				    
				    if($assetObj->isParsable()){
				        
				        $child['children'] = $assetObj->getTextFragment()->getAttachmentsForElementsTree($level+2, $version);
				        
				        foreach($child['children'] as $key => $attachment){
				            $child['children'][$key]['info']['assetclass_id'] = $assetObj->getStringid().'_'.$attachment['info']['assetclass_name'];
				            $child['children'][$key]['info']['assetclass_name'] = $assetObj->getStringid().'_'.$attachment['info']['assetclass_name'];
				            
				            if(isset($child['children'][$key]['asset_object']) && is_object($child['children'][$key]['asset_object'])){
				                $child_asset = $child['children'][$key]['asset_object'];
				                $child_asset_array = $child_asset->getArrayForElementsTree($level+2);
				                $child['children'][$key]['children'] = array($child_asset_array);
				            }
				        }
			        }
				    
				    $info[$i]['children'] = array($child);
				} */
				
				$info[$i]['level'] = $level;
		        
		        break;
		        
		        case "container":
		        break;
		        
		        case "itemspace":
		        break;
		        
		        case "field":
		        
		        $field = new SmartestPageField;
		        
		        // a simple 'hydrateBy' did not take into account that fields are not cross-site and multiple fields may exist of the same name (one for each site)
		        $correct_sql = "SELECT * FROM PageProperties WHERE pageproperty_name='".$t->getParameter('name')."' AND pageproperty_site_id='".$site_id."'";
                $result = $this->database->queryToArray($correct_sql);
                $field->hydrate($result[0]);
                
                $info->setParameter('exists', (count($result) > 0) ? 'true' : 'false');
				$info->setParameter('defined', $field->isDefinedOnPage($page->getId()));
				$info->setParameter('assetclass_name', $t->getParameter('name'));
				$info->setParameter('assetclass_id', 'field_'.$field->getId());
				
				break;
				
		        case "template":
		        break;
		        
		    }
		    
		    $elements[$c]->setParameter('info', $info);
		    $c++;
		    
		}
		
		/* $fieldNames = $this->getFieldNames();
			
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

		} */
		
		$placeholderNames = $this->getTemplatePlaceholderNames($template_file_path);
			
		if(is_array($placeholderNames)){
		
			foreach($placeholderNames as $placeholderName){
			    
			    $info[$i]['info'] = $this->getAssetClassInfo($placeholderName);
				
				$info[$i]['info']['exists'] = $this->getAssetClassExists($placeholderName);
				$info[$i]['info']['defined'] = $this->getAssetClassDefinedOnPage($placeholderName, $page->getId());
				
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
				            $child['children'][$key]['info']['assetclass_name'] = $assetObj->getStringid().'_'.$attachment['info']['assetclass_name'];
				            
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
						$asset = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), false, $item_id);
					}else{
						$asset = $this->getAssetClassDefinition($info[$i]['info']['assetclass_name'], $page->getId(), true, $item_id);
					}
					
					$assetObj = new SmartestTemplateAsset();
					
					if($assetObj->hydrate($asset)){
					
					    $info[$i]['info']['asset_id'] = $asset;
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
		
		return $info;
	}
	
	public function parseTagsByName($name){
	    
	    if($contents = $this->getFile()->getContent()){
		    
		    if(is_array($name)){
		        $name = implode('|', $name);
		    }
		    
		    $a = "([A-Za-z0-9_]+)"; // attribute name
		    $v = "(\$\w([\w\._]+))"; // variable value
		    $l = "\"([^\"]*)\""; // literal value
            
            $regex = "/<\?sm:(".$name.")((\s".$a."\s?=\s?(".$v."|".$l."))*):\?>/i";
            
			$regexp = preg_match_all($regex, $contents, $matches, PREG_SET_ORDER);
			
			$tags = array();
			
			foreach($matches as $array){
			    $tags[] = $this->processTagFromRegex($array);
			}
			
			// unset to save memory
			unset($contents);
			return $tags;

		}else{
			return false;
		}
	}
	
	public function processTagFromRegex($array){
	    
	    $ph = new SmartestParameterHolder("Template tag: ".$array[1]);
	    $ph->setParameter('type', $array[1]);
	    
	    $attribute_string = $array[2];
	    
	    $a = "([A-Za-z0-9_]+)"; // attribute name
	    $v = "(\\$([A-Za-z][\w\._]+))"; // variable value
	    $l = "\"([^\"]*)\""; // literal value
	    
	    $regex = "/\s".$a."\s?=\s?(".$v."|".$l.")/";
	    
	    $attributes = preg_match_all($regex, $attribute_string, $matches, PREG_SET_ORDER);
	    
	    $attributes = array();
	    
	    foreach($matches as $attr){
	        
	        $attribute = new SmartestParameterHolder('Tag attribute: '.$attr[1]);
	        
	        $attribute->setParameter('name', $attr[1]);
	        
	        if($attr[3]){
	            $attribute->setParameter('type', 'variable');
	            $attribute->setParameter('value', $attr[3]);
	        }else{
	            $attribute->setParameter('type', 'literal');
	            $attribute->setParameter('value', $attr[5]);
	        }
	        
	        $attributes[$attr[1]] = $attribute;
	    }
	    
	    if(isset($attributes['name'])){
	        $ph->setParameter('name', $attributes['name']->getParameter('value'));
	    }
	    
	    $ph->setParameter('attributes', $attributes);
	    
	    return $ph;
	    
	}
	
	public function getImportedStylesheets(){
	    
	    $regex = '/<link rel="stylesheet" href="([^"]+(Resources\/Stylesheets\/([^"]+)))"/mi';
	    $result = preg_match_all($regex, $this->getContent(), $matches);
	    $stylesheets = array();
	    
	    foreach($matches[3] as $m){
	        if(is_file(SM_ROOT_DIR.'Public/Resources/Stylesheets/'.$m)){
	            $a = new SmartestAsset;
	            if($a->findBy('url', $m)){
	                $stylesheets[] = $a;
	            }
	        }
	    }
	    
	    return $stylesheets;
	    
	}
	
	public function clearRecentlyEditedInstances($site_id, $user_id=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_TEMPLATES');
	    
	    $q->setTargetEntityByIndex(1);
	    
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        if(is_numeric($user_id)){
            $q->addQualifyingEntityByIndex(2, $user_id);
        }
        
        $q->delete();
	    
	}
    
}