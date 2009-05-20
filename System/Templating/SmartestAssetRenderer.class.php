<?php

class SmartestAssetRenderer extends SmartestEngine{
    
    protected $_asset;
    protected $draft_mode;
    
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
    
    public function render($params, $render_data='', $path='none'){
        
        $asset_type_info = $this->_asset->getTypeInfo();
        $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
        
        if(!is_array($render_data)){
            $render_data = array();
        }
        
        if(isset($path)){
            $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
        }else{
            $path = 'none';
        }
        
        // if(file_exists($render_template)){
            
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
                    
                    // echo "Rendering Asset ID: ".$this->_asset->getId();
                    
                    // If draft, check that a temporary preview copy has been created, and creat it if not
                    if($this->getDraftMode()){
                        if($this->_asset->getTextFragment()->ensurePreviewFileExists()){
                            
                            $child = $this->startChildProcess($render_process_id);
                	        $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
                	        $child->setProperty('asset', $this->_asset);
                	        $child->setProperty('attachments', $attachments);
                	        
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
                	        
                	        $content = $child->fetch($this->_asset->getTextFragment()->getParsableFilePath());
                	        
                	        $this->killChildProcess($child->getProcessId());
                	        
                        }else{
                            $content = $this->raiseError("Asset '".$this->_asset->getStringid()."' is not published");
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
            
        /* }else{
            return $this->raiseError("Render template '".$render_template."' not found.");
        } */
        
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
            
            $sm = new SmartyManager('AssetRenderer');
            $r = $sm->initialize($asset->getStringId());
            $r->assignAsset($asset);
            $r->setDraftMode($this->getDraftMode());
            $content = $r->render($params, $render_data, $path);
            
        }else{
            $content = $this->raiseError("Render template '".$render_template."' not found.");
        }
        
        return $content;
        
    }
    
}