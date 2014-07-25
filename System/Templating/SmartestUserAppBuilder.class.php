<?php

class SmartestUserAppBuilder extends SmartestBasicRenderer{
    
    protected $page;
    
    public function __construct(){
        
        parent::__construct('main');
        
        $this->left_delimiter = '<'.'?sm:';
		$this->right_delimiter = ':?'.'>';
		
		$this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/UserAppFramework/";
        
    }
    
    // Unlike with SmartestWebPageBuilder this function is optional:
    public function getPage(){
        return $this->page;
    }
    
    // Unlike with SmartestWebPageBuilder this function is optional:
    public function assignPage($page){
        $this->page = $page;
        if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $page->getSiteId());
        }
    }
    
    public function hasPage(){
        return ($this->page instanceof SmartestPage);
    }
    
    public function renderTemplateTag($requested_file){
        
        /* if(!$GLOBALS['user_action_has_page']){
            $this->raiseError("User action does not have a default background page");
            return false;
        } */
        
        if(SmartestStringHelper::getDotSuffix($requested_file) != 'tpl'){
	        $requested_file .= '.tpl';
	    }
        
        $directories = array('Presentation/Layouts/');
        
        $file_found = false;
        
        foreach($directories as $dir){
            if(is_file(SM_ROOT_DIR.$dir.$requested_file)){
                $file_found = true;
                $template = SM_ROOT_DIR.$dir.$requested_file;
                continue;
            }
        }
        
        if($file_found){
	        $render_process_id = SmartestStringHelper::toVarName('template_'.SmartestStringHelper::removeDotSuffix($requested_file).'_'.substr(microtime(true), -6));
	        $child = $this->startChildProcess($render_process_id);
	        $child->caching = false;
	        $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
	        $child->assign('this', $this->_tpl_vars['this']);
	        if($this->page instanceof SmartestPage){
	            $child->assignPage($this->page);
	        }
	        $content = $child->fetch($template);
	        $this->killChildProcess($child->getProcessId());
	        return $content;
        }else{
            return $this->raiseError('Template \''.$requested_file.'\' not found');
        }
        
    }
    
    public function getRepeatBlockData($params){
        
        $this->caching = false;
        $this->_repeat_char_length_aggr = 0;
        
        if(is_array($params['from']) || $params['from'] instanceof SmartestArray){
            return $params['from'];
        }
        
        // print_r($params['from']);
        
        if($params['from'] instanceof SmartestSortableItemReferenceSet){
            return $params['from']->getItems();
        }
        
        if(count(explode(':', $params['from'])) > 1){
            $parts = explode(':', $params['from']);
            $type = $parts[0];
            $name = $parts[1];
        }else{
            if($params['from'] == '_authors'){
                $type = 'authors';
                $uh = new SmartestUsersHelper;
                return $uh->getCreditableUsersOnSite($this->page->getSiteId());
            }else{
                $type = 'set';
                $name = $params['from'];
            }
        }
        
        switch($type){
            
            case "tag":
                
                if(count(explode(';', $params['from'])) > 1){
                    $sub_type_def = end(explode(';', $params['from']));
                    $sub_type = substr($params['from'], 0, 5);
                }else{
                    $sub_type = 'page';
                }
                
                break;
            
            case "gallery":
            $g = new SmartestAssetGroup;
            if($g->findBy('name', $name, $this->page->getSiteId())){
                
                if($g->getIsGallery()){
                    if(isset($params['skip_memberships']) && !SmartestStringHelper::toRealBool($params['skip_memberships'])){
                        return $g->getMemberships();
                    }else{
                        return $g->getMembers();
                    }
                }else{
                    // the file group is not a gallery
                    return $this->raiseError('Specified file group \''.$name.'\' is not a gallery.');
                }
            }else{
                // no file group with that name
                return $this->raiseError('No file group exists with the name \''.$name.'\'.');
            }
            
            case "pagegroup":
            case "page_group":
            
            $g = new SmartestPageGroup;
            if($g->findBy('name', $name, $this->page->getSiteId())){
                if(isset($params['assignhighlight'])){
                    $highlighted_page = $g->determineHighlightedMemberOnPage($this->page, $this->getDraftMode());
                    if($highlighted_page){
                        $this->assign($params['assignhighlight'], $highlighted_page);
                    }
                }
                return $g->getMembers($this->getDraftMode());
            }else{
                // no file group with that name
                return $this->raiseError('No file group exists with the name \''.$name.'\'.');
            }
            
            break;
            
            case "set_feed_Items":
            
            $set = new SmartestCmsItemSet;
            
            if($set->findBy('name', $name, $this->page->getSiteId()) || $this->getDataSetsHolder()->h($name)){
                if($set->isAggregable()){
                    
                    if(isset($params['limit']) && is_numeric($params['limit'])){
                        
                        $limit = $params['limit'];
                        $items = $set->getFeedItems();
                        
                        if(is_array($items)){
                            return array_slice($items, 0, $limit);
                        }else{
                            return array();
                        }
                        
                    }else{
                        return $set->getFeedItems();
                    }
                    
                }else{
                    return $this->raiseError("Data set with name '".$name."' does not have feed properties.");
                }
            }
            
            break;
            
            case "set":
            case "dataset":
            default:
                
                if(isset($params['query_vars'])){
                    $query_vars = SmartestStringHelper::parseNameValueString($params['query_vars']);
                }else{
                    $query_vars = array();
                }
                
                $set = new SmartestCmsItemSet;
                
                if(isset($params['limit']) && is_numeric($params['limit'])){
                    $limit = $params['limit'];
                }else{
                    $limit = null;
                }
                
                if($set->findBy('name', $name, $this->page->getSiteId())){
        		    
        		    // echo "site ".$this->page->getSiteId();
        		    $dah = new SmartestDataAppearanceHelper;
                    $dah->setDataSetAppearsOnPage($set->getId(), $this->getPage()->getId());
                    $start = (isset($params['start']) && is_numeric($params['start'])) ? $params['start'] : 1;
                    
                    $set_mode = $this->getDraftMode() ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT ;
        		    // $items = $set->getMembers($set_mode, $limit, $start, $query_vars);
        		    $items = $set->getMembersPaged($set_mode, $limit, $start, $query_vars, $this->page->getSiteId());
        		    
        		}else if(preg_match('/^all_/', $name)){
        		    $model_varname = substr($name, 4);
        		}else{
        		    $items = array();
        		}
                
                // $this->caching = true;
         		return $items;
         		
        }
 		
    }
    
}