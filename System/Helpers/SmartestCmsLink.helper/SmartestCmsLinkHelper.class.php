<?php

SmartestHelper::register('CmsLink');

class SmartestCmsLinkHelper extends SmartestHelper{
    
    private static $database;
    
    static function getDatabase(){
        
        if(!self::$database){
            
            self::$database = SmartestDatabase::getInstance('SMARTEST');
            
        }
        
    }
    
    public static function createLink($to, $markup_attributes){
        
        if($to instanceof SmartestCmsItem){
            $link = self::createLinkFromCmsItem($to, $markup_attributes);
        }else{
            $properties = SmartestLinkParser::parseSingle($to);
            $link = new SmartestCmsLink($properties, $markup_attributes);
        }
        
        return $link;
        
    }
    
    public static function createLinkFromCmsItem(SmartestCmsItem $item, $markup_attributes){
        
        $properties = new SmartestParameterHolder('Link to existing CMS Item');
        $properties->setParameter('from_item', true);
        $properties->setParameter('item', $item);
        
        $link = new SmartestCmsLink($properties, $markup_attributes);
        
        return $link;
        
    }
    
    public static function getPageAsLinkTarget(){
        
        $sql = "SELECT * FROM Pages WHERE page_".$key."='".$value."' AND page_site_id='".constant('SM_CMS_PAGE_SITE_ID')."' AND page_deleted != 'TRUE'";
        $result = self::getDatabase()->queryToArray($sql);
        
        
        $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex(constant('SM_CMS_PAGE_SITE_ID'));
        
    }
    
    /* public static function parse($ds){
        
        // $params = SmartestLinkParser::parseSingle($ds);
        
        // print_r($params);
        
        // return $params;
        
        $link_parts = explode(':', $link);
        $type = $link_parts[0];
        
        switch($type){
            
            case "page":
            $this->_destination_type = 'page';
            // $l->setType(SM_LINKTYPE_PAGE);
            // print_r($link_parts);
            if($link_parts[1]){
                
                $page_identifier = $link_parts[1];
                
                if($lookup = $this->parseLinkIdentifier($page_identifier)){
                    
                    $this->_page = new SmartestPage;
                    
                    $sql = "SELECT * FROM Pages WHERE page_".$lookup['key']."='".$lookup['value']."' AND page_site_id='".constant('SM_CMS_PAGE_SITE_ID')."' AND page_deleted != 'TRUE'";
                    $result = $this->database->queryToArray($sql);
                    
                    if(count($result)){
                        $this->_page->hydrate($result[0]);
                        // success!
                    }else{
                        $this->_page_not_found = true;
                        $this->_error = true;
                    }
                    
                }else{
                    $this->_page_not_found = true;
                    $this->_error_message = 'Link format invalid';
                    $this->_error = true;
                    return false;
                }
                
            }else{
                // whatever was passed in the to attribute must have ended in a colon, because $link_parts[1] is empty
                $this->_page_not_found = true;
                $this->_error_message = 'Link format invalid';
                $this->_error = true;
                return false;
            }
            
            break;
            
            case "metapage":
            $this->_destination_type = 'metapage';
            // $l->setType(constant('SM_LINKTYPE_METAPAGE'));
            
            if($link_parts[1] && $link_parts[2]){
                
                $page_identifier = $link_parts[1];
                $item_identifier = $link_parts[2];
                
                if($page_lookup = $this->parseLinkIdentifier($page_identifier)){
                    
                    $this->_page = new SmartestPage;
                    $sql = "SELECT * FROM Pages WHERE page_".$page_lookup['key']."='".$page_lookup['value']."' AND page_site_id='".constant('SM_CMS_PAGE_SITE_ID')."' AND page_deleted != 'TRUE'";
                    $result = $this->database->queryToArray($sql);
                    
                    // echo $sql;
                    
                    // if($this->_page->hydrateBy($lookup['key'], $lookup['value'])){
                    if(count($result)){
                        $this->_page->hydrate($result[0]);
                        // var_dump($this->_page->getId());
                        $this->_item = new SmartestItem;
                        if($item_lookup = $this->parseLinkIdentifier($item_identifier)){

                            if($item_lookup['key'] == 'name'){
                                $key = 'slug';
                            }else{
                                $key = $item_lookup['key'];
                            }
                            
                            if($this->_item->hydrateBy($key, $item_lookup['value'], $this->_page->getSiteId())){
                                // success!
                            }else{
                                $this->_error_message = 'Item not found';
                                $this->_error = true;
                                return false;
                            }

                        }else{
                            $this->_error_message = 'Link format invalid';
                            $this->_error = true;
                            return false;
                        }
                        
                    }else{
                        // $this->_error_message = 'Page not found';
                        // $this->_error = true;
                        // return false;
                        $this->_error = true;
                        $this->_page_not_found = true;
                    }
                    
                }else{
                    $this->_error_message = 'Link format invalid';
                    $this->_error = true;
                    return false;
                }
                
            }else{
                $this->_error_message = 'Link format invalid';
                $this->_error = true;
                return false;
            }
            
            break;
            
            case "image":
            $this->_destination_type = 'image';
            // $l->setType(constant('SM_LINKTYPE_IMAGE'));
            
            if($link_parts[1]){
                // $image_file = $link_parts[1];
                if(in_array($link_parts[1], array('http', 'https'))){
                    $this->_image_file = $link_parts[1].':'.$link_parts[2];
                }else{
                    $this->_image_file = $link_parts[1];
                }
            }
            
            break;
            
            case "tag":
            $this->_destination_type = 'tag_page';
            // $l->setType(constant('SM_LINKTYPE_TAG'));
            
            if($link_parts[1]){
                $this->_tag_name = $link_parts[1];
            }else{
                $this->_error_message = 'A tag name must be provided. Link format invalid.';
                $this->_error = true;
                return false;
            }
            
            break;
            
            case "download":
            $this->_destination_type = 'download';
            // $l->setType(constant('SM_LINKTYPE_DOWNLOAD'));
            
            $asset_identifier = $link_parts[1];
            // echo $asset_identifier;
            
            if($asset_lookup = $this->parseLinkIdentifier($asset_identifier)){
                
                if($asset_lookup['key'] == 'name'){
                    $key = 'stringid';
                }else{
                    $key = $asset_lookup['key'];
                }
                
                $this->_download = new SmartestAsset;
                
                if($this->_download->hydrateBy($key, $asset_lookup['value'])){
                    // success!
                }else{
                    // echo 'Asset not found';
                    $this->_error_message = 'Asset not found';
                    $this->_error = true;
                    return false;
                }
                
            }else{
                // echo 'Link format invalid';
                $this->_error_message = 'Link format invalid';
                $this->_error = true;
                return false;
            }
            
            break;
            
            default:
            // return
            // if(in_array($type, )){
                
            // }
            
            $this->_destination_type = 'external';
            // $l->setType(constant('SM_LINKTYPE_EXTERNAL'));
            $this->_external_destination = $link;
            break;
            
        }
        
        return true;
        
    }  */

}