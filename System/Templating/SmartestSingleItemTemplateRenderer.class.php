<?php

class SmartestSingleItemTemplateRenderer extends SmartestEngine{
    
    protected $_draft_mode = false;
    protected $_template_path;
    
    public function __construct($pid){
        
        parent::__construct($pid);
        $this->assign('domain', $this->_request_data->g('domain'));
        
        $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/WebPageBuilder/";
	    $this->left_delimiter = '<'.'?sm:';
		$this->right_delimiter = ':?'.'>';
        
    }
    
    public function getDraftMode(){
        return $this->_draft_mode;
    }

    public function setDraftMode($mode){
        $this->_draft_mode = SmartestStringHelper::toRealBool($mode);
    }
    
    public function assignTemplate($tpl_path){
        $this->_template_path = $tpl_path;
    }
    
    public function renderTemplate(){
        
        ob_start();
        $this->run($this->_template_path, array());
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
    
}