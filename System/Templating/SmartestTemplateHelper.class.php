<?php

// include_once 'Managers/SetsManager.class.php';

class SmartestTemplateHelper{

	private $database;
	private $get = array();
	private $errorStack;
	private $setsManager;
	// private $controller;
	
	function __construct(){
		$this->database    = SmartestPersistentObject::get('db:main');
		// $this->get         = SmartestPersistentObject::get('controller')->getRequestVariables();
		$this->errorStack  = SmartestPersistentObject::get('errors:stack');
		// $this->setsManager = new SetsManager();
	}
	
	public function getDataHolder(){
	    return SmartestPersistentObject::get('centralDataHolder');
	}
	
	public function getAssetClass($assetclass, $params){
	
		if(isset($params['instance']) && !empty($params['instance'])){
			$instance_name = $params['instance'];
		}else{
			$instance_name = 'default';
		}
		
		if(is_file(SM_ROOT_DIR."System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp") && SM_OPTIONS_CACHE_ASSETCLASSES && SM_CONTROLLER_METHOD != "renderEditableDraftPage"){

			$html = @file_get_contents(SM_ROOT_DIR."System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp");
		
		}else{
		
			if(SM_CONTROLLER_METHOD == "renderPageFromUrl"){
				if(strlen(SM_CONTROLLER_URL) > 0){
					$pageField = "page_id";
					$value = $this->getPageIdFromUrl(SM_CONTROLLER_URL);
				}else{
					$pageField = "page_id";
					$value = SM_PAGE_ID;
				}
			}else{
				$pageField = "page_webid";
				$value = $this->get['page_id'];
			}
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage" || SM_CONTROLLER_METHOD == "renderDraftPage"){
				$assetIdentifierField = "assetidentifier_draft_asset_id";
			}else{
				$assetIdentifierField = "assetidentifier_live_asset_id";
			}
		
			$sql = "SELECT DISTINCT page_webid, assettype_code, assettype_label, Assets.*, AssetIdentifiers.* 
			FROM AssetTypes, Assets, AssetIdentifiers, AssetClasses, TextFragments, Pages 
			WHERE $pageField='$value' 
			AND page_id = assetidentifier_page_id 
			AND asset_id = $assetIdentifierField
			AND assetclass_id = assetidentifier_assetclass_id 
			AND assetclass_name = '$assetclass' 
			AND assetclass_assettype_id = assettype_id";
			
			$result = $this->database->queryToArray($sql);
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$edit_link = "<a title=\"Click to edit definition for placeholder: $assetclass (".$result[0]["assettype_code"].")\" href=\"".SM_CONTROLLER_DOMAIN."websiteManager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			}else{
				$edit_link = "<!--edit link-->";
			}
			
			if(count($result) > 0){
				
				$asset = $result[0];
				
				if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
					// $edit_link = "&nbsp;<a title=\"Click to edit definition for placeholder: $assetclass (".$result[0]["assettype_code"].")\" href=\"".SM_CONTROLLER_DOMAIN."websiteManager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" border=\"0\" /></a>";
					if(in_array($asset['assettype_code'], array("HTML", "LINE", "TEXT"))){
						$edit_link .= "&nbsp;<a href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?assettype_code=".$asset['assettype_code']."&asset_id=".$asset['asset_id']."\" title=\"Edit ".$asset['assettype_label']." placeholder\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!--Edit this text--></a>";
					}else{
						$edit_link .= "";
					}
				}
				
				$text_fragment = $this->getAssetText($asset['asset_fragment_id']);
				if($asset['assettype_code']!="TMPL"){
				switch($asset['assettype_code']){
					case "HTML":
					
						if($params['div_class']){
							$html = "\n\n<div class=\"".$params["div_class"]."\">";
						}
					
						$html .= "\n<!--Begin Smartest HTML Include-->\n".utf8_encode(stripslashes($text_fragment))."$edit_link";
						
						if(!$params['div_class']){
							$html .= "<br />";
						}
						
						"\n<!--End Smartest HTML Include-->";
						
						if($params['div_class']){
							$html .= "\n</div>";
						}
						
						
						break;
					case "LINE":
					case "TEXT":
						$html = "<div id=\"smartestAsset_{$asset['asset_webid']}\" style=\"display:inline\">".utf8_encode(stripslashes($text_fragment))." $edit_link</div>\n";
						break;
					case "CSS":
						if($text_fragment){
							$html = "<style type=\"text/css\">\n\n/**** Begin Asset CSS ****/\n\n$text_fragment\n\n</style>\n";
						}else{
							$html = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".SM_CONTROLLER_DOMAIN."/Resources/Stylesheets/{$asset['asset_url']}\" />\n";
						}
						break;
					case "JSCR":
						if($text_fragment){
							$html = "<script language=\"javascript\">\n\n//// Begin Asset Javascript /////\n\n$text_fragment\n\n</script>\n";
						}else{
							$html = "<script language=\"javascript\" src=\"".SM_CONTROLLER_DOMAIN."/Resources/Javascript/{$asset['asset_url']}\"></script>\n";
						}
						break;
					case "JPEG":
					case "GIF":
					case "PNG":
						$html = "<img src=\"".SM_CONTROLLER_DOMAIN."Resources/Images/{$asset['asset_url']}\" id=\"smartestAsset_{$asset['asset_webid']}\" alt=\"\" border=\"0\" />$edit_link";

						break;
					case "SWF":
						$html = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\" width=\"550\" height=\"400\" id=\"smartestAsset_{$asset['asset_webid']}\">
<param name=\"movie\" value=\"".SM_CONTROLLER_DOMAIN."/Resources/Assets/{$asset['asset_url']}\" />
<param name=\"quality\" value=\"high\" />
<param name=\"bgcolor\" value=\"#FFFFFF\" />
<embed src=\"".SM_CONTROLLER_DOMAIN."/Resources/Assets/{$asset['asset_url']}\" quality=\"high\" bgcolor=\"#FFFFFF\" width=\"550\" height=\"400\" name=\"myMovieName\" align=\"\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
</object>$edit_link\n";
						break;
				}
			
				if(strlen($html) > 0 && SM_CONTROLLER_METHOD != "renderEditableDraftPage" && SM_CONTROLLER_METHOD != "renderDraftPage"){
					@file_put_contents(SM_ROOT_DIR."System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp", $html);
				}
			}
		}
	}
	    return array("html"=>$html, "type"=>$asset['assettype_code'], "file"=>$asset['asset_url']);
        
	}
	
	public function getTemplateAssetClass($assetclass, $params){
		
		// echo SM_CONTROLLER_METHOD;
		
		if(isset($params['instance']) && !empty($params['instance'])){
			$instance_name = $params['instance'];
		}else{
			$instance_name = 'default';
		}
		
		if(is_file(SM_ROOT_DIR."System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp") && SM_OPTIONS_CACHE_ASSETCLASSES && SM_CONTROLLER_METHOD != "renderEditableDraftPage" && SM_CONTROLLER_METHOD != "renderDraftPage"){
		
			$html = @file_get_contents(SM_ROOT_DIR."System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp");
		
		}else{
		
			if(SM_CONTROLLER_METHOD == "renderPageFromUrl"){
				if(strlen(SM_CONTROLLER_URL) > 0){
					$pageField = "page_id";
					$value = $this->getPageIdFromUrl(SM_CONTROLLER_URL);
				}else{
					$pageField = "page_id";
					$value = SM_PAGE_ID;
				}
			}else{
				$pageField = "page_webid";
				$value = $this->get['page_id'];
			}
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$assetIdentifierField = "assetidentifier_draft_asset_id";
			}else{
				$assetIdentifierField = "assetidentifier_live_asset_id";
			}
		
			$sql = "SELECT DISTINCT page_webid, assettype_code, Assets.*, AssetIdentifiers.* 
			FROM AssetTypes, Assets, AssetIdentifiers, AssetClasses, TextFragments, Pages 
			WHERE $pageField='$value' 
			AND page_id = assetidentifier_page_id 
			AND asset_id = $assetIdentifierField
			AND assetclass_id = assetidentifier_assetclass_id 
			AND assetclass_name = '$assetclass' 
			AND assetclass_assettype_id = assettype_id";
			
			$result = $this->database->queryToArray($sql);
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$edit_link = "&nbsp;<a title=\"Click to edit definition for container: $assetclass\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this template--></a>";
			}else{
				$edit_link = "<!--edit link-->";
			}
			
			if(count($result) > 0){
				
				$asset = $result[0];
				
				if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				
					$edit_link .= "&nbsp;<a title=\"Click to edit the template: ".$asset['asset_url']."\" href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?assettype_code=TMPL&asset_id=".$asset['asset_id']."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this template--></a>";
				
				}
				
				$text_fragment = $this->getAssetText($asset['asset_fragment_id']);
				if($asset['assettype_code']=="TMPL"){
				
					if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
						$html = "\n".$edit_link;
					}else{
						$html = "<!--template: ".$asset['asset_url']."-->";
					}	
				}
			}
		}
	    
	    return array("html"=>$html, "type"=>$asset['assettype_code'], "file"=>$asset['asset_url']);
	
	}
	
	/////////////////////////////////getLists!!!

	public function getList($listname){
		
		  /* if(SM_CONTROLLER_METHOD == "renderPageFromUrl"){
				if(strlen(SM_CONTROLLER_URL) > 0){
					$pageField = "page_id";
					$value = $this->getPageIdFromUrl(SM_CONTROLLER_URL);
				}else{
					$pageField = "page_id";
					$value = SM_PAGE_ID;
				}
			}else{
				$pageField = "page_webid";
				$value = $this->get['page_id'];
			}
			
			$page_webid = $this->get['page_id'];
			$page_id = $this->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
			$sql="SELECT * FROM Lists WHERE list_name = '$listname' AND list_page_id = '$page_id' ";
			$result = $this->database->queryToArray($sql);
			
			foreach($result as $list){
			
				$set_id=$list['list_draft_set_id'];
				$tpl_name=$list['list_draft_template_file'];
				$header=$list['list_draft_header_template'];
				$footer=$list['list_draft_footer_template'];
				$sql = "SELECT * FROM Sets, ItemClasses WHERE Sets.set_id='$set_id' AND Sets.set_itemclass_id=ItemClasses.itemclass_id";
				$set = $this->database->queryToArray($sql);
				$set =$set[0];
				$set_type = $set['set_type'];
				$model_id = $set['itemclass_id'];
			
				if(is_numeric($model_id)){
					$items=$this->setsManager->getDataSetItemProperties($set_id,$set_type,$model_id);
					$count=count($items);	
				}
			
			}
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$edit_link = "&nbsp;<a title=\"Click to edit definition for set: ".$set['set_name']."\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineLists?list_id=".$listname."&page_id=".$value."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this template--></a>";
			}else{
				$edit_link = "<!--edit link-->";
			}
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				
					$edit_link .= "&nbsp;<a title=\"Click to edit the set: ".$set['set_name']."\" href=\"".SM_CONTROLLER_DOMAIN."sets/editSet?set_id=".$set_id."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this set--></a>";
				
			}
				
			$html=$edit_link;
			
			return array("items"=>$items,"html"=>$html,"tpl_name"=>$tpl_name,"header"=>$header,"footer"=>$footer); */

	}

//////////////////////////till here getLists!!!
	
	function getItemDetails($set_name){

		/* $sql = "SELECT * FROM Sets, ItemClasses WHERE Sets.set_name='$set_name' AND Sets.set_itemclass_id=ItemClasses.itemclass_id";
		$set = $this->database->queryToArray($sql);
		$set =$set[0];
		$set_id = $set['set_id'];
		$set_type = $set['set_type'];
		$model_id = $set['itemclass_id'];
		$items = $this->setsManager->getDataSetItemProperties($set_id,$set_type,$model_id);
		return $items; */
	}
	
	function getAssetText($textfragment_id){
		$sql = "SELECT textfragment_content FROM TextFragments WHERE textfragment_id='$textfragment_id'";
		$result = $this->database->queryToArray($sql);
		return $result[0]['textfragment_content'];
	}
	
	function getAsset($params){
		if(@$params['name']){
			$sql = "SELECT DISTINCT * FROM Assets, AssetTypes WHERE asset_assettype_id=assettype_id AND asset_stringid='{$params['name']}'";
		}
	}
	
	function getLink($params){
		
		$startHtml = '<a href="';
		$closingTag = '</a>';
		$html = null;
		$reservedParamNames = array("to", "with", "byId", "goCold");
		$linkable_assettypes = array("LINE", "TEXT", "JPEG", "GIF", "PNG");
		
		if(strtolower(substr($params['to'], 0, 5)) == "page:"){
		
			// link to internal page
			$_link_type = "i";
			global $site;
			$page_name = substr($params['to'], 5);
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$_link_url = "websitemanager/preview?page_id=".$this->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
			}else{
				if(@$params['byId'] == "true"){
					$_link_url = "website/renderPageFromId?page_id=".$this->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
				}else{
					// $_link_url = $this->database->specificQuery("page_url", "page_name", $page_name, "Pages");
					$page_id = $this->database->specificQuery("page_id", "page_name", $page_name, "Pages");
					$_link_url = $this->getPageUrlFromId($page_id);
				}
			}
			
			$_link_title = $this->database->specificQuery("page_title", "page_name", $page_name, "Pages");
			$html = $startHtml.SM_CONTROLLER_DOMAIN.$_link_url.'"';
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$html .= " target=\"_top\"";
			}
			
		/* }else if(strtolower(substr($params['to'], 0, 7)) == "action:"){
		
			
			$_link_type = "a";
			
			// looks for input in the form of: module/method/var=value/foo=bar
			
			if(preg_match('/^([\w_]+)(\/([\w_])+(\?((([\w_]+)=([\w_]+))*))?)?$/', substr($params['to'], 7), $matches)){
			
				$_link_url = $this->controller->getUrlFor($matches[1], $matches[3], $this->parseQueryString($matches[4]));
				$html = $startHtml.$_link_url.'"';
				
			}else{
			
				$_link_url = $this->controller->getUrlFor($this->controller->getModuleName(), $this->controller->getMethodName());
				$html = $startHtml.$_link_url.'"';
			
			}
			
		}else if(strtolower(substr($params['to'], 0, 5)) == "feed:"){
			
			// link to internal page
			$_link_type = "f";
			global $site;
			$feed_name = substr($params['to'], 5);
			
			// smartest/$schema/$model/$set/$export.xml
			
			if($feed_exists = $site->database->specificQuery("page_webid", "page_name", $page_name, "Pages")){
				$_link_url = SM_CONTROLLER_DOMAIN."smartest/";
				$pairing_id = $site->database->specificQuery("dataexport_pairing_id", "dataexport_name", $feed_name, "DataExports");
				$schema_id = $site->database->specificQuery("paring_schema_id", "paring_id", $pairing_id, "Pairings");
				// $_link_url .= $schema."/";
				$model_id = $site->database->specificQuery("paring_model_id", "paring_id", $pairing_id, "Pairings");
				$model = $site->database->specificQuery("itemclass_varname", "itemclass_id", $model_id, "ItemClasses");
				$_link_url .= $model."/";
				$set_id = $site->database->specificQuery("dataexport_set_id", "dataexport_name", $feed_name, "DataExports");
				// $_link_url .= $set."/";
				$_link_url .= $feed_name.".xml";
			}else{
				$_link_url = "";
			}
			
			$_link_title = $site->database->specificQuery("page_title", "page_name", $page_name, "Pages");
			$html = $startHtml.SM_CONTROLLER_DOMAIN.$_link_url.'"';
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$html .= " target=\"_top\"";
			} */
			
		}else{
			
			$_link_type = "e";
			// link to any other address
			if(strtolower(substr($params['to'], 0, 7)) == "http://" || strtolower(substr($params['to'], 0, 8)) == "https://"){
				$html = $startHtml.$params['to'].'"';
			}else{
				$html = $startHtml.$params['to'].'"';
			}
			
		}
		
		if(strtolower(substr($params['with'], 0, 11)) == "assetclass:"){
			
			// an assetclass is being linked
			$assetclass_name = substr($params['with'], 11);
			$assetclass = $this->getAssetClass($assetclass_name);
			if(in_array($assetclass['type'], $linkable_assettypes)){
				$_link_contents = $assetclass["html"];
			}else{
				if($_link_type == "i"){
					// get name from db as fallback
					$_link_contents = $_link_title;
				}else{
					// is external link, so no fallback. output "link".
					$_link_contents = "link";
				}
			}
			
		}else if(strtolower(substr($params['with'], 0, 6)) == "image:"){
			
			// an image is being linked
			$image_file_name = substr($params['with'], 6);
			$image = $this->getImage(array("file"=>$image_file_name));
			
			$_link_contents = $image;
			
		}else{
			
			// fill the link with regular text
			if($_link_type == "i"){
				if(@$params['with']){
					// specified
					$_link_contents = $params['with'];
				}else{
					// unspecified
					$_link_contents = $_link_title;
				}
			}else{
				if(@$params['with']){
					// specified
					$_link_contents = $params['with'];
				}else{
					// unspecified
					$_link_contents = "link";
				}
			}
		} 
		
		
		if(@$html){
		
			// add any other attributes
			foreach($params as $attribute=>$value){
				if(!in_array($attribute, $reservedParamNames)){
					$html .=' '.$attribute.'="'.$value.'"';
				}
			}
			
			// put text in link and close tag
			$html .= '>'.$_link_contents.'</a>';
		}
		
		// optionally do not include <a> tags if this is an internal link to the current page
		
		if($_link_type == "i"){
			
			$page_webid = $this->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
			
			if(@$params['goCold'] == 'true' && $page_webid == SM_PAGE_WEBID){
				return $_link_contents;
			}else{
				return $html;
			}
			
		}else{
			return $html;
		}
	}
	
	function getUrl($params){
		
		$startHtml = '';
		$closingTag = '';
		$html = null;
		$reservedParamNames = array("to", "with", "byId", "goCold");
		$linkable_assettypes = array("LINE", "TEXT", "JPEG", "GIF", "PNG");
		
		// if(strtolower(substr($params['to'], 0, 5)) == "page:"){
		
			// link to internal page
			$_link_type = "i";
			global $site;
			$page_name = substr($params['for'], 5);
			
			if(@$params['useId'] == "true"){
				$_link_url = "website/renderPageFromId?page_id=".$this->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
			}else{
				// $_link_url = $this->database->specificQuery("pageurl_url", "pageurl_page_id", SM_PAGE_ID, "PageUrls");
				$_link_url = $this->getPageUrlFromId(SM_PAGE_ID);
			}
			
			// $_link_title = $site->database->specificQuery("page_title", "page_name", $page_name, "Pages");
			// $html = $startHtml.SM_CONTROLLER_DOMAIN.$_link_url.'"';
			
		// } 
		
		return $_link_url;
		
	}
	
	
	function getImage($params){
		
		$startHtml = '<img src="';
		$html = null;
		
		if(isset($params["file"]) && strlen($params["file"]) > 5){
		
			$cache_file_name = SM_ROOT_DIR."System/Cache/SmartestEngine/"."img_".md5($params["file"])."-".SM_PAGE_ID.".tmp";
			
			if(!is_file($cache_file_name) || !SM_OPTIONS_CACHE_IMAGETAGS){
			
				if(strtolower(substr($params['file'], 0, 7)) == "http://"){
			
					$html .=$startHtml.$params["file"];
				
				}else if(strtolower(substr($params['file'], 0, 9)) == "property:"){
			
					$propertyName = substr($params['file'], 9);
					$html .=$startHtml.@$params['prepend'].$this->getItemPropertyValue(array("name"=>$propertyName));
				
				}else if(strtolower(substr($params['file'], 0, 6)) == "asset:"){
			
					// $html .=$startHtml.$params["file"];
					$asset_name = substr($params['file'], 6);
					$html .=$this->getAsset(array("name"=>$asset_name));
				
				}else{
					if(@SM_OPTIONS_IMAGES_LOCAL){
					
						$local_img_dir = SM_SYSTEM_IMAGES_DIR;
						if($local_img_dir{0} == "/"){
							$html .= $startHtml.SM_SYSTEM_IMAGES_DIR.$params["file"];
						}else{
							$html .= $startHtml.SM_CONTROLLER_DOMAIN.SM_SYSTEM_IMAGES_DIR.$params["file"];
						}
					
					}else{
				
						$html .= $startHtml.SM_CONTROLLER_DOMAIN.SM_SYSTEM_IMAGES_DIR.$params["file"];
					
					}
				}
				
				$alt = (isset($params["alt"])) ? $params["alt"] : "";
				$border = (isset($params["border"])) ? $params["border"] : "0";
				
				$html .="\" alt=\"$alt\" border=\"$border\" />";
				file_put_contents($cache_file_name, $html);
			
			}else{
				
				$html = file_get_contents($cache_file_name);
				
			}
		
		}
		
		return $html;
	}
	
	function getStylesheet($params){
		
		$startHtml = "<link rel=\"stylesheet\" href=\"";
		$html = null;
		
		if(isset($params["file"]) && strlen($params["file"]) > 5){
			
			if(strtolower(substr($params['file'], 0, 7)) == "http://"){
			
				$html .= $startHtml.$params["file"];
				
			}else if(strtolower(substr($params['file'], 0, 8)) == "https://"){
			
				$html .= $startHtml.$params["file"];
			
			}else{
				
				$html .= $startHtml.SM_CONTROLLER_DOMAIN."Resources/Stylesheets/".$params["file"];
				
			}
			
			$html .= "\"";
			
			if(isset($params["media"])){
				
				$html .= " media=\"".$params["media"]."\"";
				
			}
			
			$html .= " />";
		
		}
		
		return $html;
	}
	
	function getImagePath($params){
		
		$startHtml = '';
		$html = null;
		
		if(isset($params["file"]) && strlen($params["file"]) > 5){
		
			
				if(strtolower(substr($params['file'], 0, 7)) == "http://"){
			
					$html .=$startHtml.$params["file"];
				
				}else if(strtolower(substr($params['file'], 0, 9)) == "property:"){
			
					$propertyName = substr($params['file'], 9);
					$html .=$startHtml.@$params['prepend'].$this->getItemPropertyValue(array("name"=>$propertyName));
				
				}else if(strtolower(substr($params['file'], 0, 6)) == "asset:"){
			
					$asset_name = substr($params['file'], 6);
					$html .=$this->getAsset(array("name"=>$asset_name));
				
				}else{
					if(@SM_OPTIONS_IMAGES_LOCAL){
					
						$local_img_dir = SM_SYSTEM_IMAGES_DIR;
						if($local_img_dir{0} == "/"){
							$html .= $startHtml.SM_SYSTEM_IMAGES_DIR.$params["file"];
						}else{
							$html .= $startHtml."/".SM_SYSTEM_IMAGES_DIR.$params["file"];
						}
					
					}else{
				
						$html .= $startHtml.SM_CONTROLLER_DOMAIN.SM_SYSTEM_IMAGES_DIR.$params["file"];
					
					}
				}
				
				$alt = (isset($params["alt"])) ? $params["alt"] : "";
				$border = (isset($params["border"])) ? $params["border"] : "0";
		
		}
		
		return $html;
	}
		
	function getItemPropertyValue($params){
		if(@$params["name"]){
			
		}
	}
	
	function getPageIdFromUrl($url){
		$sql = "SELECT pageurl_page_id from PageUrls WHERE pageurl_url='$url'";
		$result = $this->database->queryToArray($sql);
		return $result[0]['pageurl_page_id'];
	}
	
	function getPageUrlFromId($page_id){
		$sql = "SELECT pageurl_url from PageUrls WHERE pageurl_page_id='$page_id'";
		$result = $this->database->queryToArray($sql);
		return $result[0]['pageurl_url'];
	}
	
	function error($message="[unspecified error]"){
		if($this->errorStack instanceof ErrorStack){
			$this->errorStack->recordError($message, 101);
		}
	}

}

?>