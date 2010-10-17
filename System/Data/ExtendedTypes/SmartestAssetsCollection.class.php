<?php

class SmartestAssetsCollection extends SmartestArray implements SmartestSubmittableValue, SmartestManyToManyItemPropertyValue{
    
    public function getIds(){
        $ids = array();
        foreach($this->_data as $item){
            $ids[] = $item->getId();
        }
        return $ids;
    }
    
    public function hydrateFromFormData($v){
        /* if(parent::hydrateFromFormData($v)){
            
            // $h = new SmartestCmsItemsHelper;
            // $data = $h->hydrateMixedListFromIdsArray($v);
            
            if(is_array($data)){
                $this->_data = $data;
                return true;
            }else{
                return false;
            }
        } */
        return $this->hydrateFromStoredIdsArray($v);
    }
    
    public function hydrateFromStoredIdsArray($ids){
        if(parent::hydrateFromFormData($ids)){
            
            // $h = new SmartestCmsItemsHelper;
            // $data = $h->hydrateMixedListFromIdsArray($ids);
            if(count($ids)){
                
                $sql = "SELECT * FROM Assets WHERE asset_id IN (".implode(', ', $ids).")";
                $db = SmartestDatabase::getInstance('SMARTEST');
                $result = $db->queryToArray($sql);
                
                if(count($ids) && !count($result)){
                    return false;
                }
                
                $assets = array();
                
                foreach($result as $a){
                    $asset = new SmartestRenderableAsset;
                    $asset->hydrate($a);
                    $assets[] = $asset;
                }
                
                $this->_data = $assets;
                return true;
            
            }else{
                return true;
            }
        }
    }
    
    public function getFilesSummary($char_length=100){
    
        if(count($this->_data)){
    
            $string = '';
            $overspill_buffer_base_length = 15;
            $last_key = count($this->_data) - 1;
    
            foreach($this->_data as $k=>$file){
                // is there room in the string
                $name = $file->getLabel();
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
        
                  // if there are still more after this one, then buffer space is needed
                    // amount of buffer space depends on how many digits this figure is
            }
        
            return $string;
    
        }else{
        
            return "No items selected.";
        
        }
    
    }

    public function __toString(){
        return $this->getFilesSummary();
    }

    public function offsetGet($offset){
    
        switch($offset){
            case "summary":
            return new SmartestString($this->getFilesSummary());
        }
    
        return parent::offsetGet($offset);
    
    }

}