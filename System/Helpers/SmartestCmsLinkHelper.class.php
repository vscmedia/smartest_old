<?php

SmartestHelper::register('CmsLink');

class SmartestCmsLinkHelper extends SmartestHelper{
    
    protected $_item;
    protected $_page;
    protected $_host_page;
    protected $_params = array();
    protected $_type;
    protected $_error_message;
    protected $_error = false;
    protected $_draft_mode = false;
    protected $_preview_mode = false;
    protected $_image_file = null;
    protected $_tag_name = null;
    protected $_download = null;
    protected $_page_not_found = false;
    protected $_external_destination = null;
    protected $database;
    
    public function __construct($page='', $params='', $draft=false, $preview=false){
        
        if(is_array($params)){
            $this->_params = $params;
        }
        
        if(is_object($page)){
            $this->_host_page = $page;
        }
        
        $this->database = SmartestPersistentObject::get('db:main');
        $this->_draft_mode = $draft;
        $this->_preview_mode = $preview;
    }
    
    public function parse($link){
        
        $link_parts = explode(':', $link);
        $type = $link_parts[0];
        
        switch($type){
            
            case "page":
            $this->_type = 'page';
            // print_r($link_parts);
            if($link_parts[1]){
                
                $page_identifier = $link_parts[1];
                
                // echo $page_identifier;
                
                if($lookup = $this->parseLinkIdentifier($page_identifier)){
                    
                    $this->_page = new SmartestPage;
                    
                    $sql = "SELECT * FROM Pages WHERE page_".$lookup['key']."='".$lookup['value']."' AND page_site_id='".constant('SM_CMS_PAGE_SITE_ID')."' AND page_deleted != 'TRUE'";
                    $result = $this->database->queryToArray($sql);
                    
                    // if($this->_page->hydrateBy($lookup['key'], $lookup['value'])){
                    if(count($result)){
                        $this->_page->hydrate($result[0]);
                        // success!
                    }else{
                        $this->_page_not_found = true;
                        $this->_error = true;
                    }
                    /* else{
                        $this->_error_message = 'Page not found';
                        $this->_error = true;
                        return false;
                    } */
                    
                }else{
                    $this->_error_message = 'Link format invalid';
                    $this->_error = true;
                    return false;
                }
                
            }else{
                // whatever was passed in the to attribute must have ended in a colon, because $link_parts[1] is empty
                $this->_error_message = 'Link format invalid';
                $this->_error = true;
                return false;
            }
            
            break;
            
            case "metapage":
            $this->_type = 'metapage';
            
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
                        
                        if($item_lookup = $this->parseLinkIdentifier($item_identifier)){

                            $this->_item = new SmartestItem;
                            
                            if($item_lookup['key'] == 'name'){
                                $key = 'slug';
                            }else{
                                $key = $item_lookup['key'];
                            }
                            
                            if($this->_item->hydrateBy($key, $item_lookup['value'])){
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
            $this->_type = 'image';
            
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
            $this->_type = 'tag_page';
            
            if($link_parts[1]){
                $this->_tag_name = $link_parts[1];
            }else{
                $this->_error_message = 'A tag name must be provided. Link format invalid.';
                $this->_error = true;
                return false;
            }
            
            break;
            
            case "download":
            $this->_type = 'download';
            
            $asset_identifier = $link_parts[1];
            
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
            $this->_type = 'external';
            $this->_external_destination = $link;
            break;
            
        }
        
        return true;
        
    }
    
    public function parseContent($with){
        if(substr($with, 0, 6) == 'image:'){
            $with = '<img src="'.SM_CONTROLLER_DOMAIN.'Resources/Images/'.substr($with, 6).'"';
            if(isset($this->_params['alt'])){
                $with .= ' alt="'.$this->_params['alt'].'" />';
            }else{
                $with .= ' alt="'.$this->getContent(true).'" />';
            }
            return $with;
        }else{
            return $with;
        }
    }
    
    public function getContent($ignore_with=false){
        
        if($this->_params['with'] && !$ignore_with){
            return $this->parseContent($this->_params['with']);
        }else{
            
            switch($this->_type){

                case "page":
                return $this->_page->getTitle();
                break;

                case "metapage":
                return $this->_item->getName();
                break;

                case "image":
                return $this->_image_file;
                break;
                
                case "tag_page":
                return $this->_tag_name;
                break;
                
                case "download":
                return $this->_download->getUrl();
                break;

                case "external":
                return $this->_external_destination;
                break;
            }
            
        }
        
    }
    
    public function getUrl(){
        
        switch($this->_type){
            
            case "page":
            
            if($this->_preview_mode){
                if($this->_page_not_found){
                    return '#';
                }else{
                    return SM_CONTROLLER_DOMAIN.'websitemanager/preview?page_id='.$this->_page->getWebid();
                }
            }else{
                if($this->_page->getIsPublished() == 'TRUE'){
                    if($this->_page_not_found){
                        return '#';
                    }else{
                        if($this->shouldUseId()){
                            return SM_CONTROLLER_DOMAIN.'website/renderPageFromId?page_id='.$this->_page->getWebid();
                        }else{
                            return SM_CONTROLLER_DOMAIN.$this->_page->getDefaultUrl();
                        }
                    }
                }else{
                    return '#';
                }
            }
            
            break;
            
            case "metapage":
            
            // print_r($this->_page);
            
            if($this->_preview_mode){
                if(is_object($this->_page)){
                    return SM_CONTROLLER_DOMAIN.'websitemanager/preview?page_id='.$this->_page->getWebid().'&amp;item_id='.$this->_item->getId();
                }else{
                    return '#';
                }
            }else if($this->shouldUseId()){
                if(is_object($this->_page)){
                    return SM_CONTROLLER_DOMAIN.'website/renderPageFromId?page_id='.$this->_page->getWebid().'&amp;item_id='.$this->_item->getWebid();
                }else{
                    return '#';
                }
            }else{
                if(is_object($this->_page) && is_object($this->_item)){
                    $template_url = SM_CONTROLLER_DOMAIN.$this->_page->getDefaultUrl();
                    $url = str_replace(':id', $this->_item->getId(), $template_url);
                    $url = str_replace(':long_id', $this->_item->getWebid(), $url);
                    $url = str_replace(':name', $this->_item->getSlug(), $url);
                    return $url;
                }else{
                    return '#';
                }
            }
            
            break;
            
            case "image":
            return SM_CONTROLLER_DOMAIN.'Resources/Images/'.$this->_image_file;
            break;
            
            case "tag_page":
            return SM_CONTROLLER_DOMAIN.'tags/'.$this->_tag_name.'.html';
            break;
            
            case "download":
            return SM_CONTROLLER_DOMAIN.'download/'.$this->_download->getUrl().'?key='.$this->_download->getWebid();
            break;
            
            case "external":
            return $this->_external_destination;
            break;
        }
        
    }
    
    protected function parseLinkIdentifier($page_identifier){
        
        if(preg_match('/((name|id|webid)=)?([\w-]+)/i', $page_identifier, $matches)){
            
            // print_r($matches);
            if(strlen($matches[2]) && in_array($matches[2], array('name', 'id', 'webid'))){
                $index_key = $matches[2];
            }else{
                $index_key = 'name';
            }
            
            $index_value = $matches[3];
            return array('key'=>$index_key, 'value'=>$index_value);
            
        }else{
            return false;
        }
    }
    
    public function getType(){
        return $this->_type;
    }
    
    public function getMarkup(){
        
        /* if($this->_error && !$this->_page_not_found){
            if($this->_draft_mode){
                return '<strong>'.$this->_error_message.'</strong>';
            }else{
                return '<!--link failed: '.$this->_error_message.'-->';
            }
        }else{
            
            // put html together
            if($this->shouldOmitAnchorTag()){
                
                $markup = '<!--cold link: would have been linked to:'.$this->getUrl().'-->'.$this->getContent();
                
            }else{
                
                // which of the params should be converted to html attributes
                // Note the target and href attributes are dealt with separately below
                $allowed_attributes = array('title', 'id', 'name', 'style', 'onclick', 'ondblclick', 'onmouseover', 'onmouseout', 'class');
                
                $markup = '<a href="'.$this->getUrl().'"';
                
                foreach($this->_params as $attribute=>$value){
                    if(in_array($attribute, $allowed_attributes)){
                        $markup .=' '.$attribute.'="'.$value.'"';
                    }
                }
                
                if($this->_preview_mode && in_array($this->_type, array('page', 'metapage')) && SmartestStringHelper::toRealBool($this->_page->getIsPublished())){
                    $markup .=' target="_top"';
                }else{
                    if(isset($this->_params['target'])){
                        $markup .=' target="'.$this->_params['target'].'"';
                    }
                }
                
                $markup .='>'.$this->getContent().'</a>';
                
            }
            
        }
        
        return $markup; */
        
    }
    
    public function shouldOmitAnchorTag(){
        return $this->isInternalPage() && $this->shouldGoCold() && is_object($this->_host_page) && $this->_page->getId() == $this->_host_page->getId();
    }
    
    public function shouldGoCold(){
        return (isset($this->_params['goCold']) && $this->_params['goCold'] && $this->_params['goCold'] != 'false');
    }
    
    public function shouldUseId(){
        return (isset($this->_params['byId']) && $this->_params['byId'] && $this->_params['byId'] != 'false');
    }
    
    public function isInternalPage(){
        return in_array($this->_type, array('page', 'metapage'));
    }
    
    public function getErrorMessage(){
        return $this->_error_message;
    }
    
}