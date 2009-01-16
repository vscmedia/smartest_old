<?php

class SmartestSearchPage extends SmartestPage{
    
    protected $_results = array();
    protected $_query;
    
    public function setSearchQuery($query){
        $this->_query = $query;
    }
    
    public function getResults(){
        if($this->_query && !count($this->_results)){
            $this->_results = $this->getSite()->getSearchResults($this->_query);
            return $this->_results;
        }else{
            return array();
        }
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
        $data->setParameter('search_results', $this->getResultsAsArrays());
        $data->setParameter('num_search_results', count($data->getParameter('search_results')));
        return $data;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "query":
            return $this->_query;
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function __toArray(){
        $data = parent::__toArray();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), true);
        // $data['formatted_title'] = "";
        $data['query'] = $this->_query;
        return $data;
    }
    
}