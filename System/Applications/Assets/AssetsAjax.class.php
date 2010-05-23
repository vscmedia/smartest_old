<?php

class AssetsAjax extends SmartestSystemApplication{
    
    public function fullTextSearchResults(){
        
        $db = SmartestDatabase::getInstance('SMARTEST');
        $q = $this->getRequestParameter('query');
        $sql = "SELECT Assets.* FROM Assets, TextFragments WHERE TextFragments.textfragment_asset_id=Assets.asset_id AND (Assets.asset_site_id='".$this->getSite()->getId()."' OR Assets.asset_shared='1') AND Assets.asset_deleted=0 AND (Assets.asset_stringid LIKE '%".$q."%' OR Assets.asset_label LIKE '%".$q."%' OR (TextFragments.textfragment_content LIKE '%".$q."%')) ORDER BY Assets.asset_label LIMIT 150";
        $assets = array();
        
        if(strlen($q) > 2){
            
            $result = $db->queryToArray($sql);
            
            foreach($result as $r){
                $a = new SmartestAsset;
                $a->hydrate($r);
                $assets[] = $a;
            }
            
            $this->send($assets, 'assets');
            
        }
        
    }
    
}