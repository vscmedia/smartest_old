<?php

class SmartestWebPageBuilder extends SmartestEngine{
    
    protected $templateHelper;
	protected $page;
	protected $_page_rendering_data = array();
	protected $_page_rendering_data_retrieved = false;
	protected $draft_mode = false;
	
	public function __construct($pid){
	    
	    parent::__construct($pid);
	    
	    $this->_context = SM_CONTEXT_CONTENT_PAGE;
	    
	    $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/WebPageBuilder/";
	    $this->left_delimiter = '<?sm:';
		$this->right_delimiter = ':?'.'>';
	    
	}
	
	public function getPage(){
        return $this->page;
    }
    
    public function assignPage($page){
        $this->page = $page;
    }
    
    public function setPageRenderingData($data){
        $this->_page_rendering_data = &$data;
        $this->_page_rendering_data_retrieved = true;
    }
    
    public function getDraftMode(){
        return $this->draft_mode;
    }
    
    public function setDraftMode($mode){
        $this->draft_mode = SmartestStringHelper::toRealBool($mode);
    }
    
    public function startChildProcess($pid, $type=''){
        
        if($this->_page_rendering_data_retrieved){
        
	        $cp = parent::startChildProcess($pid);
	        $cp->setDraftMode($this->getDraftMode());
	        $cp->assignPage($this->page);
	        $cp->setPageRenderingData($this->_page_rendering_data);
	        
            return $this->_child_processes[$pid];
        
        }
	}
    
    public function renderPage($page, $draft_mode=false){
	    
	    $this->page = $page;
	    $this->setDraftMode($draft_mode);
	    // $this->_page_rendering_data = $this->page->fetchRenderingData($draft_mode);
	    // $this->_page_rendering_data_retrieved = true;
	    $this->setPageRenderingData($this->page->fetchRenderingData($draft_mode));
	    $this->_tpl_vars['this'] = $this->_page_rendering_data;
	    
	    if($draft_mode){
	        $template = SM_ROOT_DIR."Presentation/Masters/".$page->getDraftTemplate();
	    }else{
	        $template = SM_ROOT_DIR."Presentation/Masters/".$page->getLiveTemplate();
	    }
	    
	    if(!file_exists($template)){
	        $template = SM_ROOT_DIR.'System/Presentation/Error/_websiteTemplateNotFound.tpl';
	    }
	    
	    // $this->_smarty_include(array('smarty_include_tpl_file'=>$template, 'smarty_include_vars'=>array()));
	    $this->run($template, array());
	}
    
    public function renderContainer($container_name, $params, $parent){
        
        // echo 'container:'.$container_name.', rendering process id:'.$this->getProcessId().', context:'.$this->_context.'<br />';
        
        if($this->_context == SM_CONTEXT_CONTENT_PAGE){
        
            $container = new SmartestContainerDefinition;
        
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
            
            }
        
        }else{
            
            if($this->getDraftMode()){
                return "<br />ERROR: Container tag can only be used in page context.";
            }
            
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
        
        // $requested_file = SM_ROOT_DIR.'Presentation/Layouts/'.$name;
	    
	    if($file_found){
	        $render_process_id = SmartestStringHelper::toVarName('template_'.SmartestStringHelper::removeDotSuffix($requested_file).'_'.substr(microtime(true), -6));
	        $child = $this->startChildProcess($render_process_id);
	        $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
	        $content = $child->fetch($template);
	        $this->killChildProcess($child->getProcessId());
	        return $content;
        }else{
            if($this->getDraftMode()){
                return '<br />ERROR: Template \''.$requested_file.'\' not found';
            }
        }
        
    }
    
    public function renderPlaceholder($placeholder_name, $params, $parent){
        
        $placeholder = new SmartestPlaceholderDefinition;
        $assetclass_types = SmartestDataUtility::getAssetClassTypes();
        
        if($asset_id = $placeholder->load($placeholder_name, $this->getPage(), $this->getDraftMode())){
            
            if(array_key_exists($placeholder->getType(), $assetclass_types)){
                
                $asset = $placeholder->getAsset($this->getDraftMode());
                
                if(is_object($asset)){
                    
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
                    
                    if($this->getDraftMode()){
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
                    }
        	        
        	        
                    
                    $this->_renderAssetObject($asset, $params, $render_data);
                    
                }
                
	        }else{
	            // some sort of error? unsupported type.
	            return "<br />ERROR: Placeholder type '".$placeholder->getType()."' is unsupported";
	        }
            
            // $html = $this->renderAsset(array('id'=>$asset_id, 'style'=>$style));
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			    $edit_link = "<a title=\"Click to edit definition for placeholder: ".$placeholder->getPlaceholder()->getLabel()." (".$placeholder->getPlaceholder()->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/definePlaceholder?assetclass_id=".$placeholder->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
		    }else{
			    $edit_link = "<!--edit link-->";
		    }
            
            return $html.$edit_link;
            
        }else{
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
            
                $ph = new SmartestPlaceholder;
            
                if($ph->hydrateBy('name', $placeholder_name)){
                    $edit_link = "<a title=\"Click to edit definition for placeholder: ".$ph->getLabel()." (".$ph->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/definePlaceholder?assetclass_id=".$ph->getName()."&amp;page_id=".$this->page->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";
                    return $edit_link;
                }
            
            }
            
        }
        
    }
    
    public function renderAttachment($name){
        
        if(isset($name) && strlen($name)){
            
            if($this->_context == SM_CONTEXT_DYNAMIC_TEXTFRAGMENT){
            
                $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/attachment.tpl';
                $attachments = $this->getProperty('attachments');
                $asset = $this->getProperty('asset');
                
                if(array_key_exists($name, $attachments)){
                    
                    $attachment = $attachments[$name];
                    
                    if($attachment['status'] == 'DEFINED'){
                        
                        $attachment['div_width'] = (int) $attachment['asset']['width'] + 10;
                        
                        if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
            			    $attachment['edit_link'] = "<a title=\"Click to edit definition for attachment: ".$name."\" href=\"".SM_CONTROLLER_DOMAIN."assets/defineAttachment?attachment=".$name."&amp;asset_id=".$asset->getId()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Attach a different file--></a>";
            		    }else{
            			    $attachment['edit_link'] = "<!--edit link-->";
            		    }
            		    
                        $this->run($file, array('_textattachment'=>$attachment));
                        
                    }else{
                        // asset tag exists, but isn't defined.
                    }
                }else{
                    if($this->getDraftMode()){
                        echo '<br />ERROR: Attachment \''.$name.'\' does not exist.';
                    }
                }
            
            }else{
                
                if($this->getDraftMode()){
                    echo '<br />ERROR: Attachment tags must be used only in text assets.';
                }
                
            }
	        
        }
        
    }
    
    public function renderField($field_name, $params){
        
        if(is_array($this->_page_rendering_data) && is_array($this->_page_rendering_data['fields'])){
            
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
                
                if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
                    return '<br />NOTICE: field \''.$field_name.'\' does not exist on this site.';
                }
                
            }
            
        }else{
            return null;
        }
        
    }
    
    public function renderEditFieldButton($field_name, $params){
        
        $markup = '<!--edit link-->';
        
        if(is_array($this->_page_rendering_data) && is_array($this->_page_rendering_data['fields'])){
        
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
            
            if($list->hasRepeatingTemplate($this->getDraftMode())){
            
                if($list->hasHeaderTemplate($this->getDraftMode())){
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getHeaderTemplate($this->getDraftMode()), 'smarty_include_vars'=>array()));
                    // echo $list->getHeaderTemplate($this->getDraftMode());
                    $this->run($list->getHeaderTemplate($this->getDraftMode()), array());
                }
            
                $data = $list->getItemsAsArrays($this->getDraftMode());
                
                foreach($data as $item){
                    // print_r($item);
                    $this->_tpl_vars['item'] = $item;
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getRepeatingTemplate($this->getDraftMode()), 'smarty_include_vars'=>array()));
                    $this->run($list->getRepeatingTemplate($this->getDraftMode(), array()));
                    // echo $list->getRepeatingTemplate($this->getDraftMode());
                }
                
                foreach($data as $item){
                    
                    if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
    				    $edit_link = "<a title=\"Click to edit ".$item['_model']['name'].": ".$item['name']."\" href=\"".SM_CONTROLLER_DOMAIN."datamanager/editItem?item_id=".$item['id']."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this item--></a>";
    			    }else{
    				    $edit_link = "<!--edit link-->";
    			    }
    			    
    			    echo $edit_link;
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
            
        }
    
    }
    
    public function renderLink($to, $params){
        
        if(strlen($to)){
            
            $preview_mode = (SM_CONTROLLER_METHOD == "renderEditableDraftPage") ? true : false;
            
            $link_helper = new SmartestCmsLinkHelper($this->getPage(), $params, $this->getDraftMode(), $preview_mode);
            $link_helper->parse($to);
            
            return $link_helper->getMarkup();
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
        		    $items = $set->getMembers($this->getDraftMode(), false, $limit, $query_vars);
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
                if($this->getDraftMode()){
                    return "<br />ERROR: No asset could with ID or Name: ".$asset_id;
                }
            }

        }else{
            if($this->getDraftMode()){
                return "<br />ERROR: Could not render asset. Neither of attributes 'id' and 'name' are properly defined.";
            }
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
            
            if($path == 'file'){
                echo $asset->getUrl();
            }else if($path == 'full'){
                echo $asset->getFullWebPath();
            }else{
                if($asset->usesTextFragment() && $asset->isParsable()){
                    
                    $render_process_id = SmartestStringHelper::toVarName('textfragment_'.$asset->getStringid().'_'.substr(microtime(true), -6));
                    
                    $attachments = $asset->getTextFragment()->getAttachmentsAsArrays();
                    
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
                            echo '<br />ERROR: TextFragment render preview could not be created.';
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
                            echo "<!--Asset '".$asset->getStringid()."' not published-->";
                        }
                    }
                    
                }else{
                    $this->run($render_template, array('asset_info'=>$asset->__toArray(), 'render_data'=>$render_data));
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
            echo "<br />ERROR: Render template '".$render_template."' not found.";
        }
        
    }
    
    public function renderItemPropertyValue($params){
        
        if(isset($params['path'])){
            $path = (!in_array($params['path'], array('file', 'full'))) ? 'none' : $params['path'];
        }else{
            $path = 'none';
        }
        
        if(isset($params["name"]) && strlen($params["name"])){
            
            $requested_property_name = $params["name"];
            
            // echo $requested_property_name;
            
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
                                    if(SmartestStringHelper::toRealBool($value)){
                                        return $this->renderAssetById($value, $params, $path);
                                    }
                                }else{
                                    $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$render_data));
                                }
                        
                            }else{
                                if($this->getDraftMode()){
                                    return "<br />ERROR: Render template '".$render_template."' is missing.";
                                }
                            }
                            
                        }else{
                            return "<br />ERROR: Unknown Property: ".$requested_property_name;
                        }
                    }else{
                        if($this->getDraftMode()){
                            return "<br />ERROR: Page Item failed to build.";
                        }
                    }
                }else{
                    if($this->getDraftMode()){
                        return "<br />NOTICE: &lt;?sm:property:&gt; tag on static page being ignored.";
                    }
                }
            
            // for rendering the properties of an item in a list
            }else if(isset($params['context']) && ($params['context'] == 'repeat' || $params['context'] == 'list')){
                
                if(is_object($this->_tpl_vars['repeated_item_object'])){
                    
                    if(in_array($requested_property_name, $this->_tpl_vars['repeated_item_object']->getModel()->getPropertyVarNames())){
                    
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
                            
                            // It's more direct to do this, though not quite so extensible. We can update this later.
                            if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                                if(SmartestStringHelper::toRealBool($value)){
                                    return $this->renderAssetById($value, $params, $path);
                                }
                            }else{
                                $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$render_data));
                            }
                            
                        }else{
                            return "<br />ERROR: Render template '".$render_template."' is missing.";
                        }
                        
                    
                    }else{
                        return "<br />ERROR: Unknown Property: ".$requested_property_name;
                    }
                }else{
                    if($this->getDraftMode()){
                        return "<br />ERROR: Repeated item is not an object.";
                    }
                }
                
            }
            
        }else{
            if($this->getDraftMode() && $this->_tpl_vars['this']['principal_item']){
                return "<br />ERROR: &lt;?sm:property:&gt; tag missing required 'name' attribute";
            }
        }
    }
    
	public function renderSiteMap(){
	    
	    $pagesTree = $this->page->getSite()->getPagesTree(true);
	    $this->_tpl_vars['site_tree'] = $pagesTree;
	    $file = SM_ROOT_DIR."Presentation/Special/sitemap.tpl";
	    $this->run($file, array());
	    // $this->_smarty_include(array('smarty_include_tpl_file'=>$file, 'smarty_include_vars'=>array()));

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
			// $this->_smarty_include(array('smarty_include_tpl_file'=>$header, 'smarty_include_vars'=>array()));
			$this->run($header, array());
		}
		
		if (is_array($items)){ 
		
			foreach ($items as $item){
 				$item_name=$item['item_name'];
				$properties=$item['property_details'];	
				$this->assign('name', $item_name);
				$this->assign('properties', $properties);
				// $this->_smarty_include(array('smarty_include_tpl_file'=>$tpl_filename, 'smarty_include_vars'=>array()));
				$this->run($tpl_filename, array());
			}
			
		}
		
		if($result['footer']!="" && is_file(SM_ROOT_DIR."Presentation/ListItems/".$result['footer'])){
			$footer="ListItems/".$result['footer'];
			// $this->_smarty_include(array('smarty_include_tpl_file'=>$footer, 'smarty_include_vars'=>array()));
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