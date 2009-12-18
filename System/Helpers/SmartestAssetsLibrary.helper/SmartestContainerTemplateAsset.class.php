<?php

class SmartestContainerTemplateAsset extends SmartestTemplateAsset{
    
    public function isInUse($site_id=''){ // TODO: Site id not currently in use
        
        $sql = "SELECT assetclass_id FROM AssetIdentifiers, AssetClasses WHERE (AssetIdentifiers.assetidentifier_draft_asset_id='".$this->_properties['id']."' OR  AssetIdentifiers.assetidentifier_live_asset_id='".$this->_properties['id']."') AND AssetClasses.assetclass_type='SM_ASSETCLASS_CONTAINER' AND AssetClasses.assetclass_type=AssetIdentifiers.assetidentifier_assetclass_id";
        $result = $this->database->queryToArray($sql);
        return (bool) count($result);
        
    }
    
}