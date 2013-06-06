<?php

class CmsFrontEndAjax extends SmartestSystemApplication{
    
    protected $_page;
	
	protected function __smartestApplicationInit(){
	    
	    $this->manager = new SmartestRequestUrlHelper;
	    
	}
    
    public function userAgent(){
        
        // Official Mime Type for JSON is 'application/json' See http://www.ietf.org/rfc/rfc4627.txt
        header('Content-type: application/json');
        echo $this->getUserAgent()->getSimpleClientSideObjectAsJson();
        exit;
        
    }
    
    public function pageInfo(){
        
        if($this->lookupSiteDomain()){
            
            define('SM_AJAX_CALL', true);
            
            $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->_site->getId()); // ID needed
    		$page_webid = $this->getRequestParameter('page_id');
    		
    		if($this->getRequestParameter('draft') && SmartestStringHelper::toRealBool($this->getRequestParameter('draft')) && $this->_auth->getUserIsLoggedIn()){
    		    $draft_mode = true;
    		}else{
    		    $draft_mode = false;
    		}
    		
    		if(isset($type_index[$page_webid])){
    		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		        $page = new SmartestItemPage;
    		    }else{
    		        $page = new SmartestPage;
    		    }
    		}else{
    		    $page = new SmartestPage;
    		}
    		
    		if($page->hydrate($page_webid)){
    		    
    		    $page->setDraftMode($draft_mode);
    		    
    		    if($page->getType() == 'ITEMCLASS'){
    		        
    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    		                $item->setDraftMode($draft_mode);
    		                $page->setPrincipalItem($item);
    		                header('Content-type: application/json');
    		                echo $page->__toJson();;
    		                exit;
    		            }else{
    		                // item could not be found
    		                header('Content-type: application/json');
    		                echo '{error: \'Item could not be found\'}';
    		                exit;
    		            }
    		        }else{
    		            // no item id
    		            header('Content-type: application/json');
		                echo '{error: \'No valid item ID provided\'}';
		                exit;
    		        }
    		        
    		    }else{
    		        header('Content-type: application/json');
	                echo $page->__toJson();;
	                exit;
    		    }
    		    
    		}
            
        }
        
    }
    
    public function pageFragment(){
        
        if($this->lookupSiteDomain()){
            
            define('SM_AJAX_CALL', true);
            
            // echo "hello world";
            $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->_site->getId()); // ID needed
    		$page_webid = $this->getRequestParameter('page_id');
    		
    		if($this->getRequestParameter('draft') && SmartestStringHelper::toRealBool($this->getRequestParameter('draft')) && $this->_auth->getUserIsLoggedIn()){
    		    $draft_mode = true;
    		}else{
    		    $draft_mode = false;
    		}

    		if(isset($type_index[$page_webid])){
    		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		        $page = new SmartestItemPage;
    		    }else{
    		        $page = new SmartestPage;
    		    }
    		}else{
    		    $page = new SmartestPage;
    		}
		
    		if($page->hydrate($page_webid)){
		        
		        $page->setDraftMode($draft_mode);
		        
    		    if($page->getType() == 'ITEMCLASS'){
		        
    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    		                $item->setDraftMode($draft_mode);
    		                $page->setPrincipalItem($item);
    		                $ph = new SmartestWebPagePreparationHelper($page);
    		            }else{
    		                // item could not be found
    		                echo "Item not found";
    		                exit;
    		            }
    		        }else{
    		            // no item id
    		        }
		        
    		    }else{
    		        // echo "hello";
    		        // page is a static page - no item
    		        $ph = new SmartestWebPagePreparationHelper($page);
		        
    		        $overhead_finish_time = microtime(true);
            		$overhead_time_taken = number_format(($overhead_finish_time - SM_START_TIME)*1000, 2, ".", "");

            		define("SM_OVERHEAD_TIME", $overhead_time_taken);
            		SmartestPersistentObject::get('timing_data')->setParameter('overhead_time', microtime(true));
                
                    $this->getRequestParameter('container_name');
                
                    define('SM_LINK_URLS_ABSOLUTE', true);
                
            	    $html = $ph->fetchContainer($this->getRequestParameter('container_name'), $draft_mode);

        	        ///// START FILTER CHAIN
            	    $fc = new SmartestFilterChain("WebPageBuilder");
            	    $fc->setDraftMode($draft_mode);
        	        $html = $fc->execute($html);

        	        $cth = 'Content-Type: '.$this->getRequest()->getContentType().'; charset='.$this->getRequest()->getCharSet();

            	    header($cth);
        	        echo $html;
        	        exit;
		        
    		    }
		    
    		}else{
		    
    		    echo "Page not found";
                exit;
		    
    		}
		
		}else{

            echo "No such domain";
            exit;

        }
        
    }

}