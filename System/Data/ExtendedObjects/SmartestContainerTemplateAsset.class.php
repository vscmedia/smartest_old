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
    
}