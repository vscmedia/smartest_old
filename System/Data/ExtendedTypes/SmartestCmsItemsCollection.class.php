<?php

class SmartestCmsItemsCollection extends SmartestArray implements SmartestSubmittableValue, SmartestManyToManyItemPropertyValue{

    public function addItem(SmartestCmsItem $item, $offset=''){
        $this->add($item, $offset);
    }
    
    public function addItemById($id, $offset=''){
        if($item = SmartestCmsItem::retrieveByPk($id)){
            $this->add($item, $offset);
        }
    }
    
    public function getIds(){
        $ids = array();
        foreach($this->_data as $item){
            $ids[] = $item->getId();
        }
        return $ids;
    }
    
    public function getItemIds(){
        return $this->getIds();
    }
    
    public function getSimpleIdsArray(){
        return $this->getIds();
    }
    
    /* public function getStorableFormat(){
        return implode(',', $this->getIds());
    }
    
    // Expects a comma-separated list of numeric ids
    public function hydrateFromStorableFormat($storable_format){
        $ids_array = preg_split('/[,\s]+/', $comma_separated_ids);
        $h = new SmartestCmsItemsHelper;
        $this->_data = $h->hydrateMixedListFromIdsArray($ids_array);
    } */
    
    public function hydrateFromFormData($v){
        if(parent::hydrateFromFormData($v)){
            
            $h = new SmartestCmsItemsHelper;
            $data = $h->hydrateMixedListFromIdsArray($v);
            
            if(is_array($data)){
                $this->_data = $data;
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function hydrateFromStoredIdsArray($ids, $draft_mode=false){
        if(parent::hydrateFromFormData($ids)){
            
            $h = new SmartestCmsItemsHelper;
            $data = $h->hydrateMixedListFromIdsArray($ids, $draft_mode);
            
            if(is_array($data)){
                $this->_data = $data;
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function getItemsSummary($char_length=100){
        
        if(count($this->_data)){
        
            $string = '';
            $overspill_buffer_base_length = 15;
            $last_key = count($this->_data) - 1;
        
            foreach($this->_data as $k=>$item){
                
                // is there room in the string
                $name = $item->getName();
                $len = strlen($name);
                $next_key = $k+1;
            
                if(isset($this->_data[$next_key])){
                    $digit_len = strlen(count(array_slice($this->_data, $next_key)));
                    // 2 is for quotes
                    $remaining_space = $char_length - $digit_len - $overspill_buffer_base_length - strlen($string) - 2;
                }else{
                    $remaining_space = $char_length - strlen($string) - 2;
                }
            
                if($k > 0 && $k < $last_key){
                    // 2 is for comma and space
                    $remaining_space = $remaining_space - 2;
                }else if($k == $last_key){
                    // 5 is for " and "
                    $remaining_space = $remaining_space - 5;
                }
            
                if($len <= $remaining_space){
                    if($k > 0 && $k < $last_key){
                        $string .= ', ';
                    }else if($k == $last_key){
                        if(count($this->_data) > 1){
                            $string .= ' and ';
                        }
                    }
                    $string .= '"'.$name.'"';
                }else{
                    $num_left = count(array_slice($this->_data, $k));
                    $string .= ' and '.$num_left.' more...';
                    break;
                }
            }
            
            return $string;
        
        }else{
            
            return "No items selected.";
            
        }
        
    }
    
    public function __toString(){
        return $this->getItemsSummary();
    }
    
    public function offsetGet($offset){
        
        if(is_numeric($offset)){
            // echo "fetch offset ".$offset;
            return $this->_data[$offset];
        }
        
        switch($offset){
            case "summary":
            return new SmartestString($this->getItemsSummary());
        }
        
        return parent::offsetGet($offset);
        
    }

}