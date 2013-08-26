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
        
        exit;
        
    }
    
    public function assetSearch(){
	    
	    $db = SmartestDatabase::getInstance('SMARTEST');
	    $q = $this->getRequestParameter('query');
	    // $sql = "SELECT Assets.* FROM Assets, TextFragments, Tags, TagsObjectsLookup WHERE (asset_site_id='".$this->getSite()->getId()."' OR asset_shared=1) AND asset_deleted='0' AND (Assets.asset_stringid LIKE '%".$q."%' OR Assets.asset_label LIKE '%".$q."%' OR (TextFragments.textfragment_content LIKE '%".$q."%' AND (TextFragments.textfragment_asset_id=Assets.asset_id OR Assets.asset_fragment_id=TextFragments.textfragment_id) OR (Tags.tag_label LIKE '%".$q."%' AND TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id=Assets.asset_id AND TagsObjectsLookup.taglookup_type='SM_ASSET_TAG_LINK'))) ORDER BY Assets.asset_label";
	    // $result = $db->queryToArray($sql);
	    $sql1 = "SELECT Assets.asset_id FROM Assets, TextFragments WHERE TextFragments.textfragment_asset_id=Assets.asset_id AND (Assets.asset_site_id='".$this->getSite()->getId()."' OR Assets.asset_shared='1') AND Assets.asset_deleted=0 AND (Assets.asset_stringid LIKE '%".$q."%' OR Assets.asset_label LIKE '%".$q."%' OR (TextFragments.textfragment_content LIKE '%".$q."%')) ORDER BY Assets.asset_label LIMIT 150";
	    $result1 = $db->queryToArray($sql1);
	    
	    $sql2 = "SELECT Assets.asset_id FROM Assets, Tags, TagsObjectsLookup WHERE Assets.asset_deleted=0 AND (Assets.asset_site_id='".$this->getSite()->getId()."' OR Assets.asset_shared='1') AND (Tags.tag_label LIKE '%".$q."%' AND TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id=Assets.asset_id AND TagsObjectsLookup.taglookup_type='SM_ASSET_TAG_LINK') LIMIT 150";
	    $result2 = $db->queryToArray($sql2);
	    
	    // echo $sql2;
	    
	    $asset_ids = array();
	    
	    foreach($result1 as $r){
	        $asset_ids[] = $r['asset_id'];
	    }
	    
	    foreach($result2 as $r){
	        $asset_ids[] = $r['asset_id'];
	    }
	    
	    $asset_ids = array_unique($asset_ids);
	    
	    $final_sql = "SELECT Assets.* FROM Assets WHERE Assets.asset_id IN ('".implode("','", $asset_ids)."')";
	    $result = $db->queryToArray($final_sql);
	    $assets = array();
	    
	    foreach($result as $r){
	        $asset = new SmartestAsset;
	        $asset->hydrate($r);
	        $assets[] = $asset;
	    }
	    
	    $this->send($assets, 'assets');
	    
	}
    
    public function setAssetLabelFromInPlaceEditField(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setLabel($this->getRequestParameter('new_label'));
	        $asset->save();
	        echo $this->getRequestParameter('new_label');
	        exit;
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        exit;
	        
	    }
        
    }
    
    public function setAssetOwnerById(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setUserId($this->getRequestParameter('owner_id'));
	        SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' set the owner id of file '.$asset->getLabel().' to'.$this->getRequestParameter('owner_id').'.');
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function setAssetLanguage(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setLanguage($this->getRequestParameter('asset_language'));
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function setAssetShared(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setShared($this->getRequestParameter('is_shared'));
	        
	        if($this->getRequestParameter('is_shared')){
	            SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' shared file '.$asset->getLabel().' with other sites.');
	        }else{
	            SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' made file '.$asset->getLabel().' no longer with other sites.');
	        }
	        
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function assetComments(){
        
        $asset = new SmartestAsset;
        $asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id)){
		    
		    $this->send($asset, 'asset');
		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		    header('HTTP/1.1 200 OK');
		
		}
        
    }
    
    public function submitAssetComment(){
        
        $asset = new SmartestAsset;
		$asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id)){
            
            $asset->addComment($this->getRequestParameter('comment_content'), $this->getUser()->getId());
            SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' left a comment on file '.$asset->getLabel().'.');
            header('HTTP/1.1 200 OK');

		}
		
		exit;
		
    }
    
    public function removeAssetComment(){
        
        $comment = new SmartestAssetComment;
        
        if($comment->find($this->getRequestParameter('comment_id'))){
            $comment->delete();
        }
        
    }
    
    public function updateGalleryOrder(){
        
        $group = new SmartestAssetGroup;
        
        if($group->find($this->getRequestParameter('group_id'))){
            if($group->getIsGallery()){
                if($this->getRequestParameter('new_order')){
                    $group->setNewOrderFromString($this->getRequestParameter('new_order'));
                    // echo "proceed";
                    header('HTTP/1.1 200 OK');
                }
            }
        }
        
        exit;
        
    }
    
    public function tagAsset(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        if($asset->tag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function unTagAsset(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        if($asset->untag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
}