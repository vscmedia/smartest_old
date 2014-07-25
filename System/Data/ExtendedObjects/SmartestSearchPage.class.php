<?php

class SmartestSearchPage extends SmartestPage{
    
    protected $_results = array();
    protected $_query;
    protected $_results_retrieved = false;
    
    public function setSearchQuery($query){
        $this->_query = $query;
        $this->_results_retrieved = false;
    }
    
    public function getSearchQuery(){
        return strip_tags($this->_query);
    }
    
    public function getResults(){
        // echo count($this->_results);
        if($this->_query){
            if(!$this->_results_retrieved){
                $this->_results = $this->getSite()->getSearchResults($this->_query);
                $this->_results_retrieved = true;
                
            }
            return $this->_results;
        }else{
            return array();
        }
    }
    
    public function getLastSearchTimeTaken(){
        
        return $this->getSite()->getLastSearchTimeTaken();
        
    }
    
    public function getResultsAsArrays(){
        
        $results = $this->getResults();
        $arrays = array();
        
        foreach($results as $r){
            $arrays[] = $r->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getDefaultUrl(){
        return 'search?q='.$this->_query;
    }
    
    public function fetchRenderingData(){
        
        $data = parent::fetchRenderingData();
        // $data['search_results'] = $this->getResultsAsArrays();
        // $data['num_search_results'] = count($data['search_results']);
        // print_r($data);
        $data->setParameter('search_results', $this->getResults());
        $data->setParameter('num_search_results', count($data->getParameter('search_results')));
        return $data;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "query":
            return $this->_query;
            
            case "is_search":
            return true;
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    /* public function __toArray(){
        $data = parent::__toArray();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), true);
        // $data['formatted_title'] = "";
        $data['query'] = $this->_query;
        return $data;
    } */
    
}