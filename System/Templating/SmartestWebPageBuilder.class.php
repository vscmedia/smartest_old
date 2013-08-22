<?php

class SmartestWebPageBuilder extends SmartestBasicRenderer{
    
    protected $templateHelper;
	protected $page;
	protected $_page_rendering_data = array();
	protected $_page_rendering_data_retrieved = false;
	protected $_items = array();
	protected $_single_item_template_path;
	
	public function __construct($pid){
	    
	    parent::__construct($pid);
	    
	    $this->_context = SM_CONTEXT_CONTENT_PAGE;
	    
	    if(!SmartestPersistentObject::get('template_layer_data:sets')){
		    SmartestPersistentObject::set('template_layer_data:sets', new SmartestParameterHolder("Template Layer Datasets"));
		}
		
		if(!SmartestPersistentObject::get('template_layer_data:items')){
		    SmartestPersistentObject::set('template_layer_data:items', new SmartestParameterHolder("Template Layer Items"));
		}
		
		if(!defined('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN')){
		    define('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN', true);
		}
		
		if(!defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS')){
		    define('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS', true);
		}
		
		$this->caching = false;
		// Sergiy: Deny access to PHP world from frontend tpls
        // (foolproof and the case of marginally trusted template editor).
		$this->security = true;
		$this->security_settings['PHP_HANDLING'] = false;
		$this->security_settings['PHP_TAGS'] = false;
		$this->security_settings['MODIFIER_FUNCS'] = array();
		$this->security_settings['INCLUDE_ANY'] = true;

	}
	
	public function __destruct(){
	    
	    if(!$this->draft_mode){
	        
	        // echo "Destruct";
	        $p = $this->page->copy();
	        $p->setLastBuilt(time());
	        $p->save();
	        
	    }
	    
	}
	
	public function getPage(){
        return $this->page;
    }
    
    public function assignPage($page){
        $this->page = $page;
        if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $page->getSiteId());
        }
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
    
    public function getDataSetsHolder(){
        return SmartestPersistentObject::get('template_layer_data:sets');
    }
    
    public function getItemsHolder(){
        return SmartestPersistentObject::get('template_layer_data:items');
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
	
	public function prepareForRender(){
        
        $this->page->loadAssetClassDefinitions();
	    $this->page->loadItemSpaceDefinitions();
	    $this->setPageRenderingData($this->page->fetchRenderingData());
	    $this->_tpl_vars['this'] = $this->_page_rendering_data;
	    
    }
    
    public function renderPage($page, $draft_mode=false){
	    
	    $this->page = $page;
	    $this->setDraftMode($draft_mode);
	    
	    $GLOBALS['CURRENT_PAGE'] = $page;
	    
	    if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $page->getSiteId());
        }
	    
	    $this->prepareForRender();
	    
	    if($draft_mode){
	        $safe_template = "Presentation/Masters/".$page->getDraftTemplate();
	    }else{
	        $safe_template = "Presentation/Masters/".$page->getLiveTemplate();
	    }
	    
	    $template = SM_ROOT_DIR.$safe_template;
	    
	    if(!defined('SM_CMS_PAGE_ID')){
		    define('SM_CMS_PAGE_ID', $this->page->getId());
		}
	    
	    if(!is_file($template)){
	        
	        if(is_dir($template)){
                
                // no template is set at all. show "you need to create one" message.
                $this->assign('required_template', $safe_template);
	            $template = SM_ROOT_DIR.'System/Presentation/Error/_pageHasNoMasterTemplate.tpl';
                
            }else{
                
                // page refers to a non-existent template.
                $this->assign('required_template', $safe_template);
	            $template = SM_ROOT_DIR.'System/Presentation/Error/_websiteTemplateNotFound.tpl';
                
            }
	        
	        ob_start();
	        $this->run($template, array());
	        $content = ob_get_contents();
	        ob_end_clean();
	    
	        return $content;
	        
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
            
            if($this->getPage()->hasContainerDefinition($container_name)){
                
                $container_def = $this->getPage()->getContainerDefinition($container_name, $this->getDraftMode());
                
                $this->run($container_def->getTemplateFilePath(), array());
                
                if($this->_request_data->g('action') == "renderEditableDraftPage"){
		    
    			    $edit_link = '';
    			    
    			    if(constant("SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN")){
    			        
		                // print_r($container_def->getTemplate());
		                    
    	    		    if(is_object($container_def->getTemplate())){
        			        $edit_link .= "<a title=\"Click to edit template: ".$container_def->getTemplate()->getUrl()."\" href=\"".$this->_request_data->g('domain')."templates/editTemplate?template=".$container_def->getTemplate()->getId()."&amp;type=SM_ASSETTYPE_CONTAINER_TEMPLATE&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this template--></a>";
        			    }
	                    
	                    if($this->getPage() instanceOf SmartestItemPage){
    			            $edit_link .= "<a title=\"Click to edit definition for container: ".$container_name."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;item_id=".$this->getPage()->getSimpleItem()->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_red.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
			            }else{
			                $edit_link .= "<a title=\"Click to edit definition for container: ".$container_name."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_red.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
			            }

			        }
		    
    		    }else{
    			    $edit_link = "<!--edit link-->";
    		    }
    		    
    		    return $edit_link;
                
            }else{
                
                
                
            }
            
            /* $container = new SmartestContainerDefinition;
        
            if($container->load($container_name, $this->getPage(), $this->getDraftMode())){
            
                if($container->getTemplateFilePath()){
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$container->getTemplateFilePath(), 'smarty_include_vars'=>array()));
                    $this->run($container->getTemplateFilePath(), array());
                }
            
                if($this->_request_data->g('action') == "renderEditableDraftPage"){
			    
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
	        $child->caching = false;
	        $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
	        $child->assign('this', $this->_tpl_vars['this']);
	        $content = $child->fetch($template);
	        $this->killChildProcess($child->getProcessId());
	        return $content;
        }else{
            return $this->raiseError('Template \''.$requested_file.'\' not found');
        }
        
    }
    
    public function renderPlaceholder($placeholder_name, $params, $parent){
        
        $assetclass_types = SmartestDataUtility::getAssetClassTypes();
        
        $display = (isset($params['display']) && in_array($params['display'], array('file', 'filename', 'full', 'path', 'normal', 'download', 'size'))) ? $params['display'] : 'normal';
        
        if($this->getPage()->hasPlaceholderDefinition($placeholder_name, $this->getDraftMode())){
            
            $placeholder = $this->getPage()->getPlaceholderDefinition($placeholder_name, $this->getDraftMode());
            
            if(array_key_exists($placeholder->getType(), $assetclass_types)){
                
                $asset = $placeholder->getAsset($this->getDraftMode());
                
                if(is_object($asset)){
                    
                    $type_info = $asset->getTypeInfo();
                    
                    if($display == 'file' || $display == 'filename'){
                        
                        return $asset->getUrl();
                        
                    }else if($display == 'full' || $display == 'path'){
                        
                        if($asset->usesLocalFile()){
                            
                            return $asset->getFullWebPath();
                            
                        }else{
                            
                            return $this->raiseError('display="'.$display.'" used on asset type that does not have a local file: '.$asset->getType());
                            
                        }
                        
                    }else if($display == 'download'){
                        
                        return $asset->getAbsoluteDownloadUri();
                    
                    }else if($display == 'size'){
                        
                        return $asset->getSize();
                    
                    }else{
                        
                        $render_data = array();
                        
                        if(isset($params['transform'])){
                            $transform_param_values = SmartestStringHelper::parseNameValueString($params['transform']);
                            // TODO: Allow inline transformations on certain asset types - resize, (rotate?)
                        }
                    
                        if($this->getDraftMode()){
                            $rd = $placeholder->getDraftRenderData();
                        }else{
                            $rd = $placeholder->getLiveRenderData();
                        }
                        
                        if($data = unserialize($rd)){
                            $external_render_data = $data;
                            $asset->setAdditionalRenderData($data, true);
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
        	            
        	            $asset->setAdditionalRenderData($render_data, true);
        	            
        	            $html = $asset->render($this->getDraftMode());
        	            
        	            // This code makes sure that if internal link codes are input as values on an image placeholder, the link will be built
        	            if($asset->isImage() && isset($render_data['link_href']) && strlen($render_data['link_href'])){
        	                
        	                $link_properties = SmartestLinkParser::parseSingle($render_data['link_href']);
        	                $link = new SmartestCmsLink($link_properties, array());
        	                $image = $asset->getImage();
        	                $image->setAdditionalRenderData($render_data);
        	                $link->setImageAsContent($image);
        	                
        	                if($GLOBALS['CURRENT_PAGE']){
                    		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
                    		}
        	                
        	                $html = $link->render($this->getDraftMode());
        	                
        	            }
                    
                    }
                    
                }
                
	        }else{
	            // some sort of error? unsupported type.
	            return $this->raiseError("Placeholder type '".$placeholder->getType()."' is unsupported");
	            
	        }
            
            if(isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol'])){
                
            }else{
                
                $show_edit_link = isset($params['showeditbutton']) ? (SmartestStringHelper::toRealBool($params['showeditbutton']) && $this->_request_data->g('action') == "renderEditableDraftPage") : ($placeholder->getPlaceholder()->isEditableFromPreview() && $this->_request_data->g('action') == "renderEditableDraftPage");
                
                if($show_edit_link){
                
                    if($this->_request_data->g('action') == "renderEditableDraftPage"){
                        if($this->getPage() instanceOf SmartestItemPage){
                            $edit_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$placeholder->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid().'&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
                            $delete_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$placeholder->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid().'&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
                        }else{
                            $edit_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$placeholder->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid();
                        }
    			        $edit_link = "<a title=\"Click to edit definition for placeholder: ".$placeholder->getPlaceholder()->getLabel()." (".$placeholder->getPlaceholder()->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
    			        // $edit_link .= "<a title=\"Click to clear definition for placeholder: ".$placeholder->getPlaceholder()->getLabel()." (".$placeholder->getPlaceholder()->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
    		        }else{
    			        $edit_link = "<!--edit link-->";
    		        }
		        
	            }
		    
		    }
            
            return $html.$edit_link;
            
        }else{
            
            if($this->_request_data->g('action') == "renderEditableDraftPage"){
            
                $ph = new SmartestPlaceholder;
                
                if(isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol'])){
                    
                }else{
                    
                    if($ph->findBy('name', $placeholder_name)){
                        
                        $show_edit_link = isset($params['showeditbutton']) ? (SmartestStringHelper::toRealBool($params['showeditbutton']) && $this->_request_data->g('action') == "renderEditableDraftPage") : ($ph->isEditableFromPreview() && $this->_request_data->g('action') == "renderEditableDraftPage");
                        
                        if($show_edit_link){
                        
                            if($this->getPage() instanceOf SmartestItemPage){
                                $edit_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$ph->getName()."&amp;page_id=".$this->page->getWebid().'&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
                            }else{
                                $edit_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$ph->getName()."&amp;page_id=".$this->page->getWebid();
                            }
                        
                            $edit_link = "<a title=\"Click to edit definition for placeholder: ".$ph->getLabel()." (".$ph->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
                            return $edit_link;
                        
                        }
                        
                    }else{
                        
                        $edit_link = "<a title=\"Placeholder ".$placeholder_name." does not exist. Click to create.\" href=\"".$this->_request_data->g('domain')."websitemanager/addPlaceholder?placeholder_name=".$placeholder_name."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/error.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
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
                
                $item_name = isset($params['item_name']) ? $params['item_name'] : 'item';
                
                // Tell Smartest that this particular item appears on this page.
                // Strictly speaking, this information is already stored as the itemspace def, 
                // but we want to standardise this information so that it can be processed efficiently
                $dah = new SmartestDataAppearanceHelper;
                $dah->setItemAppearsOnPage($def->getSimpleItem($this->getDraftMode())->getId(), $this->getPage()->getId());
                
                if($def->getItemspace()->usesTemplate()){
                
                    $template_id = $def->getItemspace()->getTemplateAssetId();
                    $template = new SmartestTemplateAsset;
                    
                    if($template->find($template_id)){
                        
                        $template_path = SM_ROOT_DIR.'Presentation/Layouts/'.$template->getUrl();
                        $render_process_id = SmartestStringHelper::toVarName('itemspace_template_'.SmartestStringHelper::removeDotSuffix($template->getUrl()).'_'.substr(microtime(true), -6));
            	        
            	        $child = $this->startChildProcess($render_process_id);
            	        $child->setContext(SM_CONTEXT_ITEMSPACE_TEMPLATE);
            	        $item = $def->getItem(false, $this->getDraftMode());
            	        $item->setDraftMode($this->getDraftMode());
            	        $child->assign($item_name, $item); // set above
            	        $content = '<!--rendering itemspace: '.$itemspace_name."-->\n\n";
            	        $content .= $child->fetch($template_path);
            	        $content .= $this->renderItemEditButton($item->getId());
            	        $content .= $this->renderItemSpaceDefineButton($itemspace_name);
            	        $this->killChildProcess($child->getProcessId());
            	        
            	        return $content;
            	        
                    }else{
                        return $this->raiseError("Problem rendering itemspace with template ID ".$template_id.": template not found.");
                    }
                
                }else{
                
                    // itemspace doesn't use template, but data is still loaded
                    $this->_comment("ItemSpace '".$itemspace_name."' does not use a template.");
                    $item = $def->getItem(false, $this->getDraftMode());
        	        $item->setDraftMode($this->getDraftMode());
        	        $this->assign($itemspace_name.'_item', $item);
        	        return $this->renderItemEditButton($item->getId()).$this->renderItemSpaceDefineButton($itemspace_name);
                
                }
            
            }else{
                
                // item space is not defined
                $this->_comment("ItemSpace '".$itemspace_name."' is not defined.");
                
            }
        
        }else{
            
            return $this->raiseError("ItemSpace '".$itemspace_name."' used outside page context.");
            
        }
        
    }
    
    public function renderItemEditButton($item_id){
        
        if($this->getDraftMode()){
            $url = $this->_request_data->g('domain').'datamanager/openItem?item_id='.$item_id;
            if($this->page){
                $url .= '&amp;from=pagePreview&amp;page_webid='.$this->page->getWebid();
            }else if($this->_request_data->g('request_parameters')->g('page_id')){
                $url .= '&amp;from=pagePreview&amp;page_webid='.$this->_request_data->g('request_parameters')->g('page_id');
            }
            // print_r($this->_request_data);
            $html = '<a href="'.$url.'" target="_top" title="Edit item ID '.$item_id.'"><img src="'.$this->_request_data->g('domain').'Resources/Icons/package_small.png" alt="Edit item ID '.$item_id.'" /></a>';
        }else{
            $html = '';
        }
        
        return $html;
        
    }
    
    public function renderItemSpaceDefineButton($itemspace_name){
        
        if($this->getDraftMode()){
            
            $url = $this->_request_data->g('domain').'websitemanager/defineItemspace?assetclass_id='.$itemspace_name;
            
            if($this->page){
                $url .= '&amp;from=pagePreview&amp;page_id='.$this->page->getWebid();
            }else if($this->_request_data->g('request_parameters')->g('page_id')){
                $url .= '&amp;from=pagePreview&amp;page_id='.$this->_request_data->g('request_parameters')->g('page_id');
            }
            
            $html = '<a href="'.$url.'" target="_top" title="Edit itemspace '.$itemspace_name.'"><img src="'.$this->_request_data->g('domain').'Resources/Icons/arrow_refresh_blue.png" alt="Edit itemspace'.$itemspace_name.'" /></a>';
        }else{
            $html = '';
        }
        
        return $html;
        
    }
    
    public function renderField($field_name, $params){
        
        // if($this->_page_rendering_data['fields']->hasParameter($field_name)){
    
            $value = $this->_page_rendering_data['fields'][$field_name];
        
            if($this->getDraftMode()){
			    $edit_link = $this->renderEditFieldButton($field_name, $params);
		    }else{
			    $edit_link = '';
		    }

            $value .= $edit_link;
    
            return $value;
    
        /* }else{
        
            return $this->raiseError('Field \''.$field_name.'\' does not exist on this site.');
        
        } */
        
    }
    
    public function renderEditFieldButton($field_name, $params){
        
        if($this->_page_rendering_data['fields'] instanceof SmartestParameterHolder){
        
            $markup = '<!--edit link-->';
        
            if($this->_page_rendering_data['fields']->hasParameter($field_name)){
        
                if($this->_request_data->g('action') == "renderEditableDraftPage"){
    		        $markup = "&nbsp;<a title=\"Click to edit definitions for field: ".$field_name."\" href=\"".$this->_request_data->g('domain')."metadata/defineFieldOnPage?page_id=".$this->getPage()->getWebid()."&amp;assetclass_id=".$field_name."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
    	        }
	    
            }
        
            return $markup;
        
        }
        
    }
    
    public function renderList($list_name, $params){
        
        $list = new SmartestCmsItemList;
        
        if($list->load($list_name, $this->getPage(), $this->getDraftMode())){
            
            if($list->hasRepeatingTemplate($this->getDraftMode())){
                
                $limit = $list->getMaximumLength() > 0 ? $list->getMaximumLength() : null;
                $data = $list->getItems($this->getDraftMode(), $limit);
                // var_dump($limit);
                
                if($list->getType() == 'SM_LIST_ARTICULATED'){
                
                    if($list->hasHeaderTemplate($this->getDraftMode())){
                        $this->run($list->getHeaderTemplate($this->getDraftMode()), array());
                    }
                
                    foreach($data as $item){
                        
                        if($this->_request_data->g('action') == "renderEditableDraftPage"){
        				    $edit_link = "<a title=\"Click to edit ".$item['_model']['name'].": ".$item['name']."\" href=\"".$this->_request_data->g('domain')."datamanager/editItem?item_id=".$item['id']."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this item--></a>";
        			    }else{
        				    $edit_link = "<!--edit link-->";
        			    }
                        
                        $child = $this->startChildProcess(substr(md5($list->getName().$item->getId().microtime()), 0, 8));
            	        $child->assign('repeated_item', $item);
            	        $child->assign('repeated_item_object', $item); // legacy support
            	        $child->assign('edit_link', $edit_link);
            	        $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
            	        $content .= $child->fetch($list->getRepeatingTemplate($this->getDraftMode()));
            	        
            	        $this->killChildProcess($child->getProcessId());
        			    
                    }
                
                    if($list->hasFooterTemplate($this->getDraftMode())){
                        $this->run($list->getFooterTemplate($this->getDraftMode()), array());
                    }
                
                }else{
                    
                    if(isset($params['assign']) && strlen($params['assign'])){
                        $this->assign(SmartestStringHelper::toVarName($params['assign']), $data);
                    }else{
                        $child = $this->startChildProcess(substr(md5($list->getName().microtime()), 0, 8));
        	            $child->assign('items', $data);
        	            $child->assign('num_items', count($data));
        	            $child->assign('title', $list->getTitle());
        	            $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
        	            $child->setDraftMode($this->getDraftMode());
        	            $child->caching = false;
        	            $content = $child->fetch($list->getRepeatingTemplate($this->getDraftMode()));
        	            $this->killChildProcess($child->getProcessId());
    	            }
                    
                }
            
            } // end if: the list has repeating template
            
            // var_dump($list->hasRepeatingTemplate($this->getDraftMode()));
            
            // echo 'hello';
            
            if($this->_request_data->g('action') == "renderEditableDraftPage"){
			    $edit_link = "<a title=\"Click to edit definitions for embedded list: ".$list->getName()."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineList?assetclass_id=".$list->getName()."&amp;page_id=".$this->getPage()->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_blue.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this list--></a>";
		    }else{
			    $edit_link = "<!--edit link-->";
		    }
        
            $content .= $edit_link;
            
            return $content;
            
        }else{
            
            // echo 'list did not load.';
            
        }
    
    }
    
    public function renderBreadcrumbs($params){
        
        if($this->_tpl_vars['this']['navigation']['_breadcrumb_trail']){

    		$breadcrumbs = $this->_tpl_vars['this']['navigation']['_breadcrumb_trail'];
    		$separator = (isset($params['separator'])) ? $params['separator'] : "&gt;";
    		$string = "";
    		
    		// print_r($breadcrumbs);

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
                
                /* if($page->getType() == 'ITEMCLASS' && !$page instanceof SmartestItemPage){
                    $text = $page->getTitle();
                }else{
    			    $text = $this->renderLink($to, $link_params);
			    } */
			    
			    $ph = new SmartestParameterHolder("Link Attributes: [".$to."]");
			    $ph->loadArray($link_params);
			    
			    $link = SmartestCmsLinkHelper::createLink($to, $ph);
			    $link->setHostPage($this->getPage());
			    $text = $link->render($this->getDraftMode());

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
    
    public function renderUrl($to, $params){
        
        // used by the tinymce url helper, as well as the {url} template helper.
        
        if(strlen($to)){
            
            $preview_mode = ($this->_request_data->g('action') == "renderEditableDraftPage") ? true : false;
            
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
        
        $this->caching = false;
        $this->_repeat_char_length_aggr = 0;
        
        if(is_array($params['from']) || $params['from'] instanceof SmartestArray){
            return $params['from'];
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
                    if(isset($params['skip_memberships']) && SmartestStringHelper::toRealBool($params['skip_memberships'])){
                        return $g->getMembers();
                    }else{
                        return $g->getMemberships();
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
                
                if($set->findBy('name', $name, $this->page->getSiteId()) || $this->getDataSetsHolder()->h($name)){
        		    
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
    
    public function getDataSetItemsByName($name){
        
        $set = new SmartestCmsItemSet;
        
        if($set->findBy('name', $name, $this->page->getSiteId()) || $this->getDataSetsHolder()->h($name)){
		    
		    $set_mode = $this->getDraftMode() ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT ;
		    $items = $set->getMembersPaged($set_mode, null, 0, $query_vars, $this->page->getSiteId());
		    return $items;
		    
		}else{
		    
		    $this->raiseError("Data set with name '".$name."' could not be found.");
		    return array();
		    
		}
        
    }
    
    public function renderAssetById($asset_id, $params, $path='none'){
        
        if(strlen($asset_id) && SmartestStringHelper::toRealBool($asset_id)){
            
            if(is_numeric($asset_id)){
                $hydrateField = 'id';
            }else{
                $hydrateField = 'stringid';
            }
            
            $asset = new SmartestRenderableAsset;
            
            if($asset->hydrateBy($hydrateField, $asset_id)){
                
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
	            
	            $asset->setAdditionalRenderData($render_data, true);
                return $asset->render($this->getDraftMode());
                
            }else{
                
                return $this->raiseError("No asset found with ID or Name: ".$asset_id);
                
            }

        }else{
            
            return $this->raiseError("Could not render asset. Neither of attributes 'id' and 'name' are properly defined.");
            
        }
    }
    
    /* public function _renderAssetObject($asset, $params, $render_data='', $path='none'){
        
        $sm = new SmartyManager('AssetRenderer');
        $r = $sm->initialize($asset->getStringId());
        $r->assignAsset($asset);
        $r->setDraftMode($this->getDraftMode());
        // $content = $r->render($params, $render_data, $path);
        return $r->render($params, $render_data, $path);
        
    } */
    
    public function _renderAssetObject($asset, $markup_params, $render_data='', $path='none'){
        
        $asset_type_info = $asset->getTypeInfo();
        $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
        
        if(!is_array($render_data) && !$render_data instanceof SmartestParameterHolder){
            $render_data = array();
        }
        
        if(isset($path)){
            $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
        }else{
            $path = 'none';
        }
        
        if(file_exists($render_template)){
            
            $asset->setAdditionalRenderData($render_template);
            $content = $asset->render($this->getDraftMode());
            
        }else{
            $content = $this->raiseError("Render template '".$render_template."' not found.");
        }
        
        return $content;
        
    }
    
    public function renderItemPropertyValue($params){
        
        if(isset($params['path'])){
            $path = (!in_array($params['path'], array('file', 'full'))) ? 'none' : $params['path'];
        }else{
            $path = 'none';
        }
        
        if(isset($params["name"]) && strlen($params["name"])){
            
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
                    
                    if(in_array($requested_property_name, $this->page->getPrincipalItem()->getModel()->getPropertyVarNames())){
                    
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
                                
                                if(is_object($value)){
                                    // return $this->_renderAssetObject($value, $params, $params, $path);
                                    $value->setAdditionalRenderData($params);
                                    return $value->render($this->getDraftMode());
                                }else{
                                    return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$this->page->getPrincipalItem()->getId());
                                }
                                
                            }else{
                                $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                            }
                    
                        }else{
                            return $this->raiseError("Render template '".$render_template."' is missing.");
                        }
                        
                    }else if($requested_property_name == "name"){
                        return new SmartestString($this->page->getPrincipalItem()->getName());
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
                                    
                                    if(is_object($value)){
                                        // return $this->_renderAssetObject($value, $params, $params, $path);
                                        $value->setAdditionalRenderData($params);
                                        $value->render($this->getDraftMode());
                                    }else{
                                        return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$object->getId());
                                    }
                                    
                                    
                                }else{
                                    $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                                }

                            }else{
                                return $this->raiseError("Render template '".$render_template."' is missing.");
                            }
                            
                        }else if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){
                            
                            // $array = $object->__toArray(true);
                            // return $array[$requested_property_name];
                            return $object->getPropertyValueByVarName($requested_property_name);
                            
                        }else if($requested_property_name == "name"){
                            return new SmartestString($object->getName());
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
                            
                            if(is_object($value)){
                                $value->setAdditionalRenderData($params);
                                return $value->render($this->getDraftMode());
                            }else{
                                return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$object->getId());
                            }
                            
                        }else{
                            $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                        }

                    }else{
                        return $this->raiseError("Render template '".$render_template."' is missing.");
                    }
                    
                }else if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){
                    
                    // $array = $object->__toArray(true);
                    // return $array[$requested_property_name];
                    return $object->getPropertyValueByVarName($requested_property_name);
                    
                }else if($requested_property_name == "name"){
                    return new SmartestString($object->getName());
                }else{
                    
                    return $this->raiseError("Unknown Property: ".$requested_property_name);
                    
                }
                
            }else{
                
                // $object is not an object
                // if($this->getDraftMode()){
                    return $this->raiseError("Item is not an object");
                // }
                
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
                            
                            if(is_object($value)){
                                $value->setAdditionalRenderData($params);
                                return $value->render($this->getDraftMode());
                            }else{
                                return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$object->getId());
                            }
                            
                        }else{
                            $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                        }
                        
                    }else{
                        return $this->raiseError("Render template '".$render_template."' is missing.");
                    }
                    
                
                }else if($requested_property_name == "name"){
                    return new SmartestString($this->_tpl_vars['repeated_item_object']->getName());
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
	    
	    if($this->getDraftMode()){
	        
	        if(isset($params['id'])){
	            return '<!--On a live page, Google Analytics will be placed here ('.$params['id'].')-->';
            }
	        
	    }else{
	    
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
            
            return $this->raiseError("Could not build iframe. The required 'url' parameter was not specified.");
            
        }
        
	}
	
	public function assignSingleItemTemplate($tpl_path){
        $this->_single_item_template_path = $tpl_path;
    }
    
    public function renderSingleItemTemplate(){
        
        ob_start();
        $this->run($this->_single_item_template_path, array());
        $content = ob_get_contents();
        ob_end_clean();
        
        if($this->_request_data->g('action') == "renderEditableDraftPage" && $path == 'none' && $show_preview_edit_link){
		    
		    if(isset($asset_type_info['editable']) && SmartestStringHelper::toRealBool($asset_type_info['editable'])){
		        $edit_link .= "<a title=\"Click to edit file: ".$this->_asset->getUrl()." (".$this->_asset->getType().")\" href=\"".$this->_request_data->g('domain')."assets/editAsset?asset_id=".$this->_asset->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
		    }else{
		        $edit_link = "<!--edit link-->";
	        }
	    
        }
	    
	    $content .= $edit_link;
	    
	    return $content;
        
    }
	
	/* public function getListData($listname){
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
	} */
	
	/* public function getLink($params){
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
	} */
    
}