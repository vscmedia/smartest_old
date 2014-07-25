<?php

class SmartestBasicRenderer extends SmartestEngine{
    
    protected $_asset; // used when rendering an Asset
    protected $_image; // used when rendering a plain old image
    protected $draft_mode = false;
    protected $_other_pages;
    
    public function __construct($pid){
        
        parent::__construct($pid);
        $this->assign('domain', $this->_request_data->g('domain'));
        
        $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/WebPageBuilder/";
	    $this->left_delimiter = '<'.'?sm:';
		$this->right_delimiter = ':?'.'>';
		$this->caching = false;
		$this->_tpl_vars['sm_draft_mode'] = false;
		
		$this->_other_pages = new SmartestParameterHolder('Pages besides the main page');
        
    }
    
    public function getDraftMode(){
        return $this->draft_mode;
    }
    
    public function setDraftMode($mode){
        $this->draft_mode = SmartestStringHelper::toRealBool($mode);
        $this->_tpl_vars['sm_draft_mode'] = $this->draft_mode;
    }
    
    public function assignAsset(SmartestAsset $asset){
        
        $this->_asset = $asset;
        
    }
    
    public function assignImage(SmartestImage $image){
        
        $this->_image = $image;
        
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
                        
                        if($attachment['zoom'] && $attachment['asset']->isImage()){
                            $attachment['div_width'] = (int) $attachment['thumbnail']['width'];
                            $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/zoom_attachment.tpl';
                        }else{
                            $attachment['div_width'] = (int) $attachment['asset']['width'];
                            $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/attachment.tpl';
                        }
                        
                        if($this->_request_data->g('action') == "renderEditableDraftPage" || ($this->_request_data->g('action') == "pageFragment" && $this->getDraftMode())){
            			    $attachment['edit_link'] = "<a title=\"Click to edit definition for attachment: ".$name."\" href=\"".$this->_request_data->g('domain')."assets/defineAttachment?attachment=".$name."&amp;asset_id=".$asset->getId()."&amp;from=pagePreviewDirectEdit\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Attach a different file--></a>";
            		    }else{
            			    $attachment['edit_link'] = "<!--edit link-->";
            		    }
            		    
                        $this->run($file, array('_textattachment'=>$attachment));
                        
                    }else{
                        // asset tag exists, but isn't defined.
                        $this->_comment("asset tag exists, but isn't defined.");
                    }
                }else{
                    return $this->raiseError('Attachment \''.$name.'\' does not exist.');
                }
            
            }else{
                
                return $this->raiseError('Attachment tags can only be used in text files.');
                
            }
	        
        }
        
    }
    
    public function renderAsset($render_data='', $path='none', $preview_mode=false){
        
        $asset_type_info = $this->_asset->getTypeInfo();
        
        if($preview_mode){
            $render_template = SM_ROOT_DIR.$asset_type_info['render']['preview_template'];
        }else{
            $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
        }
        
        if(!is_array($render_data) && !($render_data instanceof SmartestParameterHolder)){
            $render_data = array();
        }
        
        if(isset($path)){
            $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
        }else{
            $path = 'none';
        }
        
        if(file_exists($render_template)){
            
            if(isset($params['style']) && strlen($params['style'])){
                $render_data['style'] = $params['style'];
            }
            
            if($path == 'file'){
                $content = $this->_asset->getUrl();
            }else if($path == 'full'){
                $content = $this->_asset->getFullWebPath();
            }else{
                
                if($this->_asset->usesTextFragment()){
                    
                    $render_process_id = SmartestStringHelper::toVarName('textfragment_'.$this->_asset->getStringid().'_'.substr(microtime(true), -6));
                    
                    if($this->_asset->getTextFragment()->containsAttachmentTags()){
                        $attachments = $this->_asset->getTextFragment()->getAttachments();
                    }else{
                        $attachments = array();
                    }
                    
                    // If draft, check that a temporary preview copy has been created, and creat it if not
                    if($this->getDraftMode()){
                        if($this->_asset->getTextFragment()->ensurePreviewFileExists()){
                            
                            $child = $this->startChildProcess($render_process_id);
                	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
                	        $child->setProperty('asset', $this->_asset);
                	        $child->setProperty('attachments', $attachments);
                	        $child->setDraftMode($this->getDraftMode());
                	        
                	        $content = $child->fetch($this->_asset->getTextFragment()->getParsableFilePath(true));
                	        
                	        $this->killChildProcess($child->getProcessId());
                	        
                        }else{
                            $content = $this->raiseError('TextFragment render preview could not be created.');
                        }
                    }else{
                    // otherwise parse local disk copy.
                        if($this->_asset->getTextFragment()->isPublished()){
                            
                	        $child = $this->startChildProcess($render_process_id);
                	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
                	        $child->setProperty('asset', $this->_asset);
                	        $child->setProperty('attachments', $attachments);
                	        $child->setDraftMode($this->getDraftMode());
                	        
                	        $content = $child->fetch($this->_asset->getTextFragment()->getParsableFilePath());
                	        
                	        $this->killChildProcess($child->getProcessId());
                	        
                        }else{
                            $content = $this->raiseError("Asset '".$this->_asset->getStringid()."' is not published");
                        }
                    }
                    
                    $parser = new SmartestDataBaseStoredTextAssetToolkit();
                    $method = $this->_asset->getParseMethodName();
                    
                    if(method_exists($parser, $method)){
                        $content = $parser->$method($content, $this->_asset, $this);
                    }
                    
                }else{
                    
                    if($this->_asset->isImage()){
                        
                        $image = $this->_asset->getImage();
                        
                        $actual_img_width = $this->_asset->getImage()->getWidth();
                        $actual_img_height = $this->_asset->getImage()->getHeight();
                        
                        if(isset($render_data['width']) && isset($render_data['height']) && is_numeric($render_data['width']) && is_numeric($render_data['height']) && ($render_data['width'] != $actual_img_width || $render_data['height'] != $actual_img_height)){
                            $image = $this->_asset->getImage()->resizeAndCrop($render_data['width'], $render_data['height']);
                        }else if(isset($render_data['width']) && is_numeric($render_data['width']) && (!isset($render_data['height']) || !is_numeric($render_data['height'])) && $render_data['width'] != $actual_img_width){
                            $image = $this->_asset->getImage()->restrictToWidth($render_data['width']);
                        }else if(isset($render_data['height']) && is_numeric($render_data['height']) && (!isset($render_data['width']) || !is_numeric($render_data['width'])) && $render_data['height'] != $actual_img_height){
                            $image = $this->_asset->getImage()->restrictToHeight($render_data['height']);
                        }
                        
                        if(!isset($render_data['width'])){
                            $render_data['width'] = $image->getWidth();
                        }

                        if(!$render_data['height']){
                            $render_data['height'] = $image->getHeight();
                        }
                    
                    }
                    
                    ob_start();
                    $this->run($render_template, array('asset_info'=>$this->_asset, 'render_data'=>$render_data, 'image'=>$image));
                    $content = ob_get_contents();
        	        ob_end_clean();
                    
                }
            }
            
            if(isset($path)){
                $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
                if($path == 'none'){
                    $edit_link = $this->renderEditAssetButton($this->_asset->getId(), $render_data);
                }
            }else{
                $path = 'none';
                $edit_link = $this->renderEditAssetButton($this->_asset->getId(), $render_data);
            }
	        
	        $content .= $edit_link;
            return $content;
            
        }else{
            return $this->raiseError("Render template '".$render_template."' not found.");
        }
        
    }
    
    public function renderEditAssetButton($asset_id, $render_data='', $editableonly=true){
        
        if(is_object($this->_asset) && $this->_asset->getId() == $asset_id){
            $asset = $this->_asset;
        }else{
            $asset = new SmartestAsset;
            if(!$asset->find($asset_id)){
                return $this->_raiseError('Asset with ID '.$asset_id.' could not be found.');
            }
        }
        
        if(!is_array($render_data) && !($render_data instanceof SmartestParameterHolder)){
            $render_data = array();
        }
        
        $asset_type_info = $asset->getTypeInfo();
        
        $show_preview_edit_link = (!isset($asset_type_info['show_preview_edit_link']) || SmartestStringHelper::toRealBool($asset_type_info['show_preview_edit_link']));
        
        /* var_dump(isset($asset_type_info['editable']) && SmartestStringHelper::toRealBool($asset_type_info['editable']));
        
        print_r($asset_type_info); */
        
        if($render_data instanceof SmartestParameterHolder && $render_data->hasParameter('show_preview_edit_link')){
            $show_preview_edit_link = SmartestStringHelper::toRealBool($render_data->getParameter('show_preview_edit_link'));
        }else if(is_array($render_data) && isset($render_data['show_preview_edit_link'])){
            $show_preview_edit_link = SmartestStringHelper::toRealBool($render_data['show_preview_edit_link']);
        }
        
        if(($this->_request_data->g('action') == "renderEditableDraftPage" || ($this->_request_data->g('action') == "pageFragment" && $this->getDraftMode())) && $show_preview_edit_link){
		    
		    if(!$editableonly || (isset($asset_type_info['editable']) && SmartestStringHelper::toRealBool($asset_type_info['editable']))){
		        $edit_link = '';
		        $edit_url = $this->_request_data->g('domain')."assets/editAsset?asset_id=".$asset->getId()."&amp;from=pagePreview";
		        if($this->_request_data->g('request_parameters')->hasParameter('item_id')) $edit_url .= '&item_id='.$this->_request_data->g('request_parameters')->getParameter('item_id');
		        if($this->_request_data->g('request_parameters')->hasParameter('page_id')) $edit_url .= '&page_id='.$this->_request_data->g('request_parameters')->getParameter('page_id');
		        $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit file: ".$asset->getUrl()." (".$asset->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
		    }else{
		        $edit_link = "<!--edit link-->";
	        }
	    
        }else{
            
            $edit_link = "<!--no edit link-->";
        }
        
        return $edit_link;
        
    }
    
    public function renderLink(SmartestCmsLink $link){
        
        $ph = new SmartestParameterHolder("Link Attibutes: ".$link->getDestinationProperties()->getParameter('destination'));
        
        if($link->hasError()){
            
            // eo = Error Output
            $eo = '';
            
            if($link->getDestinationProperties()->getParameter('text') && ($link->getDestinationProperties()->getParameter('text') != SmartestLinkParser::LINK_TARGET_TITLE)){
                $eo .= $link->getDestinationProperties()->getParameter('text');
            }else if($link->getRenderData()->hasParameter('with') && ($link->getRenderData()->getParameter('with') != SmartestLinkParser::LINK_TARGET_TITLE)){
                $eo .= $link->getRenderData()->getParameter('with');
            }
            
            $eo .= $this->raiseError($link->getErrorMessage());
            
            return $eo;
        }
        
        $link_params = array();
        
        $link_params['_link_url'] = $link->getUrl($this->draft_mode);
        $link_params['_link_use_span'] = SmartestStringhelper::toRealBool($link->getRenderData()->getParameter('span'));
        $link_params['_link_span_invisible'] = ($link->getRenderData()->hasParameter('spanvisible') && !SmartestStringhelper::toRealBool($link->getRenderData()->getParameter('spanvisible')));
        $link_params['_link_contents'] = $link->getContent($this->draft_mode);
        $link_params['_link_parameters'] = SmartestStringHelper::toAttributeString($link->getMarkupAttributes()->getParameters());
        $link_params['_link_show_anchor'] = !$link->shouldOmitAnchorTag($this->draft_mode);
        
        $this->_tpl_vars['_linkparameters'] = $link_params;
        
        $this->caching = false;
        $html = $this->fetch(SM_ROOT_DIR."System/Presentation/WebPageBuilder/basic_link.tpl");
        $this->caching = true;
        
        return $html;
        
    }
    
    public function renderImage($render_data='', $path='none'){
        
        if(!$render_data['width']){
            $render_data['width'] = $this->_image->getWidth();
        }
        
        if(!$render_data['height']){
            $render_data['height'] = $this->_image->getHeight();
        }
            
        $render_template = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/display.image.tpl';
        
        ob_start();
        $this->run($render_template, array('render_data'=>$render_data, 'image'=>$this->_image));
        $content = ob_get_contents();
        ob_end_clean();
        
        return $content;
        
    }
    
    public function renderSmartestCreditButton(){
	    
	    $target = $this->getDraftMode() ? ' target="_blank"' : '';
	    return "<a href=\"http://sma.rte.st/\" title=\"This website is powered by Smartest\"".$target."><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/smartest_credit_button.png\" alt=\"Powered by Smartest - Content Management System to the Stars\" style=\"border:0px\" /></a>";
	    
	}
	
	public function hasOtherPage($page_name){
	    return $this->_other_pages->hasParameter($page_name);
	}
	
	public function getOtherPage($page_name){
	    return $this->_other_pages->getParameter($page_name);
	}
	
	public function addOtherPage($page_name, SmartestPage $page){
	    $this->_other_pages->setParameter($page_name, $page);
	}
    
}