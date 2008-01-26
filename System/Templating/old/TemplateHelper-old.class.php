<?php

class TemplateHelper{

	var $database;
	var $get;
	var $errorStack;

	function TemplateHelper($database, $get){
		$this->database = $database;
		$this->get = $get;
		global $ERRORSTACK;
		$this->errorStack =& $ERRORSTACK;
	}
	
	function getAssetClass($assetclass){
		
		//echo SM_CONTROLLER_METHOD;
		
		if(is_file("System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp") && SM_OPTIONS_CACHE_ASSETCLASSES && SM_CONTROLLER_METHOD != "renderEditableDraftPage"){
		
			$html = @file_get_contents("System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp");
		
		}else{
		
			if(SM_CONTROLLER_METHOD == "renderPageFromUrl"){
				$pageField = "page_url";
				$value = SM_CONTROLLER_URL;
			}else{
				$pageField = "page_webid";
				$value = $this->get['page_id'];
			}
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage" || SM_CONTROLLER_METHOD == "renderDraftPage"){
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
				$edit_link = "&nbsp;<a title=\"Click to edit definition for placeholder: $assetclass (".$result[0]["assettype_code"].")\" href=\"".SM_CONTROLLER_DOMAIN."websiteManager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" border=\"0\" /></a>";
			}else{
				$edit_link = "<!--edit link-->";
			}
			
			if(count($result) > 0){
				
				$asset = $result[0];
				
				if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
					// $edit_link = "&nbsp;<a title=\"Click to edit definition for placeholder: $assetclass (".$result[0]["assettype_code"].")\" href=\"".SM_CONTROLLER_DOMAIN."websiteManager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" border=\"0\" /></a>";
					if(in_array($asset['assettype_code'], array("HTML", "LINE", "TEXT", "CSS", "JSCR"))){
						$edit_link .= "<a href=\"".SM_CONTROLLER_DOMAIN."assets/editAsset?assettype_code=".$asset['assettype_code']."&asset_id=".$asset['asset_id']."\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" border=\"0\" /></a>";
					}
				}
				
				$text_fragment = $this->getAssetText($asset['asset_fragment_id']);
				if($asset['assettype_code']!="TMPL"){
				switch($asset['assettype_code']){
					case "HTML":
						$html = "\n<!--Begin Smartest HTML Include-->\n\n".$text_fragment."$edit_link\n<!--End Smartest HTML Include-->\n";
						break;
					case "LINE":
					case "TEXT":
						$html = "<div id=\"smartestAsset_{$asset['asset_webid']}\" style=\"display:inline\">$text_fragment"."$edit_link</div>\n";
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
			
				if(strlen($html) > 0 && SM_CONTROLLER_METHOD != "renderEditableDraftPage"  && SM_CONTROLLER_METHOD != "renderDraftPage"){
					@file_put_contents("System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp", $html);
				}
			}
		}
	}
	    return array("html"=>$html, "type"=>$asset['assettype_code'], "file"=>$asset['asset_url']);
        
	}
	
	function getTemplateAssetClass($assetclass){
		
		//echo SM_CONTROLLER_METHOD;
		
		if(is_file("System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp") && SM_OPTIONS_CACHE_ASSETCLASSES && SM_CONTROLLER_METHOD != "renderEditableDraftPage" && SM_CONTROLLER_METHOD != "renderDraftPage"){
		
			$html = @file_get_contents("System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".SM_PAGE_ID.".tmp");
		
		}else{
		
			if(SM_CONTROLLER_METHOD == "renderPageFromUrl"){
				$pageField = "page_url";
				$value = SM_CONTROLLER_URL;
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
				$edit_link = "&nbsp;<a title=\"Click to edit definition for container: $assetclass\" href=\"".SM_CONTROLLER_DOMAIN."websiteManager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" border=\"0\" /></a>";
			}else{
				$edit_link = "<!--edit link-->";
			}
			
			if(count($result) > 0){
				
				$asset = $result[0];
				
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

	function getLists($listname){
		
		//echo SM_CONTROLLER_METHOD;
		
		if(is_file("System/Cache/SmartestEngine/"."ac_".md5($listname)."-".SM_PAGE_ID.".tmp") && SM_OPTIONS_CACHE_ASSETCLASSES && SM_CONTROLLER_METHOD != "renderEditableDraftPage"){
		
			$html = @file_get_contents("System/Cache/SmartestEngine/"."ac_".md5($listname)."-".SM_PAGE_ID.".tmp");
		
		}else{
		
			if(SM_CONTROLLER_METHOD == "renderPageFromUrl"){
				$pageField = "page_url";
				$value = SM_CONTROLLER_URL;
			}else{
				$pageField = "page_webid";
				$value = $this->get['page_id'];
			}
			
			/*if(SM_CONTROLLER_METHOD == "renderEditableDraftPage" || SM_CONTROLLER_METHOD == "renderDraftPage"){
				$listField = "assetidentifier_draft_asset_id";
			}else{
				$assetIdentifierField = "assetidentifier_live_asset_id";
			}*/
			$page_webid = $get['page_id'];
			$page_id = $this->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
			$sql="SELECT * FROM Lists WHERE list_name = '$listname' AND list_page_id = '$page_id' ";
			$result = $this->database->queryToArray($sql);
			foreach($result as $list){
// 			print_r($list);
			$set_id=$list['list_draft_set_id'];				
			$set = $this->setsManager->getSet($set_id);
			$set_type = $set['set_type'];
			$model_id = $set['itemclass_id'];
			if(is_numeric($model_id)){
				$items=$this->getDataSetItemProperties($set_id,$set_type,$model_id);
				$count=count($items);	
			}
			}
			return array("items"=>$items);
// 			$sql = "SELECT DISTINCT page_webid, assettype_code, Assets.*, AssetIdentifiers.* 
// 			FROM AssetTypes, Assets, AssetIdentifiers, AssetClasses, TextFragments, Pages 
// 			WHERE $pageField='$value' 
// 			AND page_id = assetidentifier_page_id 
// 			AND asset_id = $assetIdentifierField
// 			AND assetclass_id = assetidentifier_assetclass_id 
// 			AND assetclass_name = '$assetclass' 
// 			AND assetclass_assettype_id = assettype_id";
// 			
// 			$result = $this->database->queryToArray($sql);
			
			if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				$edit_link = "&nbsp;<a title=\"Click to edit definition for placeholder: $assetclass (".$result[0]["assettype_code"].")\" href=\"".SM_CONTROLLER_DOMAIN."websiteManager/defineAssetClass?assetclass_id=".$assetclass."&page_id=".$value."\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/page_white_edit.png\" alt=\"edit\" border=\"0\" /></a>";
			}else{
				$edit_link = "<!--edit link-->";
			}
			
	}	
// 	    return $html;
        
	}

/////////////////////////////////till here getLists!!!


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
				$_link_url = "website/renderEditableDraftPage?page_id=".$site->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
			}else{
				if(@$params['byId'] == "true"){
					$_link_url = "website/renderPageFromId?page_id=".$site->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
				}else{
					$_link_url = $site->database->specificQuery("page_url", "page_name", $page_name, "Pages");
				}
			}
			
			$_link_title = $site->database->specificQuery("page_title", "page_name", $page_name, "Pages");
			$html = $startHtml.SM_CONTROLLER_DOMAIN.$_link_url.'"';
			
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
			
			$page_webid = $site->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
			
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
				$_link_url = "website/renderPageFromId?page_id=".$site->database->specificQuery("page_webid", "page_name", $page_name, "Pages");
			}else{
				$_link_url = $site->database->specificQuery("page_url", "page_name", $page_name, "Pages");
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
		
			$cache_file_name = "System/Cache/SmartestEngine/"."img_".md5($params["file"])."-".SM_PAGE_ID.".tmp";
			
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
							$html .= $startHtml."/".SM_SYSTEM_IMAGES_DIR.$params["file"];
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
	
	function getImagePath($params){
		
		$startHtml = '';
		$html = null;
		
		if(isset($params["file"]) && strlen($params["file"]) > 5){
		
			// $cache_file_name = "System/Cache/SmartestEngine/"."img_".md5($params["file"])."-".SM_PAGE_ID.".temp";
			
			// if(!is_file($cache_file_name) || !SM_OPTIONS_CACHE_IMAGETAGS){
			
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
							$html .= $startHtml."/".SM_SYSTEM_IMAGES_DIR.$params["file"];
						}
					
					}else{
				
						$html .= $startHtml.SM_CONTROLLER_DOMAIN.SM_SYSTEM_IMAGES_DIR.$params["file"];
					
					}
				}
				
				$alt = (isset($params["alt"])) ? $params["alt"] : "";
				$border = (isset($params["border"])) ? $params["border"] : "0";
				
				// $html .="\" alt=\"$alt\" border=\"$border\" />";
				// file_put_contents($cache_file_name, $html);
			
			// }else{
				
				// $html = file_get_contents($cache_file_name);
				
			// }
		
		}
		
		return $html;
	}
	
	
	function getItemPropertyValue($params){
		if(@$params["name"]){
			
		}
	}
	
	function error($message="[unspecified error]"){
		if($this->errorStack instanceof ErrorStack){
			$this->errorStack->recordError($message, 101);
		}
	}
	function getDataSetItemProperties($set_id,$set_type,$model_id){
	$result=$this->previewSet($set_id,$set_type,$model_id); 
	foreach($result as $key=>$value){
	$item_id=$value['item_id'];
	$class_id=$value['item_itemclass_id'];
	$itemspropertyvalues = $this->getItemPropertyValues($item_id,$class_id);
		$setitemproperties[$key]['item_id']=$item_id;
		$setitemproperties[$key]['item_name']=$value['item_name'];;
		$setitemproperties[$key]['property_details']=$itemspropertyvalues;
	}
        return $setitemproperties;
	}
	function getItemPropertyValues($item_id,$class_id){
	
		$getProperties = $this->getItemClassProperties($class_id);
		foreach($getProperties as $p){

		if($p['itemproperty_setting'] == 1 || $p['itemproperty_datatype'] == 'NODE' ){
			}			
			else{
				$getValue = $this->getSingleItemPropertyValue($item_id, $p["itemproperty_id"]);
				$p['itempropertyvalue_content']=$getValue;
				$getPropertyvalues[]  = $p;		
			}
		}

		return $getPropertyvalues;
	}
	function getItemClassProperties($class_id){
		
		$id = $class_id;
		$sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='$id' ORDER BY itemproperty_varname";		
		$properties = $this->database->queryToArray($sql);

		return $properties;
	}
	function getSingleItemPropertyValue($item_id, $property_id){
		$sql = "SELECT itempropertyvalue_content FROM ItemPropertyValues WHERE itempropertyvalue_item_id='$item_id' AND itempropertyvalue_property_id='$property_id'";
		//echo $sql."<br>";
		$values = $this->database->queryToArray($sql);
		return $values[0]['itempropertyvalue_content'];
	}
}

?>