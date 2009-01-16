<?php

class SmartestWebPagePreparationHelper{
    
    protected $_page;
    
    public function __construct($page){
        
        $this->_controller = SmartestPersistentObject::get('controller');
        
        if($page instanceof SmartestPage){
            $this->_page = $page;
        }else{
            throw new SmartestException("Supplied data is not a valid SmartestPage object.");
        }
        
    }
    
    public function createBuilder(){
        
        $m = new SmartyManager("WebPageBuilder");
        $wpb = $m->initialize();
        return $wpb;
        
    }
    
    public function fetch($draft_mode=false){
        
        // var_dump($draft_mode);
        
        if($this->cachedPagesAllowed()){	
			// echo "retrieving from cache...";
			return SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName(), true);
			
		}else{
			// echo "building...";
			return $this->build($draft_mode);
			
		}
        
    }
    
    public function cachedPagesAllowed(){
        
        return (file_exists(SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName()) && $this->_page->getCacheAsHtml() == "TRUE" && $this->_page->getDraftMode() == false && SM_CONTROLLER_METHOD != 'searchDomain');
        
    }
    
    public function build($draft_mode=''){
        
        $b = $this->createBuilder();
        $b->assign('domain', SM_CONTROLLER_DOMAIN);
        
        if($draft_mode === true || $draft_mode === false){
            $b->setDraftMode($draft_mode);
        }else{
            $draft_mode = $this->_page->getDraftMode();
        }
        
        $content = $b->renderPage($this->_page, $draft_mode);
        
        if($this->_page->getCacheAsHtml() == "TRUE" && !$draft_mode){
        
            $filename = SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName();
		
		    if(!SmartestFileSystemHelper::save($filename, $content, true)){
			    SmartestLog::getInstance('system')->log("Page cache failed to build for page: ".$this->_page->getTitle());
		    }
		
	    }
		
		return $content;
        
    }
    
}