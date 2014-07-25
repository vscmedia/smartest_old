<?php

class PagesAjax extends SmartestSystemApplication{

    public function tagPage(){
	    
	    $page = new SmartestPage;
	    
	    if($page->find($this->getRequestParameter('page_id'))){
	        
	        if($page->tag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	        }else{
	            header('HTTP/1.1 500 Internal Server Error');
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function unTagPage(){
	    
	    $page = new SmartestPage;
	    
	    if($page->find($this->getRequestParameter('page_id'))){
	        
	        if($page->untag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	        }else{
	            header('HTTP/1.1 500 Internal Server Error');
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function setPageGroupLabelFromInPlaceEditField(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('pagegroup_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $group->setLabel($this->getRequestParameter('new_label'));
	        $group->save();
	        echo $this->getRequestParameter('new_label');
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function setPageGroupNameFromInPlaceEditField(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('pagegroup_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $group->setName(SmartestStringHelper::toVarName($this->getRequestParameter('new_name')));
	        $group->save();
	        echo SmartestStringHelper::toVarName($this->getRequestParameter('new_name'));
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function updatePageGroupOrder(){
	    
	    $group = new SmartestPageGroup;

            if($group->find($this->getRequestParameter('group_id'))){
                header('HTTP/1.1 200 OK');
                if($this->getRequestParameter('page_ids')){
                    $group->setNewOrderFromString($this->getRequestParameter('page_ids'));
                }
            }

            exit;
	    
	}
	
	public function pageUrls(){
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
		if(isset($type_index[$page_webid])){
		    if(($type_index[$page_webid] == 'ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_DATASET') && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		$this->send(($this->getRequestParameter('responseTableLinks') && !SmartestStringHelper::toRealBool($this->getRequestParameter('responseTableLinks')) ? false : true), 'link_urls');
		
		if($page->hydrate($page_webid)){
		    
		    if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
	            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
	                $page->setPrincipalItem($item);
	                $this->send($item, 'item');
	            }
            }
		    
		    $ishomepage = ($this->getSite()->getTopPageId() == $page->getId());
		    $this->send($ishomepage, "ishomepage");
		    $this->send($this->getSite(), 'site');
		    $this->send($page, 'page');
		    
		}
	    
	}
	
	public function setPageValueFromAjaxForm(){
	    
	    $page = new SmartestPage;
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->findby('webid', $page_webid)){
	        
	        switch($this->getRequestParameter('name')){

                case "title":
                if(strlen($this->getRequestParameter('value'))){
                    $page->setTitle($this->getRequestParameter('value'));
                    $page->save();
                    echo $this->getRequestParameter('value');
                }else{
                    echo $page->getTitle();
                }
                exit;
                
                case "name":
                if(strlen($this->getRequestParameter('value')) && $this->getUser()->hasToken('edit_page_name')){
                    $v = SmartestStringHelper::toSlug($this->getRequestParameter('value'));
                    $page->setName($v);
                    $page->save();
                    echo $v;
                }else{
                    echo $page->getName();
                }
                exit;
                
                case "parent":
                if(strlen($this->getRequestParameter('value')) && is_numeric($this->getRequestParameter('value'))){
                    $page->setParent($this->getRequestParameter('value'));
                    $page->save();
                }
                exit;
                
                case "cache_frequency":
                if(strlen($this->getRequestParameter('value'))){
                    $page->setCacheInterval($this->getRequestParameter('value'));
                    $page->save();
                }
                exit;
                
                case "force_static_title":
                if(strlen($this->getRequestParameter('value'))){
                    $page->setForceStaticTitle($this->getRequestParameter('value') ? 1 : 0);
                    $page->save();
                }
                exit;

    	    }
	        
	    }
	    
	}
	
	public function loadAssetGroupDropdownForNewPlaceholderForm(){
	    
	    $h = new SmartestAssetClassesHelper;
	    $type_code = $this->getRequestParameter('placeholder_type');
		
		if(in_array($type_code, $h->getTypeCodes())){
		    
		    $groups = $h->getAssetGroupsForPlaceholderType($type_code, $this->getSite()->getId());
		    $this->send($groups, 'groups');
		    $this->send($type_code, 'selected_type');
		    
		}
	    
	}
	
	public function clearPagesCache(){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        if($this->getUser()->hasToken('clear_pages_cache')){
            
                $page_prefix = 'site'.$this->getSite()->getId().'_';
            
                $cache_files = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Cache/Pages/');
            
                if(is_array($cache_files)){
                
                    $deleted_files = array();
                    $failed_files = array();
                    $untouched_files = array();
                
                    foreach($cache_files as $f){
                    
                        $path = SM_ROOT_DIR.'System/Cache/Pages/'.$f;
                    
                        if(strlen($f) && $page_prefix == substr($f, 0, strlen($page_prefix))){
                            // echo "deleting ".$path.'...<br />';
                            if(@unlink($path)){
                                $deleted_files[] = $f;
                            }else{
                                $failed_files[] = $f;
                            }
                        }else{
                            $untouched_files = $f;
                        }
                    }
                    
                    SmartestLog::getInstance('site')->log("{$this->getUser()} cleared the pages cache. ".count($deleted_files)." files were removed.", SmartestLog::USER_ACTION);
                    
                    $this->send(true, 'show_result');
                    $this->send($deleted_files, 'deleted_files');
                    $this->send(count($deleted_files), 'num_deleted_files');
                    $this->send($failed_files, 'failed_files');
                    $this->send($untouched_files, 'untouched_files');
                    $this->send(SM_ROOT_DIR.'System/Cache/Pages/', 'cache_path');
                
                }else{
                
                    $this->send(false, 'show_result');
                
                }
            
            }
            
        }
	    
	}

}