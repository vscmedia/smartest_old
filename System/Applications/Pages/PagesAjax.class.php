<?php

class PagesAjax extends SmartestSystemApplication{

    public function tagPage(){
	    
	    $page = new SmartestPage;
	    
	    if($page->find($this->getRequestParameter('page_id'))){
	        
	        if($page->tag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }else{
	            header('HTTP/1.1 500 Internal Server Error');
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function unTagPage(){
	    
	    $page = new SmartestPage;
	    
	    if($page->find($this->getRequestParameter('page_id'))){
	        
	        if($page->untag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function setPageGroupLabelFromInPlaceEditField(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('pagegroup_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $group->setLabel($this->getRequestParameter('new_label'));
	        $group->save();
	        echo $this->getRequestParameter('new_label');
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}

}