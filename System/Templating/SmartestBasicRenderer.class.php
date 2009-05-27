<?php

class SmartestBasicRenderer extends SmartestEngine{
    
    protected $_asset; // used when rendering an Asset
    protected $draft_mode = false;
    
    public function __construct($pid){
        
        parent::__construct($pid);
        $this->assign('domain', SM_CONTROLLER_DOMAIN);
        
        $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/WebPageBuilder/";
	    $this->left_delimiter = '<'.'?sm:';
		$this->right_delimiter = ':?'.'>';
        
    }
    
    public function getDraftMode(){
        return $this->draft_mode;
    }
    
    public function setDraftMode($mode){
        $this->draft_mode = SmartestStringHelper::toRealBool($mode);
    }
    
    public function assignAsset(SmartestAsset $asset){
        
        $this->_asset = $asset;
        
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
                    return $this->raiseError('Attachment \''.$name.'\' does not exist.');
                }
            
            }else{
                
                return $this->raiseError('Attachment tags can only be used in text files.');
                
            }
	        
        }
        
    }
    
    public function renderAsset($render_data='', $path='none'){
        
        // print_r($render_data);
        
        $asset_type_info = $this->_asset->getTypeInfo();
        $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
        
        if(!is_array($render_data) && !($render_data instanceof SmartestParameterHolder)){
            $render_data = array();
        }
        
        if(isset($path)){
            $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
        }else{
            $path = 'none';
        }
        
        if(file_exists($render_template)){
            
            if($this->_asset->isImage()){
		        
		        if(!$render_data['width']){
                    $render_data['width'] = $this->_asset->getWidth();
                }
                
                if(!$render_data['height']){
                    $render_data['height'] = $this->_asset->getHeight();
                }
            }
            
            if(isset($params['style']) && strlen($params['style'])){
                $render_data['style'] = $params['style'];
            }
            
            if($path == 'file'){
                $content = $this->_asset->getUrl();
            }else if($path == 'full'){
                $content = $this->_asset->getFullWebPath();
            }else{
                if($this->_asset->usesTextFragment() && $this->_asset->isParsable()){
                    
                    $render_process_id = SmartestStringHelper::toVarName('textfragment_'.$this->_asset->getStringid().'_'.substr(microtime(true), -6));
                    
                    $attachments = $this->_asset->getTextFragment()->getAttachments();
                    
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
                    
                    $links = SmartestLinkParser::parseEasyLinks($content);
                    
                    foreach($links as $l){
                        
                        $link = new SmartestCmsLink($l, array());
                        
                        if($link->hasError()){
                            $content = str_replace($l->getParameter('original'), $this->raiseError($link->getErrorMessage()), $content);
                        }else{
                            $content = str_replace($l->getParameter('original'), $link->render($this->getDraftMode()), $content);
                        }
                    }
                    
                }else{
                    
                    ob_start();
                    $this->run($render_template, array('asset_info'=>$this->_asset, 'render_data'=>$render_data));
                    $content = ob_get_contents();
        	        ob_end_clean();
                    
                }
            }
            
            if(SM_CONTROLLER_METHOD == "renderEditableDraftPage" && $path == 'none'){
			    
			    if(isset($asset_type_info['editable']) && $asset_type_info['editable'] && $asset_type_info['editable'] != 'false'){
			        $edit_link .= "<a title=\"Click to edit file: ".$this->_asset->getUrl()." (".$this->_asset->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?asset_id=".$this->_asset->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    }else{
			        $edit_link = "<!--edit link-->";
		        }
		    
	        }
		    
		    $content .= $edit_link;
            
            return $content;
            
        }else{
            return $this->raiseError("Render template '".$render_template."' not found.");
        }
        
    }
    
    public function renderLink(SmartestCmsLink $link){
        
        $ph = new SmartestParameterHolder("Link Attibutes: ".$link->getDestinationProperties()->getParameter('destination'));
        
        if($link->hasError()){
            return $this->raiseError($link->getErrorMessage());
        }
        
        $render_process_id = 'dynamic_link_'.substr(md5($link->getUrl($this->draft_mode)), 0, 8);
        
        $child = $this->startChildProcess($render_process_id);
        $child->setContext(SM_CONTEXT_HYPERLINK);
        $child->assign('_link_url', $link->getUrl($this->draft_mode));
        $child->assign('_link_contents', $link->getContent($this->draft_mode));
        $child->assign('_link_parameters', SmartestStringHelper::toAttributeString($link->getMarkupAttributes()->getParameters()));
        $child->assign('_link_show_anchor', !$link->shouldOmitAnchorTag($this->draft_mode));
        
        $html = $child->fetch(SM_ROOT_DIR."System/Presentation/WebPageBuilder/basic_link.tpl");
        $this->killChildProcess($child->getProcessId());
        
        return $html;
        
    }
    
}