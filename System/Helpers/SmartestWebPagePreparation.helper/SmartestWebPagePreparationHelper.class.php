<?php

class SmartestWebPagePreparationHelper{
    
    protected $_page;
    protected $database;
    // protected $_controller;
    protected $_request;
    
    public function __construct($page){
        
        // $this->_controller = SmartestPersistentObject::get('controller');
        // $this->_request_data = SmartestPersistentObject::get('request_data');
        $this->_request = SmartestPersistentObject::get('controller')->getCurrentRequest();
        
        if($page instanceof SmartestPage){
            $this->_page = $page;
        }else{
            throw new SmartestException("Supplied data is not a valid SmartestPage object.");
        }
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    public function createBuilder(){
        
        $m = new SmartyManager("WebPageBuilder");
        $wpb = $m->initialize();
        return $wpb;
        
    }
    
    public function fetch($draft_mode=false){
        
        if($this->cachedPagesAllowed()){	
			return SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName(), true);
		}else{
			return $this->build($draft_mode);
		}
        
    }
    
    public function cachedPagesAllowed(){
        
        return (file_exists(SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName()) && $this->_page->getCacheAsHtml() == "TRUE" && $this->_page->getDraftMode() == false && ($this->_request->getAction() == 'renderPageFromId' || $this->_request->getAction() == 'renderPageFromUrl'));
        
    }
    
    public function build($draft_mode=''){
        
        $b = $this->createBuilder();
        
        $b->assign('domain', $this->_request->getDomain());
        $b->assign('method', $this->_request->getAction());
        $b->assign('section', $this->_request->getModule());
        
        if($ua = SmartestPersistentObject::get('userAgent')){
            $b->assign('sm_user_agent_json', $ua->getSimpleClientSideObjectAsJson());
            $b->assign('sm_user_agent', $ua->__toArray());
        }
        
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