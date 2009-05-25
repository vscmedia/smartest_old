<?php

class SmartestParentMetaPagePropertyReference extends SmartestManyToManyLookup{
    
    public function getPage(){
        $p = new SmartestPage;
        if(!$p->hydrate($this->getEntityForeignKeyValue(1))){
            SmartestLog::getInstance('system')->log("Page with ID {$this->getEntityForeignKeyValue(1)} not found when searching for pages with referring property ID {$this->getEntityForeignKeyValue(2)}");
        }
        return $p;
    }
    
    public function getPageId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setPageId($id){
        return $this->setEntityForeignKeyValue(1, $id);
    }
    
    public function getProperty(){
        $p = new SmartestItemProperty;
        if(!$p->hydrate($this->getEntityForeignKeyValue(2))){
            SmartestLog::getInstance('system')->log("Item property with ID {$this->getEntityForeignKeyValue(2)} not found when searching for referring property id for page ID {$this->getEntityForeignKeyValue(1)}");
        }
        return $p;
    }
    
    public function getPropertyId(){
        return $this->getEntityForeignKeyValue(2);
    }
    
    public function setPropertyId($id){
        return $this->setEntityForeignKeyValue(2, $id);
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_MTMLOOKUP_PARENT_METAPAGE_RPID');
        }
        
        parent::save();
        
    }
    
}