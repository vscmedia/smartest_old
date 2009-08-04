<?php

class SmartestCmsLink extends SmartestHelper{
    
    protected $_host_page;
    protected $_error_message;
    protected $_has_error = false;
    protected $_draft_mode = false;
    protected $_preview_mode = false;
    protected $database;
    protected $_destination_properties;
    protected $_destination;
    protected $_markup_attributes;
    protected $_render_data;
    
    const PAGE = 1;
    const METAPAGE = 2;
    const IMAGE = 4;
    const DOWNLOAD = 8;
    const TAG = 16;
    const EXTERNAL = 256;
    
    const ERROR_PAGE_NOT_FOUND = 1;
    const ERROR_ITEM_NOT_FOUND = 2;
    const ERROR_INVALID_DESTINATION = 4;
    
    public function __construct($destination_properties, $markup_attributes){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
        if(is_array($destination_properties)){
            $ph = new SmartestParameterHolder("Link destination properties: ".$destination_properties['to']);
            $ph->loadArray($destination_properties);
            $this->_destination_properties = $ph;
        }else if($destination_properties instanceof SmartestParameterHolder){
            $this->_destination_properties = $destination_properties;
        }
        
        $this->_markup_attributes = $this->getSeparatedAttributes($markup_attributes)->getParameter('html');
        $this->_render_data = $this->getSeparatedAttributes($markup_attributes)->getParameter('other');
        
        // Give any HTML attributes passed by SmartestCmsLinkHelper a chance to be included
        $extra_markup_attributes = $this->getSeparatedAttributes($this->_destination_properties)->getParameter('html');
        $this->_markup_attributes->absorb($extra_markup_attributes);
        
        $this->setTypeFromNameSpace($this->_destination_properties->getParameter('namespace'));
        
        $this->_loadDestination();
        
    }
    
    ///// NEW API FUNCTIONS /////
    
    public function setTypeFromNamespace($ns){
        
        $ns = strtolower($ns);
        
        if(strlen($this->_destination_properties->getParameter('destination'))){
        
            $du = new SmartestDataUtility;
            $model_names = array_keys($du->getModelNamesLowercase());
        
            if(in_array($ns, array('page', 'metapage', 'item', 'image', 'img', 'download', 'dl', 'tag', 'tag', 'tag_page'))){
            
                switch($ns){
                
                    case "page":
                    $this->setType(SM_LINK_TYPE_PAGE);
                    break;
                
                    case "metapage":
                    case "item":
                    $this->setType(SM_LINK_TYPE_METAPAGE);
                    $this->_destination_properties->setParameter('format', SM_LINK_FORMAT_AUTO);
                    break;
                
                    case "image":
                    case "img":
                    $this->setType(SM_LINK_TYPE_IMAGE);
                    break;
                
                    case "download":
                    case "dl":
                    $this->setType(SM_LINK_TYPE_DOWNLOAD);
                    break;
                
                    case "tag":
                    case "tag_page":
                    $this->setType(SM_LINK_TYPE_TAG);
                    break;
                }
                
                $this->setNamespace($ns);
            
                return true;
            
            }else if(in_array($ns, $model_names)){

                $this->setType(SM_LINK_TYPE_METAPAGE);
                $this->setNamespace($ns);
                $this->_destination_properties->setParameter('format', SM_LINK_FORMAT_USER);
            
                return true;
            
            }else{
            
                if(substr($this->_destination_properties->getParameter('destination'), 0, 4) == 'http'){
                    $this->setType(SM_LINK_TYPE_EXTERNAL);
                    $this->_destination_properties->setParameter('format', SM_LINK_FORMAT_URL);
                    return true;
                }
            
            }
        
        }else{
            $this->error("Link could not be built. No destination given.");
            $this->setType(SM_LINK_TYPE_DUD);
            return false;
        }
        
    }
    
    public function getSeparatedAttributes($markup_attributes){
        
        if(is_array($markup_attributes)){
            $data = $markup_attributes;
        }else if($markup_attributes instanceof SmartestParameterHolder){
            $data = $markup_attributes->getParameters();
        }
        
        $allowed_attributes = array('title', 'id', 'name', 'style', 'onclick', 'ondblclick', 'onmouseover', 'onmouseout', 'class', 'target');
        $html_attributes_array = array();
        $other_attributes_array = array();
        
        foreach($data as $name => $value){
            
            if(in_array($name, $allowed_attributes)){
                $html_attributes_array[$name] = $value;
            }else{
                $other_attributes_array[$name] = $value;
            }
            
        }
        
        $html_attributes = new SmartestParameterHolder("Link HTML Attributes");
        $html_attributes->loadArray($html_attributes_array);
        
        $other_attributes = new SmartestParameterHolder("Link Non-HTML Attributes");
        $other_attributes->loadArray($other_attributes_array);
        
        $ph = new SmartestParameterHolder("Separated Attributes");
        $ph->setParameter('html', $html_attributes);
        $ph->setParameter('other', $other_attributes);
        
        return $ph;
        
    }
    
    public function getMarkupAttributes(){
        return $this->_markup_attributes;
    }
    
    public function getMarkupAttribute($attribute_name){
        return $this->_markup_attributes->getParameter($attribute_name);
    }
    
    public function getDestinationProperties(){
        return $this->_destination_properties;
    }
    
    public function getDestinationProperty($property_name){
        return $this->_destination_properties->getParameter($property_name);
    }
    
    public function getRenderData(){
        return $this->_render_data;
    }
    
    public function getScope(){
        return $this->_destination_properties->getParameter('scope');
    }
    
    public function setType($type){
        return $this->_destination_properties->setParameter('type', (int) $type);
    }
    
    public function getType(){
        return $this->_destination_properties->getParameter('type');
    }
    
    public function setNamespace($ns){
        return $this->_destination_properties->setParameter('namespace', $ns);
    }
    
    public function getNamespace(){
        return $this->_destination_properties->getParameter('namespace');
    }
    
    public function setDestinationString($s){
        return $this->_destination_properties->setParameter('destination', $s);
    }
    
    public function getDestinationString(){
         return $this->_destination_properties->getParameter('destination');
    }
    
    protected function _loadDestination(){
        
        if(defined('SM_CMS_PAGE_SITE_ID')){
            $site_id = SM_CMS_PAGE_SITE_ID;
        }else if(SmartestSession::hasData('current_open_project')){
            $site_id = SmartestPersistentObject::get('current_open_project')->getId();
        }
        
        switch($this->getType()){
            
            case SM_LINK_TYPE_PAGE:
            $d = new SmartestPage;
            
            $sql = "SELECT * FROM Pages WHERE page_".$this->_destination_properties->getParameter('page_ref_field_name')."='".$this->_destination_properties->getParameter('page_ref_field_value')."' AND page_site_id='".$site_id."' AND page_type='NORMAL' AND page_deleted != 'TRUE'";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
                $d->hydrate($result[0]);
                $this->_destination = $d;
            }else{
                return $this->error("The requested page was not found. (Link destination: ".$this->_destination_properties->getParameter('destination').')');
            }
            
            break;
            
            case SM_LINK_TYPE_METAPAGE:
            $d = new SmartestItemPage;
            
            if($this->_destination_properties->getParameter('format') == SM_LINK_FORMAT_AUTO){
                
                $sql = "SELECT * FROM Pages WHERE page_".$this->_destination_properties->getParameter('page_ref_field_name')."='".$this->_destination_properties->getParameter('page_ref_field_value')."' AND page_site_id='".$site_id."' AND page_type='ITEMCLASS' AND page_deleted != 'TRUE'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    $d->hydrate($result[0]);
                    
                    if($this->_destination_properties->getParameter('item_ref_field_name') == 'name'){
                        $this->_destination_properties->setParameter('item_ref_field_name', 'slug');
                    }
                    
                    $sql = "SELECT * FROM Items WHERE item_".$this->_destination_properties->getParameter('item_ref_field_name')."='".$this->_destination_properties->getParameter('item_ref_field_value')."' AND item_site_id='".$site_id."' AND item_itemclass_id='{$d->getDatasetId()}' AND item_deleted != '1'";
                    $result = $this->database->queryToArray($sql);
                    
                    if(count($result)){
                        $d->setPrincipalItem(SmartestCmsItem::retrieveByPk($result[0]['item_id']));
                        $this->_destination = $d;
                    }else{
                        return $this->error("The requested item was not found. (Link destination: ".$this->_destination_properties->getParameter('destination').')');
                    }
                
                }else{
                    echo $sql;
                    return $this->error("The requested page was not found. (Link destination: ".$this->_destination_properties->getParameter('destination').')');
                }
            
            }else if($this->_destination_properties->getParameter('format') == SM_LINK_FORMAT_USER){
                
                $du = new SmartestDataUtility;
                $model_names = $du->getModelNamesLowercase();
                $model_id = $model_names[$this->_destination_properties->getParameter('namespace')];
                
                // user-formatted wikipedia style links. start with the item, figure out the metapage, and go from there
                $sql = "SELECT Items.item_id, Items.item_slug, Items.item_webid, Items.item_itemclass_id, Items.item_site_id, Items.item_deleted, ItemClasses.itemclass_varname, ItemClasses.itemclass_name, ItemClasses.itemclass_id FROM Items, ItemClasses WHERE item_".$this->_destination_properties->getParameter('item_ref_field_name')."='".$this->_destination_properties->getParameter('item_ref_field_value')."' AND ItemClasses.itemclass_id='".$model_id."' AND Items.item_itemclass_id=ItemClasses.itemclass_id AND item_site_id='".constant('SM_CMS_PAGE_SITE_ID')."' AND item_deleted != '1'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $item = SmartestCmsItem::retrieveByPk($result[0]['item_id']);
                    
                    $sql = "SELECT * FROM Pages WHERE page_id='".$item->getMetaPageId()."' AND page_site_id='".constant('SM_CMS_PAGE_SITE_ID')."' AND page_type='ITEMCLASS' AND page_deleted != 'TRUE'";
                    $result = $this->database->queryToArray($sql);
                    
                    if(count($result)){
                    
                        $d->hydrate($result[0]);
                        $d->setPrincipalItem($item);
                    
                        $this->_destination = $d;
                    
                    }else{
                        return $this->error("The requested page was not found. (Link destination: ".$this->_destination_properties->getParameter('destination').')');
                    }
                    
                }else{
                    return $this->error("The requested item was not found. (Link destination: ".$this->_destination_properties->getParameter('destination').')');
                }
                
            }
            
            break;
            
            case SM_LINK_TYPE_IMAGE:
            $d = new SmartestAsset;
            $d->hydrateBy('url', $this->_destination_properties->getParameter('filename'));
            $this->_destination = $d;
            break;
            
            case SM_LINK_TYPE_DOWNLOAD:
            $d = new SmartestAsset;
            $d->hydrateBy('url', $this->_destination_properties->getParameter('filename'));
            $this->_destination = $d;
            break;
            
            case SM_LINK_TYPE_TAG:
            $d = new SmartestTag;
            $this->_destination = $d;
            break;
            
        }
        
    }
    
    public function getDestination(){
        return $this->_destination;
    }
    
    public function setHostPage($p){
        $this->_host_page = $p;
    }
    
    public function getHostPage(){
        return $this->_host_page;
    }
    
    public function hasHostPage(){
        return (($this->_host_page instanceof SmartestPage) && is_numeric($this->_host_page->getId()));
    }
    
    public function error($message){
        $this->_has_error = true;
        $this->_error_message = $message;
    }
    
    public function hasError(){
        return $this->_has_error;
    }
    
    ///// END NEW API FUNCTIONS /////
    
    public function shouldOmitAnchorTag($draft_mode=false){
        // return !$this->_preview_mode && ($this->isInternalPage() && $this->shouldGoCold() && is_object($this->_host_page) && $this->_page->getId() == $this->_host_page->getId());
        if(!$this->_destination_properties->getParameter('destination') || $this->_destination_properties->getParameter('destination') == '#'){
            return true;
        }else{
            if($this->getHostPage()){
                if($this->getType() == SM_LINK_TYPE_PAGE){
                    if(($this->_destination->getId() == $this->getHostPage()->getId()) && $this->shouldGoCold()){
                        return true;
                    }else{
                        if($draft_mode || $this->_destination->getIsPublished()){
                            return false;
                        }else{
                            return true;
                        }
                    }
                }else if($this->getType() == SM_LINK_TYPE_METAPAGE){
                    if(($this->_destination->getId() == $this->getHostPage()->getId()) && ($this->_destination->getSimpleItem()->getId() == $this->getHostPage()->getSimpleItem()->getId()) && $this->shouldGoCold()){
                        return true;
                    }else{
                        if($draft_mode || ($this->_destination->getIsPublished() && $this->_destination->getSimpleItem()->getIsPublished())){
                            return false;
                        }else{
                            return true;
                        }
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    
    public function shouldGoCold(){
        return (isset($this->_render_data['goCold']) && !SmartestStringHelper::isFalse($this->_render_data['goCold']));
    }
    
    public function shouldUseId(){
        return (isset($this->_render_data['byId']) && !SmartestStringHelper::isFalse($this->_render_data['byId']));
    }
    
    public function isInternalPage(){
        SmartestLog::getInstance('system')->log('Deprecated function used: SmartestCmsLink::isInternalPage()', SM_LOG_DEBUG);
        return in_array($this->getType(), array(SM_LINK_TYPE_PAGE, SM_LINK_TYPE_METAPAGE));
    }
    
    public function isInternal(){
        return !in_array($this->getType(), array(SM_LINK_TYPE_EXTERNAL));
    }
    
    public function getErrorMessage(){
        return $this->_error_message;
    }
    
    public function getContent($draft_mode=false){
        
        if($this->_render_data->getParameter('with')){
            // if the with="" attribute is specified
            if(substr($this->_render_data->getParameter('with'), 0, 6) == 'image:'){
                // return $this->_destination->__toString();
                $a = new SmartestRenderableAsset;
                $a->hydrateBy('url', substr($this->_render_data->getParameter('with'), 6));
                // print_r($a);
                return $a->render($draft_mode);
            }else{
                return $this->_render_data->getParameter('with');
            }
        }else if($this->_destination_properties->getParameter('text') && ($this->_destination_properties->getParameter('text') != SmartestLinkParser::LINK_TARGET_TITLE)){
            // if the text is given in the link via a pipe (|)
            return $this->_destination_properties->getParameter('text');
        }else{
            // otherwise guess
            
            if($this->getType() == SM_LINK_TYPE_EXTERNAL){
                
                return $this->_destination_properties->getParameter('destination');
                
            }else{
                
                if($this->hasError()){
                    
                    return null;
                    
                }else{
                    
                    switch($this->getType()){

                        case SM_LINK_TYPE_PAGE:
                        return $this->_destination->getTitle();
                        break;

                        case SM_LINK_TYPE_METAPAGE:
                
                        if($this->_destination->getForceStaticTitle() == 1){
                            return $this->_destination->getTitle(true);
                        }else{
                            return $this->_destination->getTitle();
                        }
                
                        break;

                        case SM_LINK_TYPE_IMAGE:
                        return $this->_destination;
                        break;
                
                        case SM_LINK_TYPE_TAG:
                        return $this->_destination->getTitle();
                        break;
                
                        case SM_LINK_TYPE_DOWNLOAD:
                        return $this->_destination->getUrl();
                        break;
                    }
                
                }
            
            }
            
        }
        
    }
    
    public function getUrl($draft_mode=false){
        
        switch($this->getType()){

            case SM_LINK_TYPE_PAGE:
            
            if($draft_mode){
                return SM_CONTROLLER_DOMAIN.'websitemanager/preview?page_id='.$this->_destination->getWebId();
            }else{
                if($this->_destination->getIsPublishedAsBoolean()){
                    return SM_CONTROLLER_DOMAIN.$this->_destination->getDefaultUrl();
                }else{
                    return '#';
                }
            }
            
            break;

            case SM_LINK_TYPE_METAPAGE:
    
            if($draft_mode){
                return SM_CONTROLLER_DOMAIN.'websitemanager/preview?page_id='.$this->_destination->getWebId().'&amp;item_id='.$this->_destination->getPrincipalItem()->getId();
            }else{
                if($this->_destination->getIsPublishedAsBoolean() && $this->_destination->getPrincipalItem()->isPublished()){
                    $template_url = SM_CONTROLLER_DOMAIN.$this->_destination->getDefaultUrl();
                    $url = str_replace(':id', $this->_destination->getPrincipalItem()->getId(), $template_url);
                    $url = str_replace(':long_id', $this->_destination->getPrincipalItem()->getWebid(), $url);
                    $url = str_replace(':name', $this->_destination->getPrincipalItem()->getSlug(), $url);
                    return $url;
                }else{
                    return '#';
                }
            }
    
            break;

            case SM_LINK_TYPE_IMAGE:
            return $this->_destination->getWebUrl();
            break;
    
            case SM_LINK_TYPE_TAG:
            return $this->_destination->getTitle();
            break;
    
            case SM_LINK_TYPE_DOWNLOAD:
            return SM_CONTROLLER_DOMAIN.'download/'.$this->_destination->getUrl().'?key='.$this->_destination->getWebid();
            break;
            
            case SM_LINK_TYPE_EXTERNAL:
            
            // if($this->_destination_properties->getParameter('newwin')){
            //    return "javascript:window.open('".$this->_destination_properties->getParameter('destination')."');";
            //}else{
                return $this->_destination_properties->getParameter('destination');
            // }
            
            break;
            
        }
        
    }
    
    public function render($draft_mode=false, $ama=''){
        
        if($this->getType() == SM_LINK_TYPE_EXTERNAL){
            
            if($draft_mode && !$this->_destination_properties->getParameter('newwin')){
                $this->_markup_attributes->setParameter('target', '_top');
                $this->_markup_attributes->setParameter('onclick', "return confirm('You will be taken to an external page. Continue?')");
            }else{
                if($this->_markup_attributes->getParameter('target') == '_blank' || $this->_markup_attributes->getParameter('target') == '_new'){
                    $this->_destination_properties->setParameter('newwin', true);
                }
            }
            
            if($this->_destination_properties->getParameter('newwin')){
                $this->_markup_attributes->setParameter('target', '_blank');
            }
            
        }
        
        $url = $this->getUrl($draft_mode);
        $contents = $this->getContent();
        
        if($draft_mode && ($this->getType() == SM_LINK_TYPE_PAGE || $this->getType() == SM_LINK_TYPE_METAPAGE) && $url != '#'){
            $this->_markup_attributes->setParameter('target', '_top');
        }
        
        $sm = new SmartyManager('BasicRenderer');
        $r = $sm->initialize($this->getDestinationString());
        $r->setDraftMode($draft_mode);
        
        if(is_array($ama)){
           $additional_markup_attributes = $this->getSeparatedAttributes($ama)->getParameter('html');
           $this->_markup_attributes->loadArray($additional_markup_attributes);
        }
        
        $content = $r->renderLink($this);
	    
	    return $content;
        
    }
    
}