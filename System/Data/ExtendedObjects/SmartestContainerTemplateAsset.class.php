<?php 

class SmartestContainerTemplateAsset extends SmartestAsset{
    
    protected $_template_file;
    protected $_base_dir = '';
    
    public function getFile(){
        
        $this->_base_dir = SM_ROOT_DIR.'Presentation/Layouts/';
        
        if(!$this->_template_file){
            
            $this->_template_file = new SmartestFile();
            
            if($this->_template_file->loadFile($this->_base_dir.$this->getUrl())){
                    
            }else{
                // file doesn't exist or isn't readable
            }
            
        }
        
        return $this->_template_file;
        
    }
    
    public function getArrayForElementsTree($level){
	    
	    $info = array();
	    $info['asset_id'] = $this->getId();
	    $info['asset_webid'] = $this->getWebid();
	    $info['asset_type'] = $this->getType();
	    $info['assetclass_name'] = $this->getStringid();
	    $info['assetclass_id'] = 'asset_'.$this->getId();
	    $info['defined'] = 'PUBLISHED';
	    $info['exists'] = 'true';
	    $info['filename'] = $this->getUrl();
	    $info['type'] = 'template';
	    $level++;
	    return array('info'=>$info, 'level'=>$level);
	}
    
}