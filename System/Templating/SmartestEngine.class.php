<?php

require('Smarty.class.php');

class SmartestEngine extends Smarty{

	protected $controller;
	protected $section;
	protected $method;
	protected $domain;
	protected $get;
	protected $templateHelper;
	protected $page;
	protected $_page_rendering_data = array();
	protected $draft_mode = false;
	
	public function __construct(){
	
		parent::Smarty();
		// global $site;
		
		$this->controller = SmartestPersistentObject::get('controller');
		$this->section = $this->controller->getModuleName();
		$this->method  = $this->controller->getMethodName();
		$this->domain  = $this->controller->getDomain();
		$this->get     = $this->controller->getRequestVariables();
		
		$this->templateHelper = new SmartestTemplateHelper;
		$this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/";
		$this->compiler_file = SM_ROOT_DIR."System/Templating/SmartestEngineCompiler.class.php";
        $this->compiler_class = "SmartestEngineCompiler";
    	
    	// it would be nice to get away from curly brackets, but probably too late now:
    	// $this->left_delimiter = '<%';
		// $this->right_delimiter = '%>';
		
	}
	
	public function getPage(){
        return $this->page;
    }
    
    public function getDraftMode(){
        return $this->draft_mode;
    }
	
    public function renderPage($page, $draft_mode=false){
	    
	    $this->page = $page;
	    $this->draft_mode = $draft_mode;
	    $this->_page_rendering_data = $this->page->fetchRenderingData($draft_mode);
	    $this->_tpl_vars['this'] = $this->_page_rendering_data;
	    
	    // print_r($page);
	    
	    if($draft_mode){
	        $template = SM_ROOT_DIR."Presentation/Masters/".$page->getDraftTemplate();
	    }else{
	        $template = SM_ROOT_DIR."Presentation/Masters/".$page->getLiveTemplate();
	    }
	    
	    if(!file_exists($template)){
	        $template = SM_ROOT_DIR.'System/Presentation/Error/_websiteTemplateNotFound.tpl';
	    }
	    
	    $this->_smarty_include(array('smarty_include_tpl_file'=>$template, 'smarty_include_vars'=>array()));
	}
    
    public function renderContainer($container_name, $params, $parent){
        
        $container = new SmartestContainerDefinition;
        
        if($container->load($container_name, $this->getPage(), $this->draft_mode)){
            // var_dump($container->getTemplateFilePath());
            
            if($container->getTemplateFilePath()){
                $this->_smarty_include(array('smarty_include_tpl_file'=>$container->getTemplateFilePath(), 'smarty_include_vars'=>array()));
            }
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			    
			    $edit_link = '';
			    
			    if(is_object($container->getTemplate())){
			        // $edit_link .= "<a title=\"Click to edit template: ".$container->getTemplate()->getUrl()."\" href=\"".SM_CONTROLLER_DOMAIN."templates/editTemplate?template_id=".$container->getTemplate()->getId()."&amp;type=SM_CONTAINER_TEMPLATE&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this template--></a>";
			    }
			    
			    // $edit_link .= "<a title=\"Click to edit definition for container: ".$container_name."\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    
		    }else{
			    // $edit_link = "<!--edit link-->";
		    }
		    
		    return $edit_link;
            
        }
        
    }
    
    public function renderPlaceholder($placeholder_name, $params, $parent){
        
        $placeholder = new SmartestPlaceholderDefinition;
        // $asset_types = SmartestDataUtility::getAssetTypes();
        $assetclass_types = SmartestDataUtility::getAssetClassTypes();
        // print_r($asset_types);
        
        if($asset_id = $placeholder->load($placeholder_name, $this->getPage(), $this->draft_mode)){
            // return $placeholder->getMarkup();
            
            if(array_key_exists($placeholder->getType(), $assetclass_types)){
                
                if(is_object($placeholder->getAsset())){
	                // $type_info = $placeholder->getAsset($this->draft_mode)->getTypeInfo();
                }
                
	            // print_r($placeholder->getAsset($this->draft_mode));
	        }else{
	            // some sort of error? unsupported type.
	            return "Error: unsupported type";
	        }
	        
	        if(isset($params['style'])){
	            $style = $params['style'];
	        }else{
	            $style = '';
	        }
	        
	        // print_r($type_info['editable']);
            
            if($this->draft_mode){
                $rd = $placeholder->getDraftRenderData();
            }else{
                $rd = $placeholder->getLiveRenderData();
            }
            
            if($data = @unserialize($rd)){
                $render_data = $data;
            }else if($data = $placeholder->getDefaultAssetRenderData($this->draft_mode)){
                $render_data = $data;
            }else{
                $render_data = array();
            }
            
            $html = $this->renderAsset(array('id'=>$asset_id, 'render_data'=>$render_data, 'style'=>$style));
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			    $edit_link = "<a title=\"Click to edit definition for placeholder: ".$placeholder->getPlaceholder()->getLabel()." (".$placeholder->getPlaceholder()->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/definePlaceholder?assetclass_id=".$placeholder->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this file--></a>";

			    /* if(isset($type_info['editable']) && $type_info['editable'] && $type_info['editable'] != 'false'){
			        $edit_link .= "<a title=\"Click to edit placeholder: ".$placeholder->getPlaceholder()->getLabel()." (".$placeholder->getPlaceholder()->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?asset_id=".$asset_id."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    } */

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
    
    public function renderField($field_name, $params){
        
        // print_r($this->_page_rendering_data['fields']);
        
        if(is_array($this->_page_rendering_data) && is_array($this->_page_rendering_data['fields'])){
            
            $value = $this->_page_rendering_data['fields'][$field_name];
            
            // echo constant('SM_CONTROLLER_METHOD');
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			    $edit_link = "&nbsp;<a title=\"Click to edit definitions for field: ".$field_name."\" href=\"".SM_CONTROLLER_DOMAIN."metadata/defineFieldOnPage?page_id=".$this->getPage()->getWebid()."&amp;assetclass_id=".$field_name."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
		    }else{
			    $edit_link = '';
		    }
        
            $value .= $edit_link;
            
            return $value;
            
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
        
        if($list->load($list_name, $this->getPage(), $this->draft_mode)){
            /* if($list->getTemplateFilePath()){
                $this->_smarty_include(array('smarty_include_tpl_file'=>$container->getTemplateFilePath(), 'smarty_include_vars'=>array()));
            } */
            
            if($list->hasRepeatingTemplate($this->draft_mode)){
            
                if($list->hasHeaderTemplate($this->draft_mode)){
                    $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getHeaderTemplate($this->draft_mode), 'smarty_include_vars'=>array()));
                    // echo $list->getHeaderTemplate($this->draft_mode);
                }
            
                $data = $list->getItemsAsArrays($this->draft_mode);
                
                foreach($data as $item){
                    // print_r($item);
                    $this->_tpl_vars['item'] = $item;
                    $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getRepeatingTemplate($this->draft_mode), 'smarty_include_vars'=>array()));
                    // echo $list->getRepeatingTemplate($this->draft_mode);
                }
                
                foreach($data as $item){
                    
                    if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
    				    $edit_link = "<a title=\"Click to edit ".$item['_model']['name'].": ".$item['name']."\" href=\"".SM_CONTROLLER_DOMAIN."datamanager/editItem?item_id=".$item['id']."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this item--></a>";
    			    }else{
    				    $edit_link = "<!--edit link-->";
    			    }
    			    
    			    echo $edit_link;
                }
            
                if($list->hasFooterTemplate($this->draft_mode)){
                    $this->_smarty_include(array('smarty_include_tpl_file'=>$list->getFooterTemplate($this->draft_mode), 'smarty_include_vars'=>array()));
                    // echo $list->getFooterTemplate($this->draft_mode);
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
            
            $link_helper = new SmartestCmsLinkHelper($this->getPage(), $params, $this->draft_mode, $preview_mode);
            $link_helper->parse($to);
            
            return $link_helper->getMarkup();
        }
        
    }
    
    public function renderUrl($to, $params){
        
        // used by the tinymce url helper, as well as the {url} template helper.
        
        if(strlen($to)){
            
            $preview_mode = (SM_CONTROLLER_METHOD == "renderEditableDraftPage") ? true : false;
            
            $link_helper = new SmartestCmsLinkHelper($this->getPage(), $params, $this->draft_mode, $preview_mode);
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
                
                $set = new SmartestCmsItemSet;
                
                if(isset($params['limit']) && is_numeric($params['limit'])){
                    $limit = $params['limit'];
                }else{
                    $limit = null;
                }

        		if($set->hydrateBy('name', $name)){
        		    $items = $set->getMembers($this->draft_mode, false, $limit);
        		    // $items = array();
        		}else{
        		    $items = array();
        		}

         		return $items;
         		
                break;
        }
        
		// $items = $this->templateHelper->getItemDetails($set_name);
		
		
 		
    }
    
    public function renderAsset($params){
       
        // print_r($params);
        
        // echo 'renderAsset';
       
        if((isset($params['id']) && is_numeric($params['id'])) || (isset($params['name']) && strlen($params['name']))){
            // retrieve asset by primary key
            // return $params['id'];

            $asset = new SmartestAsset;
            
            if($asset->hydrate($params['id']) || $asset->hydrateBy('stringid', $params['name'])){
                
                $asset_type_info = $asset->getTypeInfo();
                $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
                
                if(file_exists($render_template)){
                    
                    if(isset($params['style'])){
    			        @$params['render_data']['style'] .= $params['style'];
    			    }
                    
                    $this->_smarty_include(array('smarty_include_tpl_file'=>$render_template, 'smarty_include_vars'=>array('asset_info'=>$asset->__toArray(), 'render_data'=>@$params['render_data'])));
                    
                    if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
        			    
        			    // echo 'asset';
        			    // echo $asset_type_info['editable'];
        			    
        			    
        			    
        			    if(isset($asset_type_info['editable']) && $asset_type_info['editable'] && $asset_type_info['editable'] != 'false'){
        			        $edit_link .= "<a title=\"Click to edit file: ".$asset->getUrl()." (".$asset->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?asset_id=".$asset->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
        			    }else{
        			        $edit_link = "<!--edit link-->";
        		        }
        		    
    		        }
        		    
        		    echo $edit_link;
                    
                }else{
                    return "<br />Error: ".$render_template." does not exist";
                }
                
            }else{
                if($this->getDraftMode()){
                    return "<br />Error: No asset was found with ID: ".$params['id'];
                }
            }

        }else{
            if($this->getDraftMode()){
                return "<br />Error on {property} tag: either attributes 'id' or 'name' are properly defined.";
            }
        }
        
        
    }
    
    public function renderItemPropertyValue($params){
        
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
                            
                            // echo $render_template;
                        
                            if(is_file($render_template)){
                            
                                if($this->draft_mode){
                                    $value = $property->getData()->getDraftContent();
                                }else{
                                    $value = $property->getData()->getContent();
                                }
                                
                                // print_r($property->getData());
                                
                                $render_data = array();
                                
                                //this is a hack for image attributes
                                if($params['style']){
                                    $render_data['style'] = $params['style'];
                                }
                                
                                if($params['id']){
                                    $render_data['id'] = $params['id'];
                                }
                                
                                if($params['class']){
                                    $render_data['class'] = $params['class'];
                                }
                                
                                // It's more direct to do this, though not quite so extensible. We can update this later.
                                if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                                    $this->renderAsset(array('id'=>$value, 'render_data'=>$render_data));
                                }else{
                                    $this->_smarty_include(array('smarty_include_tpl_file'=>$render_template, 'smarty_include_vars'=>array('raw_value'=>$value, 'render_data'=>$render_data)));
                                }
                            
                            }else{
                                return "Error: ".$render_template." is missing.";
                            }
                        
                            // return "Found Property: ".$requested_property_name;
                        
                        }else{
                            return "Unknown Property: ".$requested_property_name;
                        }
                    }else{
                        if($this->draft_mode){
                            return "Error: Page Item failed to build.";
                        }
                    }
                }else{
                    if($this->draft_mode){
                        return "Notice: {property} tag on static page being ignored.";
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
                        
                            if($this->draft_mode){
                                $value = $property->getData()->getDraftContent();
                            }else{
                                $value = $property->getData()->getContent();
                            }
                        
                            $this->_smarty_include(array('smarty_include_tpl_file'=>$render_template, 'smarty_include_vars'=>array('raw_value'=>$value)));
                        
                        }else{
                            return "Error: ".$render_template." is missing.";
                        }
                        
                    
                    }else{
                        return "Unknown Property: ".$requested_property_name;
                    }
                }else{
                    if($this->draft_mode){
                        return "Error: repeated item is not an object.";
                    }
                }
                
            }
            
        }else{
            if($this->draft_mode && $this->_tpl_vars['this']['principal_item']){
                return "Error: {property} tag missing required 'name' attribute";
            }
        }
    }
    
	/* public function getTemplateAssetClass($assetclass, $params){

		$result = $this->templateHelper->getTemplateAssetClass($assetclass, $params);

		if($result['type'] == "TMPL"){
			$this->_smarty_include(array('smarty_include_tpl_file'=>'Assets/'.$result['file'], 'smarty_include_vars'=>array()));
			return $result['html'];
		}else{
        	return null;
        }
	} */
	
	public function getUserAgent(){
	    return SmartestPersistentObject::get('userAgent');
	}
	
	/* public function getAssetClass($assetclass, $params){
	
		$result = $this->templateHelper->getAssetClass($assetclass, $params);
		
		if($result['type'] != "TMPL"){
			return $result['html'];
		}
	} */
	
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
			$this->_smarty_include(array('smarty_include_tpl_file'=>$header, 'smarty_include_vars'=>array()));
		}
		
		if (is_array($items)){ 
		
			foreach ($items as $item){
 				$item_name=$item['item_name'];
				$properties=$item['property_details'];	
				$this->assign('name', $item_name);
				$this->assign('properties', $properties);
				$this->_smarty_include(array('smarty_include_tpl_file'=>$tpl_filename, 'smarty_include_vars'=>array()));
			}
			
		}
		
		if($result['footer']!="" && is_file(SM_ROOT_DIR."Presentation/ListItems/".$result['footer'])){
			$footer="ListItems/".$result['footer'];
			$this->_smarty_include(array('smarty_include_tpl_file'=>$footer, 'smarty_include_vars'=>array()));
		}
		
		return $result['html'];
	}
	
	/* public function getItemDetails($params){
		$set_name = $params['from'];
		$items = $this->templateHelper->getItemDetails($set_name);
 		return $items;
	} */
	
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