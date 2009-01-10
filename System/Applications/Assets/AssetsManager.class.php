<?php

/**
  * @category   Smartest
  * @package    Assets
  * @author     Marcus Gilroy-Ware <marcus@visudo.com>
  * @copyright  2006 Visudo LLC
  * @license    Visudo License
  * @version    0.6
  */

class AssetsManager{

	protected $database;
  
	public function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
	}
	
	public function getAssetTypes(){
		
		$types = array(
		    "user_text" => array(),
		    "image" => array(),
		    "browser_instructions" => array(),
		    "embedded" => array(),
		    "other" => array()
		);
		
		$processed_xml_data = SmartestDataUtility::getAssetTypes();
		
		if(is_array($processed_xml_data)){
		    foreach($processed_xml_data as $type_array){
		        if(isset($types[$type_array['category']])){
		            $cat_array =& $types[$type_array['category']];
		            if($type_array['id'] != 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
		                $cat_array[] = $type_array;
	                }
		        }
		    }
	    }
		
		return $types;
		
	}
	
	public function getAssetTypeCodes(){
		
		$processed_xml_data = SmartestDataUtility::getAssetTypes();
		
		$assetTypes = array_keys($processed_xml_data);
		
		return $assetTypes;
	}
	
	public function getIsValidAssetTypeCode($code){
	
		$assetTypes = $this->getAssetTypeCodes();
		
		if(in_array($code, $assetTypes)){
			return true;
		}else{
			return false;
		}
		
	}
	
	public function getAssetTypeId($assettype_code){
		return $this->database->specificQuery("assettype_id","assettype_code",$assettype_code,"AssetTypes");
	}
	
	public function insertAsset($webid, $stringid, $filename, $href, $type_id, $fragment_id){
		
		$sql = "INSERT INTO Assets (asset_webid, asset_stringid, asset_url, asset_href, asset_assettype_id, asset_fragment_id) VALUES ('$webid', '$stringid', '$filename', '$href', '$type_id', '$fragment_id')";
	
		if($fragment_id!=""){
		    $textfragment_asset_id=$this->database->query($sql);
		    return $textfragment_asset_id;
		}else{
			$this->database->rawQuery($sql);
		}
	}
	
	public function getAttachableAssetTypeCodes(){
	    
	    $helper = new SmartestAssetsLibraryHelper;
	    return $helper->getAttachableAssetTypeCodes();
	    
	    /*$processed_xml_data = SmartestDataUtility::getAssetTypes();
	    $codes = array();
	    
	    foreach($processed_xml_data as $code=>$type){
	        if(isset($type['attachable']) && SmartestStringHelper::toRealBool($type['attachable'])){
	            $codes[] = $code;
	        }
	    }
	    
	    return $codes; */
	    
	}
	
	public function getAttachableFiles($site_id=''){
	    
	    $helper = new SmartestAssetsLibraryHelper;
	    return $helper->getAttachableFiles($site_id);
	    
	    /* $attachable_type_codes = $this->getAttachableAssetTypeCodes();
	    $sql = "SELECT * FROM Assets WHERE asset_deleted!='1'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared='1')";
	    }
	    
	    $sql .= " AND asset_type IN ('".implode("', '", $attachable_type_codes)."') ORDER BY asset_stringid";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $assets = array();
	    
	    foreach($result as $a){
	        $asset = new SmartestAsset;
	        $asset->hydrate($a);
	        $assets[] = $asset;
	    }
	    
	    return $assets; */
	    
	}
	
	public function getAttachableFilesAsArrays($site_id=''){
	    
	    $helper = new SmartestAssetsLibraryHelper;
	    return $helper->getAttachableFilesAsArrays($site_id);
	    
	    /* $assets = $this->getAttachableFiles($site_id);
	    
	    $arrays = array();
	    
	    foreach($assets as $a){
	        $arrays[] = $a->__toArray();
	    }
	    
	    return $arrays; */
	    
	}
	
	public function getAssetsByTypeCode($code, $site_id=''){
		
		$helper = new SmartestAssetsLibraryHelper;
	    return $helper->getAssetsByTypeCode($code, $site_id);
		
		/* $sql = "SELECT * FROM Assets WHERE asset_type='$code' AND asset_deleted != 1";
		
		if(is_numeric($site_id)){
		    $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1) ORDER BY asset_stringid";
		}
		
		$assets = $this->database->queryToArray($sql);
		
		return $assets; */
	}
	
	public function getAssetById($asset_id){
		
		if(!is_numeric($asset_id)){
			$asset_id = $this->getAssetIdFromAssetWebId($asset_id);
		}
		
		$asset = $this->database->queryToArray("SELECT * FROM Assets, AssetTypes WHERE assettype_id=asset_assettype_id AND asset_id='$asset_id'");
		return $asset[0];
	}
	
	public function getAssetClassIdFromAssetClassName($assetclass_name){
		return $this->database->specificQuery("assetclass_id", "assetclass_name", $assetclass_name, "AssetClasses");
	}
	
	public function getAssetIdFromAssetWebId($asset_webid){
		return $this->database->specificQuery("asset_id", "asset_webid", $asset_webid, "Assets");
	}
	
	public function getAssetIdFromAssetTypeCode($asset_code){
		return $this->database->specificQuery("assettype_id", "assettype_code", $asset_code, "AssetTypes");
	}
	
	public function getNumericAssetId($asset_id){
		if(!is_numeric($asset_id)){
			return $this->getAssetIdFromAssetWebId($asset_id);
		}else{
			return $asset_id;
		}
	}
		
	public function getFragment($asset_id){
		$fragment_id = $this->database->specificQuery("asset_fragment_id", "asset_id", $asset_id, "Assets");
		return $this->database->specificQuery("textfragment_content", "textfragment_id", $fragment_id, "TextFragments");
	}
	
	public function getFileName($asset_id){
		return $this->database->specificQuery("asset_url", "asset_id", $asset_id, "Assets");
	}
	
	function getStringId($asset_id){
		return $this->database->specificQuery("asset_stringid", "asset_id", $asset_id, "Assets");
	}
  	
  	public function getFragmentIdfromAssetId($asset_id){
		
		return $this->database->specificQuery("asset_fragment_id", "asset_id", $asset_id, "Assets");

	}
	
	public function setAssetStringId($asset_id, $string){
		$sql_assets = "UPDATE Assets SET asset_stringid='$string' WHERE asset_id=$asset_id";
		$this->database->rawQuery($sql_assets);
	}
	
	public function setAssetTextFragmentContent($asset_id, $content){
		$sql_fragments = "UPDATE TextFragments SET textfragment_content='$content' WHERE textfragment_id=".$this->getFragmentIdfromAssetId($this->getNumericAssetId($asset_id));
		$this->database->rawQuery($sql_fragments);
	}
	
	public function getAssetInUseInDraftPage($asset_id){
		$sql = "SELECT * from Pages, AssetIdentifiers WHERE AssetIdentifiers.assetidentifier_page_id=Pages.page_id and AssetIdentifiers.assetidentifier_draft_asset_id = '$asset_id'";
		$assets_in_use_draftpage=$this->database->queryToArray($sql);
		return $assets_in_use_draftpage;		
	}
	
	public function getAssetInUseInLivePage($asset_id){
		$sql = "SELECT * from Pages, AssetIdentifiers WHERE AssetIdentifiers.assetidentifier_page_id=Pages.page_id and AssetIdentifiers.assetidentifier_live_asset_id = '$asset_id'";
		$assets_in_use_livepage=$this->database->queryToArray($sql);
		return $assets_in_use_livepage;
	}
	
	public function getDotSuffix($assettype_code){
		return $this->database->specificQuery("assettype_dotsuffixes", "assettype_code", $assettype_code, "AssetTypes");
	}
	
	public function deleteFromAssets($asset_id,$assettype_code){
		
		$asset_sql="DELETE FROM Assets WHERE asset_id=$asset_id";
		$this->database->rawQuery($asset_sql);
		
		if($assettype_code=='LINE' || $assettype_code=='TEXT' || $assettype_code=='HTML' ){
		    $this->deleteFromTextFragments($asset_id);
		}
	}
	
	public function deleteFromTextFragments($asset_id){
		$fragments_sql="DELETE FROM TextFragments WHERE textfragment_asset_id=$asset_id";
		$this->database->rawQuery($fragments_sql);
	}
	
	public function getUniqueName($file){
		
		$filename=explode('.',$file) ;
		$sql="SELECT * from Assets where asset_url = '$file'";
		$count=$this->database->howMany($sql);
		$i=1;
		
		while($count>0){
			
			$i=$i+1;
			
			$file=$filename[0].$i.".".$filename[1];
			$sql = "SELECT * from Assets where asset_url = '$file'";
			$v1 = $this->database->howMany($sql);
				
			if($v1==0){
				$file=$filename[0].$i.".".$filename[1];
				break;
			}else{
				$count=$v1;
			}
				
		}
			
		return $file;
	}
	
	public function getUniqueStringId($name){
		
		$name_temp=$name;
		$sql="SELECT * from Assets where asset_stringid = '$name'";
		$count=$this->database->howMany($sql);
		$i=1;
		
		while($count>0){
		
			$i=$i+1;
			$name=$name_temp.$i;
			$sql="SELECT * from Assets where asset_stringid = '$name'";
			$v1=$this->database->howMany($sql);
			
			if($v1==0){
				break;
			}else{
				$count=$v1;
			}
		}
		return $name;
	}
	
	public function getTemplateAssetTypeId(){
		$sql = "SELECT assettype_id FROM AssetTypes WHERE assettype_code='TMPL'";
		$result = $this->database->queryToArray($sql);
		return $result[0]['assettype_id'];
		
	}
	
	public function checkAssetSuffix($suffix){
		$sql="SELECT * from AssetTypes";
		$assettypes = $this->database->queryToArray($sql); 
		foreach($assettypes as $type){
		$typ_suffix=$type['assettype_dotsuffixes'];
		$suffixes=explode(',',$typ_suffix);
		if(in_array($suffix,$suffixes)){return $type['assettype_id'];}		
		}
		
	}
}