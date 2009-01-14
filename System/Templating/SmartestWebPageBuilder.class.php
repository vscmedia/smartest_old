<?php

class SmartestWebPageBuilder extends SmartestEngine{
    
    protected $templateHelper;
	protected $page;
	protected $_page_rendering_data = array();
	protected $_page_rendering_data_retrieved = false;
	protected $draft_mode = false;
	protected $_items = array();
	
	public function __construct($pid){
	    
	    parent::__construct($pid);
	    
	    $this->_context = SM_CONTEXT_CONTENT_PAGE;
	    
	    $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/WebPageBuilder/";
	    $this->left_delimiter = '<'.'?sm:';
		$this->right_delimiter = ':?'.'>';
		
		if(!defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS')){
		    define('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS', true);
		}
	    
	}
	
	public function getPage(){
        return $this->page;
    }
    
    public function assignPage($page){
        $this->page = $page;
    }
    
    public function setPageRenderingData($data){
        $this->_page_rendering_data = &$data;
        $this->_tpl_vars['this'] = $this->_page_rendering_data;
        $this->_page_rendering_data_retrieved = true;
    }
    
    public function getDraftMode(){
        return $this->draft_mode;
    }
    
    public function setDraftMode($mode){
        
        $this->draft_mode = SmartestStringHelper::toRealBool($mode);
        
        if($this->page){
            $this->page->setDraftMode($mode);
        }
        
    }
    
    public function startChildProcess($pid, $type=''){
        
        $pid = SmartestStringHelper::toVarName($pid);
        
        if($this->_page_rendering_data_retrieved){
        
	        $cp = parent::startChildProcess($pid);
	        $cp->setDraftMode($this->getDraftMode());
	        $cp->assignPage($this->page);
	        $cp->setPageRenderingData($this->_page_rendering_data);
	        
            return $this->_child_processes[$pid];
        
        }
	}
	
	public function raiseError($error_msg='Unknown Template Error'){
	    
	    $this->_log($error_msg);
	    
	    if($this->getDraftMode()){
	        $this->assign('_error_text', $error_msg);
	        $error_markup = $this->fetch(SM_ROOT_DIR."System/Presentation/WebPageBuilder/markup_error.tpl");
	        echo $error_markup;
        }
	}
    
    public function prepareForRender(){
        
        $this->page->loadAssetClassDefinitions();
	    $this->page->loadItemSpaceDefinitions();
	    $this->setPageRenderingData($this->page->fetchRenderingData());
	    $this->_tpl_vars['this'] = $this->_page_rendering_data;
        
    }
    
    public function renderPage($page, $draft_mode=false){
	    
	    $this->page = $page;
	    $this->setDraftMode($draft_mode);
	    
	    $this->prepareForRender();
	    
	    if($draft_mode){
	        $template = SM_ROOT_DIR."Presentation/Masters/".$page->getDraftTemplate();
	    }else{
	        $template = SM_ROOT_DIR."Presentation/Masters/".$page->getLiveTemplate();
	    }
	    
	    if(!defined('SM_CMS_PAGE_ID')){
		    define('SM_CMS_PAGE_ID', $this->page->getId());
		}
	    
	    if(!file_exists($template)){
	        
	        $this->assign('required_template', $template);
	        $template = SM_ROOT_DIR.'System/Presentation/Error/_websiteTemplateNotFound.tpl';
	        $this->run($template, array());
	        
	    }else{
	    
	        ob_start();
	        $this->run($template, array());
	        $content = ob_get_contents();
	        ob_end_clean();
	    
	        return $content;
        }
	}
    
    public function renderContainer($container_name, $params, $parent){
        
        if($this->_context == SM_CONTEXT_CONTENT_PAGE){
            
            if($this->getPage()->hasContainerDefinition($container_name, $this->getDraftMode())){
                
                $container = $this->getPage()->getContainerDefinition($container_name, $this->getDraftMode());
                $this->run($container->getTemplateFilePath(), array());
                
                if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
		    
    			    $edit_link = '';
		    
    			    if(is_object($container->getTemplate())){
    			        // TODO: Make it an admin-controlled setting as to whether containers are changeable in the preview screen
    			        // $edit_link .= "<a title=\"Click to edit template: ".$container->getTemplate()->getUrl()."\" href=\"".SM_CONTROLLER_DOMAIN."templates/editTemplate?template_id=".$container->getTemplate()->getId()."&amp;type=SM_CONTAINER_TEMPLATE&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this template--></a>";
    			    }
		    
    			    // $edit_link .= "<a title=\"Click to edit definition for container: ".$container_name."\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
		    
    		    }else{
    			    // $edit_link = "<!--edit link-->";
    		    }
                
            }
            
            /* $container = new SmartestContainerDefinition;
        
            if($container->load($container_name, $this->getPage(), $this->getDraftMode())){
            
                if($container->getTemplateFilePath()){
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$container->getTemplateFilePath(), 'smarty_include_vars'=>array()));
                    $this->run($container->getTemplateFilePath(), array());
                }
            
                if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			    
    			    $edit_link = '';
			    
    			    if(is_object($container->getTemplate())){
    			        // TODO: Make it an admin-controlled setting as to whether containers are changeable in the preview screen
    			        // $edit_link .= "<a title=\"Click to edit template: ".$container->getTemplate()->getUrl()."\" href=\"".SM_CONTROLLER_DOMAIN."templates/editTemplate?template_id=".$container->getTemplate()->getId()."&amp;type=SM_CONTAINER_TEMPLATE&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this template--></a>";
    			    }
			    
    			    // $edit_link .= "<a title=\"Click to edit definition for container: ".$container_name."\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    
    		    }else{
    			    // $edit_link = "<!--edit link-->";
    		    }
		    
    		    return $edit_link;
            
            } */
        
        }else{
            
            return $this->raiseError('Container tag can only be used in page context.');
            
        }
        
    }
    
    public function renderTemplateTag($requested_file){
        
        if(SmartestStringHelper::getDotSuffix($requested_file) != 'tpl'){
	        $requested_file .= '.tpl';
	    }
        
        $directories = array('Presentation/Layouts/');
        
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
	        $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
	        $content = $child->fetch($template);
	        $this->killChildProcess($child->getProcessId());
	        return $content;
        }else{
            return $this->raiseError('Template \''.$requested_file.'\' not found');
        }
        
    }
    
    public function renderPlaceholder($placeholder_name, $params, $parent){
        
        $assetclass_types = SmartestDataUtility::getAssetClassTypes();
        
        $display = (isset($params['display']) && in_array($params['display'], array('file', 'full', 'normal'))) ? $params['display'] : 'normal';
        
        if($this->getPage()->hasPlaceholderDefinition($placeholder_name, $this->getDraftMode())){
            
            $placeholder = $this->getPage()->getPlaceholderDefinition($placeholder_name, $this->getDraftMode());
            
            if(array_key_exists($placeholder->getType(), $assetclass_types)){
                
                $asset = $placeholder->getAsset($this->getDraftMode());
                
                if(is_object($asset)){
                    
                    if($display == 'file'){
                        
                        return $asset->getUrl();
                        
                    }else if($display == 'full'){
                        
                        if($asset->usesLocalFile()){
                            
                            return $asset->getFullWebPath();
                            
                        }else{
                            
                            return $this->raiseError('display="full" used on asset type that does not have a local file: '.$asset->getType());
                            
                        }
                        
                    }else{
                    
                        $render_data = array();
                    
                        if($asset->isImage()){
                            $render_data['width'] = $asset->getWidth();
                            $render_data['height'] = $asset->getHeight();
                        }
                    
                        if($this->getDraftMode()){
                            $rd = $placeholder->getDraftRenderData();
                        }else{
                            $rd = $placeholder->getLiveRenderData();
                        }
                    
                        if($data = @unserialize($rd)){
                            $external_render_data = $data;
                        }else{
                            $external_render_data = array();
                        }
                    
                        foreach($external_render_data as $key => $value){
                            $render_data[$key] = $value;
                        }
        	        
            	        foreach($params as $key => $value){
                            if($key != 'name'){
        	                    if(isset($params[$key])){
                	                $render_data[$key] = $value;
                	            }else{
                	                if(!isset($render_data[$key])){
                	                    $render_data[$key] = '';
            	                    }
                	            }
            	            }
        	            }
                    
                        $this->_renderAssetObject($asset, $params, $render_data);
                    
                    }
                    
                }
                
	        }else{
	            // some sort of error? unsupported type.
	            return $this->raiseError("Placeholder type '".$placeholder->getType()."' is unsupported");
	            
	        }
            
            if(isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol'])){
                
            }else{
            
                if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			        $edit_link = "<a title=\"Click to edit definition for placeholder: ".$placeholder->getPlaceholder()->getLabel()." (".$placeholder->getPlaceholder()->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/definePlaceholder?assetclass_id=".$placeholder->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
		        }else{
			        $edit_link = "<!--edit link-->";
		        }
		    
		    }
            
            return $html.$edit_link;
            
        }else{
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
            
                $ph = new SmartestPlaceholder;
                
                if(isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol'])){
                    
                }else{
                
                    if($ph->hydrateBy('name', $placeholder_name)){
                        $edit_link = "<a title=\"Click to edit definition for placeholder: ".$ph->getLabel()." (".$ph->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/definePlaceholder?assetclass_id=".$ph->getName()."&amp;page_id=".$this->page->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
                        return $edit_link;
                    }
                
                }
            
            }
            
        }
        
    }
    
    public function renderItemSpace($itemspace_name, $params){
        
        if($this->_context == SM_CONTEXT_CONTENT_PAGE){
            
            if($this->getPage()->hasItemSpaceDefinition($itemspace_name, $this->getDraftMode())){
            
                $def = $this->getPage()->getItemSpaceDefinition($itemspace_name, $this->getDraftMode());
                
                // Tell Smartest that this particular item appears on this page.
                // Strictly speaking, this information is already stored as the itemspace def, 
                // but we want to standardise this information so that it can be processed efficiently
                $dah = new SmartestDataAppearanceHelper;
                $dah->setItemAppearsOnPage($def->getSimpleItem($this->getDraftMode())->getId(), $this->getPage()->getId());
                
                if($def->getItemspace()->usesTemplate()){
                
                    $template_id = $def->getItemspace()->getTemplateAssetId();
                    $template = new SmartestContainerTemplateAsset;
                    
                    if($template->hydrate($template_id)){
                        $template_path = SM_ROOT_DIR.'Presentation/Layouts/'.$template->getUrl();
                        $render_process_id = SmartestStringHelper::toVarName('itemspace_template_'.SmartestStringHelper::removeDotSuffix($template->getUrl()).'_'.substr(microtime(true), -6));
            	        
            	        $child = $this->startChildProcess($render_process_id);
            	        $child->setContext(SM_CONTEXT_ITEMSPACE_TEMPLATE);
            	        $content = $child->fetch($template_path);
            	        $this->killChildProcess($child->getProcessId());
            	        return $content;
            	        
                    }else{
                        $this->raiseError("Problem rendering itemspace with template ID ".$template_id.": template not found.");
                    }
                
                }else{
                
                    // itemspace doesn't use template, but data is still loaded
                    $this->_comment("ItemSpace '".$itemspace_name."' does not use a template.");
                
                }
            
            }else{
                
                // item space is not defined
                $this->_comment("ItemSpace '".$itemspace_name."' is not defined.");
                
            }
        
        }else{
            
            $this->raiseError("ItemSpace '".$itemspace_name."' used outside page context.");
            
        }
        
    }
    
    public function renderAttachment($name){
        
        if(isset($name) && strlen($name)){
            
            if($this->_context == SM_CONTEXT_DYNAMIC_TEXTFRAGMENT){
            
                
                $attachments = $this->getProperty('attachments');
                $asset = $this->getProperty('asset');
                
                $name = SmartestStringHelper::toVarName($name);
                
                if(isset($attachments[$name])){
                    
                    $attachment = $attachments[$name];
                    
                    if($attachment['status'] == 'DEFINED'){
                        
                        if($attachment['zoom']){
                            $attachment['div_width'] = (int) $attachment['thumbnail']['width'] + 10;
                            $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/zoom_attachment.tpl';
                        }else{
                            $attachment['div_width'] = (int) $attachment['asset']['width'] + 10;
                            $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/attachment.tpl';
                        }
                        
                        if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
            			    $attachment['edit_link'] = "<a title=\"Click to edit definition for attachment: ".$name."\" href=\"".SM_CONTROLLER_DOMAIN."assets/defineAttachment?attachment=".$name."&amp;asset_id=".$asset->getId()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Attach a different file--></a>";
            		    }else{
            			    $attachment['edit_link'] = "<!--edit link-->";
            		    }
            		    
                        $this->run($file, array('_textattachment'=>$attachment));
                        
                    }else{
                        // asset tag exists, but isn't defined.
                        $this->_comment("asset tag exists, but isn't defined.");
                    }
                }else{
                    echo $this->raiseError('Attachment \''.$name.'\' does not exist.');
                }
            
            }else{
                
                echo $this->raiseError('Attachment tags can only be used in text files.');
                
            }
	        
        }
        
    }
    
    public function renderField($field_name, $params){
        
        // print_r($this->_page_rendering_data['fields']);
        
        if(array_key_exists($field_name, $this->_page_rendering_data['fields'])){
    
            $value = $this->_page_rendering_data['fields'][$field_name];
        
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			    $edit_link = "&nbsp;<a title=\"Click to edit definitions for field: ".$field_name."\" href=\"".SM_CONTROLLER_DOMAIN."metadata/defineFieldOnPage?page_id=".$this->getPage()->getWebid()."&amp;assetclass_id=".$field_name."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
		    }else{
			    $edit_link = '';
		    }

            $value .= $edit_link;
    
            return $value;
    
        }else{
        
            return $this->raiseError('Field \''.$field_name.'\' does not exist on this site.');
        
        }
        
    }
    
    public function renderEditFieldButton($field_name, $params){
        
        $markup = '<!--edit link-->';
        
        if(array_key_exists($field_name, $this->_page_rendering_data['fields'])){
        
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
		        $markup = "&nbsp;<a title=\"Click to edit definitions for field: ".$field_name."\" href=\"".SM_CONTROLLER_DOMAIN."metadata/defineFieldOnPage?page_id=".$this->getPage()->getWebid()."&amp;assetclass_id=".$field_name."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
	        }
	    
        }
        
        return $markup;
        
    }
    
    public function renderList($list_name, $params){
        
        $list = new SmartestCmsItemList;
        
        if($list->load($list_name, $this->getPage(), $this->getDraftMode())){
            /* if($list->getTemplateFilePath()){
                $this->_smarty_include(array('smarty_include_tpl_file'=>$container->getTemplateFilePath(), 'smarty_include_vars'=>array()));
            } */
            
            // 
            
            // echo 'list loaded<br />';
            
            if($list->hasRepeatingTemplate($this->getDraftMode())){
                
                // echo 'list has repeating template<br />';
                
                if($list->hasHeaderTemplate($this->getDraftMode())){
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getHeaderTemplate($this->getDraftMode()), 'smarty_include_vars'=>array()));
                    // echo $list->getHeaderTemplate($this->getDraftMode());
                    $this->run($list->getHeaderTemplate($this->getDraftMode()), array());
                }
                
                $data = $list->getItems($this->getDraftMode());
                
                foreach($data as $item){
                    $this->_tpl_vars['item'] = $item;
                    $this->assign("repeated_item", $item->__toArray());
                    $this->assign("repeated_item_object", $item);
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getRepeatingTemplate($this->getDraftMode()), 'smarty_include_vars'=>array()));
                    $this->run($list->getRepeatingTemplate($this->getDraftMode()), array());
                    // echo $list->getRepeatingTemplate($this->getDraftMode());
                }
                
                foreach($data as $item){
                    
                    if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
    				    $edit_link = "<a title=\"Click to edit ".$item['_model']['name'].": ".$item['name']."\" href=\"".SM_CONTROLLER_DOMAIN."datamanager/editItem?item_id=".$item['id']."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this item--></a>";
    			    }else{
    				    $edit_link = "<!--edit link-->";
    			    }
    			    
    			    // echo $edit_link;
                }
            
                if($list->hasFooterTemplate($this->getDraftMode())){
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getFooterTemplate($this->getDraftMode()), 'smarty_include_vars'=>array()));
                    $this->run($list->getFooterTemplate($this->getDraftMode()), array());
                    // echo $list->getFooterTemplate($this->getDraftMode());
                }
                
                if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				    $edit_link = "<a title=\"Click to edit definitions for embedded list: ".$list->getLabel()."\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineList?assetclass_id=".$list->getName()."&amp;page_id=".$this->getPage()->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this list--></a>";
			    }else{
				    $edit_link = "<!--edit link-->";
			    }
            
                echo $edit_link;
            
            }
            
        }else{
            
            // echo 'list did not load.';
            
        }
    
    }
    
    public function renderBreadcrumbs($params){
        
        if($this->_tpl_vars['this']['navigation']['_breadcrumb_trail']){

    		$breadcrumbs = $this->_tpl_vars['this']['navigation']['_breadcrumb_trail'];
    		$separator = (isset($params['separator'])) ? $params['separator'] : "&gt;";
    		$string = "";

    		$link_params = array();

    		if(isset($params['linkclass'])){
    		    $link_params['class'] = $params['linkclass'];
    		}

    		$link_params['goCold'] = 'true';
    		
    		$last_breadcrumb_index = (count($breadcrumbs) - 1);
    		
    		foreach($breadcrumbs as $key => $page){
                
                if($page->getType() == 'ITEMCLASS'){
                    
                    if($key == $last_breadcrumb_index){
                        
                        $id = $this->page->getPrincipalItem()->getId();
			            $to = 'metapage:webid='.$page->getWebid().':id='.$id;
                        
                    }else{
                    
    			        if($page->hasPrincipalItem()){
    			            $id = $page->getPrincipalItem()->getId();
    			            $to = 'metapage:webid='.$page->getWebid().':id='.$id;
    			        }else{
    			            $to = 'page:webid='.$page->getWebid();
    			        }
    			    
			        }

    			}else{
    		        $to = 'page:webid='.$page->getWebid();
    		    }
                
                if($page->getType() == 'ITEMCLASS' && !$page instanceof SmartestItemPage){
                    $text = $page->getTitle();
                }else{
    			    $text = $this->renderLink($to, $link_params);
			    }

    			if($key > 0){
    				$string .= ' '.$separator.' ';
    			}

    			$string .= $text;
    		}

    		return $string;
    	}else{
    		return $this->raiseError("Automatic breadcrumbing failed - navigation data not present.");
    	}
    }
    
    public function renderLink($to, $params){
        
        if(strlen($to)){
            
            $preview_mode = (SM_CONTROLLER_METHOD == "renderEditableDraftPage") ? true : false;
            
            $link_helper = new SmartestCmsLinkHelper($this->getPage(), $params, $this->getDraftMode(), $preview_mode);
            $l = $link_helper->parse($to);
            
            $render_data = array();
            
            $url = $link_helper->getUrl();
            $contents = $link_helper->getContent();
            
            $attributes = array();
            $allowed_attributes = array('title', 'id', 'name', 'style', 'onclick', 'ondblclick', 'onmouseover', 'onmouseout', 'class');
            
            foreach($params as $name=>$value){
                if(in_array($name, $allowed_attributes)){
                    $attributes[$name] = $value;
                }
            }
            
            if($this->getDraftMode() && ($link_helper->getType() == 'page' || $link_helper->getType() == 'metapage') && $url != '#'){
                $attributes['target'] = '_top';
            }
            
            if($this->getDraftMode() && ($link_helper->getType() == 'external')){
                $attributes['onclick'] = "return confirm('You will be taken to an external page. Continue?')";
                $attributes['target'] = '_top';
            }
            
            $attribute_string = SmartestStringHelper::toAttributeString($attributes);
            
            $render_process_id = 'dynamic_link_'.substr(md5($url), 0, 8);
            $child = $this->startChildProcess($render_process_id);
	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
	        
	        if($link_helper->shouldOmitAnchorTag()){
	            $show_anchor = false;
	        }else{
	            $show_anchor = true;
	        }
	        
	        $child->assign('_link_url', $url);
	        $child->assign('_link_contents', $contents);
	        $child->assign('_link_parameters', $attribute_string);
	        $child->assign('_link_show_anchor', $show_anchor);
            
            $html = $child->fetch(SM_ROOT_DIR."System/Presentation/WebPageBuilder/basic_link.tpl");
            $this->killChildProcess($child->getProcessId());
	        
	        return $html;
            
        }else{
            
            $this->raiseError('Link could not be built. "to" field not properly defined.');
            
        }
        
    }
    
    public function renderUrl($to, $params){
        
        // used by the tinymce url helper, as well as the {url} template helper.
        
        if(strlen($to)){
            
            $preview_mode = (SM_CONTROLLER_METHOD == "renderEditableDraftPage") ? true : false;
            
            $link_helper = new SmartestCmsLinkHelper($this->getPage(), $params, $this->getDraftMode(), $preview_mode);
            $link_helper->parse($to);
            
            return $link_helper->getUrl();
        
        }
        
    }
    
    public function loadItem($id){
        
        list($model_name, $item_id) = explode(':', $id);
        
        $item = SmartestCmsItem::retrieveByPk($item_id);
        
        return $item;
        
        /* if(isset($this->_models[$model_name])){
            if(isset($this->_models[$model_name])){
                
            }
        } */
        
        // $item = new SmartestItem;
        
        
    }
    
    public function loadItemAsArray($id){
        $item = $this->loadItem($id);
        return $item->__toArray($this->getDraftMode());
    }
    
    public function getRepeatBlockData($params){
        
        if(count(explode(':', $params['from'])) > 1){
            $parts = explode(':', $params['from']);
            $type = $parts[0];
            $name = $parts[1];
        }else{
            $type = 'set';
            $name = $params['from'];
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

        		if($set->hydrateBy('name', $name)){
        		    
        		    $dah = new SmartestDataAppearanceHelper;
                    $dah->setDataSetAppearsOnPage($set->getId(), $this->getPage()->getId());
                    
        		    $set_mode = $this->getDraftMode() ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT ;
        		    $items = $set->getMembers($set_mode, false, $limit, $query_vars);
        		    
        		}else{
        		    $items = array();
        		}

         		return $items;
        }
 		
    }
    
    public function renderAssetById($asset_id, $params, $path='none'){
        
        if(strlen($asset_id) && SmartestStringHelper::toRealBool($asset_id)){
            
            if(is_numeric($asset_id)){
                $hydrateField = 'id';
            }else{
                $hydrateField = 'stringid';
            }
            
            $asset = new SmartestAsset;
            
            if($asset->hydrateBy($hydrateField, $asset_id)){
                
                // print_r($asset);
                
                $render_data = array();
                
                if($asset->isImage()){
                    $render_data['width'] = $asset->getWidth();
                    $render_data['height'] = $asset->getHeight();
                }
                
                foreach($params as $key => $value){
                    if($key != 'name'){
	                    if(isset($params[$key])){
        	                $render_data[$key] = $value;
        	            }else{
        	                if(!isset($render_data[$key])){
        	                    $render_data[$key] = '';
    	                    }
        	            }
    	            }
	            }
	            
	            // $params['path'] = $path;
	            
	            /* if($this->getDraftMode()){
                    $rd = $placeholder->getDraftRenderData();
                }else{
                    $rd = $placeholder->getLiveRenderData();
                }
                
                if($data = @unserialize($rd)){
                    $external_render_data = $data;
                }else if($data = $placeholder->getDefaultAssetRenderData($this->getDraftMode())){
                    $external_render_data = $data;
                }else{
                    $external_render_data = array();
                }
                
                foreach($external_render_data as $key => $value){
                    $render_data[$key] = $value;
                }*/
                
                return $this->_renderAssetObject($asset, $params, $render_data, $path);
                
            }else{
                
                return $this->raiseError("No asset found with ID or Name: ".$asset_id);
                
            }

        }else{
            
            return $this->raiseError("Could not render asset. Neither of attributes 'id' and 'name' are properly defined.");
            
        }
    }
    
    public function _renderAssetObject($asset, $params, $render_data='', $path='none'){
        
        $asset_type_info = $asset->getTypeInfo();
        $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
        
        if(!is_array($render_data)){
            $render_data = array();
        }
        
        if(isset($path)){
            $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
        }else{
            $path = 'none';
        }
        
        if(file_exists($render_template)){
            
            if($asset->isImage()){
		        
		        if(!$render_data['width']){
                    $render_data['width'] = $asset->getWidth();
                }
                
                if(!$render_data['height']){
                    $render_data['height'] = $asset->getHeight();
                }
            }
            
            if(isset($params['style']) && strlen($params['style'])){
                $render_data['style'] = $params['style'];
            }
            
            if($path == 'file'){
                echo $asset->getUrl();
            }else if($path == 'full'){
                echo $asset->getFullWebPath();
            }else{
                if($asset->usesTextFragment() && $asset->isParsable()){
                    
                    $render_process_id = SmartestStringHelper::toVarName('textfragment_'.$asset->getStringid().'_'.substr(microtime(true), -6));
                    
                    $attachments = $asset->getTextFragment()->getAttachments();
                    
                    // If draft, check that a temporary preview copy has been created, and creat it if not
                    if($this->getDraftMode()){
                        if($asset->getTextFragment()->ensurePreviewFileExists()){
                            
                            $child = $this->startChildProcess($render_process_id);
                	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
                	        $child->setProperty('asset', $asset);
                	        $child->setProperty('attachments', $attachments);
                	        
                	        $content = $child->fetch($asset->getTextFragment()->getParsableFilePath(true));
                	        
                	        $this->killChildProcess($child->getProcessId());
                	        
                	        echo $content;
                        }else{
                            $this->raiseError('TextFragment render preview could not be created.');
                        }
                    }else{
                    // otherwise parse local disk copy.
                        if($asset->getTextFragment()->isPublished()){
                	        $child = $this->startChildProcess($render_process_id);
                	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
                	        $child->setProperty('asset', $asset);
                	        $child->setProperty('attachments', $attachments);
                	        
                	        $content = $child->fetch($asset->getTextFragment()->getParsableFilePath());
                	        
                	        $this->killChildProcess($child->getProcessId());
                	        
                	        echo $content;
                        }else{
                            echo $this->_comment("Asset '".$asset->getStringid()."' is not published");
                        }
                    }
                    
                }else{
                    
                    $this->run($render_template, array('asset_info'=>$asset, 'render_data'=>$render_data));
                    
                }
            }
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage" && $path == 'none'){
			    
			    if(isset($asset_type_info['editable']) && $asset_type_info['editable'] && $asset_type_info['editable'] != 'false'){
			        $edit_link .= "<a title=\"Click to edit file: ".$asset->getUrl()." (".$asset->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?asset_id=".$asset->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    }else{
			        $edit_link = "<!--edit link-->";
		        }
		    
	        }
		    
		    echo $edit_link;
            
        }else{
            echo $this->raiseError("Render template '".$render_template."' not found.");
        }
        
    }
    
    public function renderItemPropertyValue($params){
        
        if(isset($params['path'])){
            $path = (!in_array($params['path'], array('file', 'full'))) ? 'none' : $params['path'];
        }else{
            $path = 'none';
        }
        
        if(isset($params["name"]) && strlen($params["name"])){
            
            // $requested_property_name = $params["name"];
            $property_name_parts = explode(':', $params["name"]);
            $requested_property_name = $property_name_parts[0];
            array_shift($property_name_parts);
            $display_type = (isset($property_name_parts[0]) && strlen($property_name_parts[0])) ? implode(':', $property_name_parts) : null;
            $params['_display_type'] = $display_type;
            
        }else{
            return $this->raiseError("&lt;?sm:property:?&gt; tag missing required 'name' attribute.");
        }
            
            
        // for rendering the properties of the principal item of a meta-page
        if(!isset($params['context']) || $params['principal_item']){
        
            if(is_object($this->page) && $this->page instanceof SmartestItemPage){
                
                if(is_object($this->page->getPrincipalItem())){
                    
                    if(in_array($requested_property_name, $this->page->getPrincipalItem()->getPropertyVarNames())){
                    
                        $lookup = $this->page->getPrincipalItem()->getModel()->getPropertyVarNamesLookup();
                        $property = $this->page->getPrincipalItem()->getPropertyByNumericKey($lookup[$requested_property_name]);
                        $property_type_info = $property->getTypeInfo();
                        
                        $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];
                        
                        if(is_file($render_template)){
                
                            if($this->getDraftMode()){
                                $value = $property->getData()->getDraftContent();
                            }else{
                                $value = $property->getData()->getContent();
                            }
                            
                            // TODO: It's more direct to do this, though not quite so extensible. We can update this later.
                            if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                                
                                foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                    $params[$key] = $param_value;
                                }
                                
                                if(SmartestStringHelper::toRealBool($value)){
                                    return $this->_renderAssetObject($value, $params, $params, $path);
                                }else{
                                    return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$this->page->getPrincipalItem()->getId());
                                }
                                
                            }else{
                                $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                            }
                    
                        }else{
                            return $this->raiseError("Render template '".$render_template."' is missing.");
                        }
                        
                    }else{
                        return $this->raiseError("Unknown Property: ".$requested_property_name);
                    }
                }else{
                    return $this->raiseError("Page Item failed to build.");
                }
            }else{
                
                return $this->raiseError("&lt;?sm:property:?&gt; tag used on static page.");
                
            }
        
        // for rendering the properties of an item loaded using item spaces
        }else if(isset($params['context']) && ($params['context'] == 'itemspace' || $this->_context == SM_CONTEXT_ITEMSPACE_TEMPLATE)){
            
            // you have to tell it which itemspace you are referring to
            if(isset($params['itemspace']) && strlen($params['itemspace'])){
                
                // print_r($this->getPage()->getItemSpaceDefinitionNames());
                
                if($this->getPage()->hasItemSpaceDefinition($params['itemspace'], $this->getDraftMode())){
		
		            $def = $this->getPage()->getItemSpaceDefinition($params['itemspace'], $this->getDraftMode());
                    $object = $def->getItem(false, $this->getDraftMode());
                    
                    if(is_object($object)){
                        
                        if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){

                            $lookup = $object->getModel()->getPropertyVarNamesLookup();
                            $property = $object->getPropertyByNumericKey($lookup[$requested_property_name]);
                            $property_type_info = $property->getTypeInfo();
                            
                            $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];

                            if(is_file($render_template)){

                                if($this->getDraftMode()){
                                    $value = $property->getData()->getDraftContent();
                                }else{
                                    $value = $property->getData()->getContent();
                                }
			                    
			                    if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                                    
                                    foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                        $params[$key] = $param_value;
                                    }
                                    
                                    if(SmartestStringHelper::toRealBool($value)){
                                        return $this->_renderAssetObject($value, $params, $params, $path);
                                    }
                                    
                                }else{
                                    $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                                }

                            }else{
                                return $this->raiseError("Render template '".$render_template."' is missing.");
                            }
                            
                        }else if(in_array($requested_property_name, array_keys($object->__toArray(true)))){
                            
                            $array = $object->__toArray(true);
                            return $array[$requested_property_name];
                            
                        }else{
                            
                            return $this->raiseError("Unknown Property: ".$requested_property_name);
                            
                        }
                        
                    }else{
                        
                        // item space is not defined
                        return $this->raiseError("&lt;?sm:property:?&gt; tag used in itemspace context, but itemspace '".$params['itemspace']."' has no object.");
                        
                    }

                }else{

                    // item space is not defined
                    // return $this->raiseError("Itemspace '".$params['itemspace']."' not defined yet.");
                    if($this->getDraftMode()){
                        echo "Itemspace '".$params['itemspace']."' not defined yet.";
                    }

                }
                
            }else{
                return $this->raiseError("&lt;?sm:property:?&gt; tag must have itemspace=\"\" attribute when used in itemspace context.");
            }
        
        // for rendering the properties of an item in a list
        }else if(isset($params['context']) && ($params['context'] == 'other') && isset($params['item'])){
            
            $object = $params['item'];
                    
            if(is_object($object)){
                        
                if(in_array($requested_property_name, $object->getPropertyVarNames())){

                    $lookup = $object->getModel()->getPropertyVarNamesLookup();
                    $property = $object->getPropertyByNumericKey($lookup[$requested_property_name]);
                    $property_type_info = $property->getTypeInfo();
                    
                    $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];

                    if(is_file($render_template)){

                        if($this->getDraftMode()){
                            $value = $property->getData()->getDraftContent();
                        }else{
                            $value = $property->getData()->getContent();
                        }
	                    
	                    if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                            
                            foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                $params[$key] = $param_value;
                            }
                            
                            if(SmartestStringHelper::toRealBool($value)){
                                return $this->_renderAssetObject($value, $params, $params, $path);
                            }
                            
                        }else{
                            $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                        }

                    }else{
                        return $this->raiseError("Render template '".$render_template."' is missing.");
                    }
                    
                }else if(in_array($requested_property_name, array_keys($object->__toArray(true)))){
                    
                    $array = $object->__toArray(true);
                    return $array[$requested_property_name];
                    
                }else{
                    
                    return $this->raiseError("Unknown Property: ".$requested_property_name);
                    
                }
                
            }else{
                
                // $object is not an object
                if($this->getDraftMode()){
                    echo "Item is not an object.";
                    print_r($object);
                }
                
            }

        
        // for rendering the properties of an item in a list
        }else if(isset($params['context']) && ($params['context'] == 'repeat' || $params['context'] == 'list')){
            
            if(is_object($this->_tpl_vars['repeated_item_object'])){
                
                if(in_array($requested_property_name, $this->_tpl_vars['repeated_item_object']->getPropertyVarNames())){
                
                    $lookup = $this->_tpl_vars['repeated_item_object']->getModel()->getPropertyVarNamesLookup();
                    $property = $this->_tpl_vars['repeated_item_object']->getPropertyByNumericKey($lookup[$requested_property_name]);
                    $property_type_info = $property->getTypeInfo();
                
                    $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];
                    
                    if(is_file($render_template)){
                    
                        if($this->getDraftMode()){
                            $value = $property->getData()->getDraftContent();
                        }else{
                            $value = $property->getData()->getContent();
                        }
                        
                        if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                            
                            foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                $params[$key] = $param_value;
                            }
                            
                            if(SmartestStringHelper::toRealBool($value)){
                                return $this->_renderAssetObject($value, $params, $params, $path);
                            }
                            
                        }else{
                            $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                        }
                        
                    }else{
                        return $this->raiseError("Render template '".$render_template."' is missing.");
                    }
                    
                
                }else{
                    return $this->raiseError("Unknown Property: ".$requested_property_name.".");
                }
                
            }else{
                
                return $this->raiseError("Repeated item is not an object.");
                
            }
            
        }
        
    }
    
	public function renderSiteMap($params){
	    
	    $pagesTree = $this->page->getSite()->getPagesTree(true);
	    $this->_tpl_vars['site_tree'] = $pagesTree;
	    $file = SM_ROOT_DIR."Presentation/Special/sitemap.tpl";
	    $this->run($file, array());

	}
	
	public function renderGoogleAnalyticsTags($params){
	    
	    if(isset($params['id'])){
	        
	        if(isset($params['legacy']) && SmartestStringHelper::toRealBool($params['legacy']) == false){
	            $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/google_analytics_legacy.tpl';
            }else{
                $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/google_analytics.tpl';
            }
            
            $render_process_id = 'google_analytics_'.$params['id'];
	        $child = $this->startChildProcess($render_process_id);
	        $child->assign('analytics_id', $params['id']);
	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
	        $content = $child->fetch($file);
	        $this->killChildProcess($child->getProcessId());
	        return $content;
	        
	    }else{
	        return $this->raiseError("Google Analytics ID not supplied.");
	    }
	}
	
	public function renderIframe($params){
	    
	    if(isset($params['url'])){
	    
	        $allowed_iframe_attributes = array('width', 'height', 'id', 'class', 'style');
        
            $iframe_render_data = array("src"=>$params['url']);
        
            foreach($params as $name => $value){
                if(in_array($name, $allowed_iframe_attributes)){
                    $iframe_render_data[$name] = $value;
                }
            }
        
            $iframe_attributes = SmartestStringHelper::toAttributeString($iframe_render_data);
        
            $render_process_id = SmartestStringHelper::toVarName('iframe_'.$params['url'].'_'.substr(microtime(true), -6));
            $child = $this->startChildProcess($render_process_id);
            $child->assign('iframe_attributes', $iframe_attributes);
            $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
            $content = $child->fetch($file);
            $this->killChildProcess($child->getProcessId());
            return $content;
        
        }else{
            
            $this->raiseError("Could not build iframe. The required 'url' parameter was not specified.");
            
        }
        
	}
	
	public function getListData($listname){
		$result = $this->templateHelper->getList($listname);
		return $result;
	}
	
	public function getList($listname){
		
		$result = $this->getListData($listname);
		$header="ListItems/".$result['header'];
		$footer="ListItems/".$result['footer'];
		$items=$result['items'];
		$tpl_filename="ListItems/".$result['tpl_name'];
		
		if($result['header']!="" && is_file(SM_ROOT_DIR."Presentation/ListItems/".$result['header'])){
			$header = "ListItems/".$result['header'];
			$this->run($header, array());
		}
		
		if (is_array($items)){ 
		
			foreach ($items as $item){
 				$item_name=$item['item_name'];
				$properties=$item['property_details'];	
				$this->assign('name', $item_name);
				$this->assign('properties', $properties);
				$this->run($tpl_filename, array());
			}
			
		}
		
		if($result['footer']!="" && is_file(SM_ROOT_DIR."Presentation/ListItems/".$result['footer'])){
			$footer="ListItems/".$result['footer'];
			$this->run($footer, array());
		}
		
		return $result['html'];
	}
	
	public function getLink($params){
		return $this->templateHelper->getLink($params);
	}
	
	public function getImage($params){
		return $this->templateHelper->getImage($params);
	}
	
	public function getStylesheet($params){
		return $this->templateHelper->getStylesheet($params);
	}
	
	public function getImagePath($params){
		return $this->templateHelper->getImagePath($params);
	}
    
}