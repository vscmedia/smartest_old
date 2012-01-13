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
    
    public function setAssetLabelFromInPlaceEditField(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setLabel($this->getRequestParameter('new_label'));
	        $asset->save();
	        // echo 'true';
	        echo $this->getRequestParameter('new_label');
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
        
    }
    
    public function setAssetOwnerById(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setUserId($this->getRequestParameter('owner_id'));
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
        
    }
    
    public function setAssetShared(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setShared($this->getRequestParameter('is_shared'));
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
        
    }
    
    public function assetComments(){
        
        $asset = new SmartestAsset;
        $asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id)){
		    
		    $this->send($asset, 'asset');
		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		
		}
        
    }
    
    public function submitAssetComment(){
        
        $asset = new SmartestAsset;
		$asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id)){
            
            $asset->addComment($this->getRequestParameter('comment_content'), $this->getUser()->getId());

		}
    }
    
    public function removeAssetComment(){
        
        $comment = new SmartestAssetComment;
        
        if($comment->find($this->getRequestParameter('comment_id'))){
            $comment->delete();
        }
        
    }
    
}