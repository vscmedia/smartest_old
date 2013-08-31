<?php

class SmartestPageNavigationDataRequestHandler implements ArrayAccess{
    
    protected $_page;
    
    
    public function __construct(SmartestPage $page){
        $this->assignPage($page);
    }
    
    public function assignPage(SmartestPage $page){
        $this->_page = $page;
    }
    
    public function getParentPage(){
        if(!$this->_page->isHomePage()){
            return $this->_page->getParentPage();
        }
    }
    
    public function getParentLevelPages(){
        if($this->_page->getParentPage()->getId() == $this->_page->getParentSite()->getTopPageId()){
	        return array($this->_page->getParentPage($this->_page->getDraftMode()));
	    }else{
	        return $this->_page->getGrandParentPage($this->_page->getDraftMode())->getPageChildrenForWeb();
        }
    }
    
    public function getHomePage(){
        if($this->_page->isHomePage()){
            return $this->_page;
        }else{
            return $this->_page->getParentSite()->getHomePage($this->_page->getDraftMode());
        }
    }
    
    public function getMainSections(){
        if($this->_page->isHomePage()){
            return $this->_page->getPageChildrenForWeb(true);
        }else{
            return $this->getHomePage()->getPageChildrenForWeb(true);
        }
    }
    
    public function offsetGet($offset){
        
        switch($offset){
        
            case "home_page":
            case "home":
            return $this->getHomePage();
        
            case "parent":
            case "parent_page":
            return $this->getParentPage();
        
            case "section":
            case "current_section":
            case "section_page":
            return $this->_page->getSectionPage();
        
            case "_breadcrumb_trail":
            return $this->_page->getPageBreadCrumbs();
        
            case "sibling_level_pages":
            case "sibling_pages":
            case "pages_with_same_parent":
            return $this->_page->getParentPage($this->_page->getDraftMode())->getPageChildrenForWeb();
        
            case "parent_level_pages":
            return $this->getParentLevelPages();
        
            case "child_pages":
            return $this->_page->getPageChildrenForWeb();
        
            case "main_sections":
            return $this->getMainSections();
        
            case "related":
            return $this->_page->getRelatedContentForRender();
        
            case "is_home_page":
            return $this->_page->isHomePage();
        
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetExists($offset){}
    public function offsetUnset($offset){}
    
}