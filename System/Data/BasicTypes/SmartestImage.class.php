<?php

class SmartestImage extends SmartestFile{
    
    protected $_resource;
    protected $_image_type = null;
    protected $_width;
    protected $_height;
    protected $_thumbnail_resource;
    protected $_render_data;
    // these vars are already declared in SmartestFile:
    // protected $_original_file_path;
    // protected $_current_file_path;
    
    const JPEG = 'jpeg';
    const JPG = 'jpeg';
    const GIF = 'gif';
    const PNG = 'png';
    
    const LANDSCAPE = 'ls';
    const SQUARE = 'sq';
    const PORTRAIT = 'pr';
    
    public function __construct(){
        parent::__construct();
        $this->_render_data = new SmartestParameterHolder('Image render data');
    }
    
    public function __toString(){
        return (string) $this->render();
    }
    
    public function getFullPath(){
        // return $this->_directory.$this->_file_name;
        return $this->_current_file_path;
    }
    
    public function getWebPath(){
        if($this->isPublic()){
            return $this->_request_data->g('domain').substr($this->getFullPath(), strlen(SM_ROOT_DIR.'Public/'));
        }
    }
    
    public function isPublic(){
        return substr($this->getFullPath(), 0, strlen(SM_ROOT_DIR.'Public/')) == SM_ROOT_DIR.'Public/';
    }
    
    public function getResource(){
      
       $resource = $this->createResource($this->_current_file_path);
       
       if($resource){
           $this->_resource = $resource;
           return $this->_resource;
       }else{
           return null;
       }
      
    }
    
    public function createResource($file_path){
        
        if(is_file($file_path)){
            
            $suffix = strtoupper(SmartestStringHelper::getDotSuffix($file_path));

            switch($suffix){

                case "JPG":
                case "JPEG":
                $resource = imagecreatefromjpeg($file_path);
                break;

                case "PNG":
                $resource = imagecreatefrompng($file_path);
                break;

                case "GIF":
                $resource = imagecreatefromgif($file_path);
                break;
            }

            return $resource;
            
        }
        
    }
    
    public function getImageType(){
        
        if(!$this->_image_type){
        
            $suffix = strtoupper(SmartestStringHelper::getDotSuffix($this->_current_file_path));

            switch($suffix){

                case "JPG":
                case "JPEG":
                $this->_image_type = self::JPEG;
                break;

                case "PNG":
                $this->_image_type = self::PNG;
                break;

                case "GIF":
                $this->_image_type = self::GIF;
                break;
            }
        
        }
        
        return $this->_image_type;
    }
    
    public function getSuffix(){
        
        return strtolower(SmartestStringHelper::getDotSuffix($this->_current_file_path));
        
    }
    
    public function setAdditionalRenderData($info, $not_empty_only=false){
	    
	    if($info instanceof SmartestParameterHolder){
	        $info = $info->getParameters();
	    }
	    
	    if(is_array($info)){
	        foreach($info as $key=>$value){
	            if(!$not_empty_only || ($not_empty_only && strlen($value))){
	                $this->_render_data->setParameter($key, $value);
                }
	        }
	    }
	    
	}
	
	public function setSingleRenderDataParameter($name, $value){
	    $this->_render_data->setParameter($name, $value);
	}
    
    public function getOrientation(){
        if($this->getHeight() == $this->getWidth()){
            return self::SQUARE;
        }else if($this->getHeight() > $this->getWidth()){
            return self::PORTRAIT;
        }else{
            return self::LANDSCAPE;
        }
    }
    
    public function isSquare(){
        return $this->getOrientation() == self::SQUARE;
    }
    
    public function isPortrait(){
        return $this->getOrientation() == self::PORTRAIT;
    }
    
    public function isLandscape(){
        return $this->getOrientation() == self::LANDSCAPE;
    }
    
    public function isTooLarge(){
        $info = $this->getTypeInfo();
        if($this->getSize(true) > $info['maximum_filesize_before_warning']){
            return true;
        }else if($this->isPortrait() && $this->getHeight() > 800){
            return true;
        }else if($this->isLandscape() && $this->getWidth() > 2000){
            return true;
        }else{
            return false;
        }
    }
    
    public function getTypeInfo(){
        
        if(!$this->_type_info){
	        
	        $asset_types = SmartestDataUtility::getAssetTypes();
	        
	        if(array_key_exists($this->getAssetTypeFromSuffix(), $asset_types)){
	            $this->_type_info = $asset_types[$this->getAssetTypeFromSuffix()];
	        }else{
	            // some sort of error? unsupported type
	        }
	        
	    }
	    
	    return $this->_type_info;
        
    }
    
    public function getAssetTypeFromSuffix(){
        switch($this->getSuffix()){
            case "jpeg":
            case "jpg":
            return "SM_ASSETTYPE_JPEG_IMAGE";
            case "png":
            return "SM_ASSETTYPE_PNG_IMAGE";
            case "gif":
            return "SM_ASSETTYPE_GIF_IMAGE";
        }
    }
    
    public function resizeAndCrop($width, $height){
        
        $url = 'Resources/System/Cache/Images/'.SmartestStringHelper::toVarName(basename($this->_current_file_path)).$width.'x'.$height.'.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        if(file_exists($full_path)){
            
            $thumbnail = new SmartestImage;
            $thumbnail->loadFile($full_path);
            return $thumbnail;
            
        }else{
        
            $height_diff = $this->getHeight() - $height;
            $width_diff = $this->getWidth() - $width;
            
            if($height_diff/$this->getHeight() > $width_diff/$this->getWidth()){
                // change in height is proportionally more than change in width, so scale according to width
                $src_w = $this->getWidth();
                $src_h = ceil($this->getWidth()/$width*$height);
                $src_y = ceil(($this->getHeight()-$src_h)/2);
                $src_x = 0;
            }else{
                // change in width is proportionally more than change in height, so scale according to height
                $src_w = ceil($this->getHeight()/$height*$width);
                $src_h = $this->getHeight();
                $src_y = 0;
                $src_x = ceil(($this->getWidth()-$src_w)/2);
            }
            
            $r = ImageCreateTrueColor($width, $height);
            
            if($this->getImageType() == self::PNG){
                imagealphablending($r, false);
                imagesavealpha($r, true);
            }
            
            imagecopyresampled($r, $this->getResource(), 0,0, $src_x, $src_y, $width, $height, $src_w, $src_h);
            
            $newversion = new SmartestImage;
            
            if($this->saveToFile($r, $full_path)){
                $newversion->loadFile($full_path);
                return $newversion;
            }
        
        }
        
    }
    
    public function saveToFile($resource, $path, $quality=null){
        
        $suffix = strtoupper(SmartestStringHelper::getDotSuffix($path));

        switch($suffix){

            case "JPG":
            case "JPEG":
            if(!$quality){$quality == 85;}
            $r = imagejpeg($resource, $path, 85);
            break;

            case "PNG":
            if(!$quality){$quality == 0;}
            $r = imagepng($resource, $path, 0);
            break;

            case "GIF":
            $r = imagegif($resource, $path);
            break;
        }
        
        if($r){
            imagedestroy($resource);
        }
        
        return $r;
        
    }
    
    public function getSquareVersion($side){
        
        $url = 'Resources/System/Cache/Images/'.$this->getSquareVersionFilename($side);
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        if(file_exists($full_path)){
            
            $thumbnail = new SmartestImage;
            $thumbnail->loadFile($full_path);
            return $thumbnail;
            
        }else{
            
            if($this->isLandscape()){
                $shortside = $this->getHeight();
                $vcopystart = 0;
                $hcopystart = ($this->getWidth() - $this->getHeight())/2;
            }else{
                $shortside = $this->getWidth();
                $hcopystart = 0;
                $vcopystart = ($this->getHeight() - $this->getWidth())/2;
            }
            
            $this->_thumbnail_resource = ImageCreateTrueColor($side, $side);
            imagecopyresampled($this->_thumbnail_resource, $this->getResource(), 0,0, $hcopystart, $vcopystart, $side, $side, $shortside, $shortside);
            
            $thumbnail = new SmartestImage;
            
            if($this->saveToFile($this->_thumbnail_resource, $full_path)){
                // saveToFile() automatically destroys image resource, making the following commented line unnecessary
                $thumbnail->loadFile($full_path);
                return $thumbnail;
            }
            
        }
        
    }
    
    public function getSquareVersionFilename(){
        return SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix(basename($this->_current_file_path))).'_sqthumb_'.$side.'.'.$this->getSuffix();
    }
    
    public function getSquarePreviewForUI(){
        // converts into a medium-quality jpg regardless of file type
        
        $url = 'Resources/System/Cache/Images/'.$this->getSquarePreviewForUIFilename();
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        $side = 73;
        
        if(file_exists($full_path)){
            
            $thumbnail = new SmartestImage;
            $thumbnail->loadFile($full_path);
            return $thumbnail;
            
        }else{
            
            if($this->isLandscape()){
                $shortside = $this->getHeight();
                $vcopystart = 0;
                $hcopystart = ($this->getWidth() - $this->getHeight())/2;
            }else{
                $shortside = $this->getWidth();
                $hcopystart = 0;
                $vcopystart = ($this->getHeight() - $this->getWidth())/2;
            }
            
            $this->_thumbnail_resource = ImageCreateTrueColor($side, $side);
            imagecopyresampled($this->_thumbnail_resource, $this->getResource(), 0,0, $hcopystart, $vcopystart, $side, $side, $shortside, $shortside);
            
            $thumbnail = new SmartestImage;
            
            if($this->saveToFile($this->_thumbnail_resource, $full_path, 70)){
                // saveToFile() automatically destroys image resource, making the following commented line unnecessary
                $thumbnail->loadFile($full_path);
                return $thumbnail;
            }
            
        }
        
    }
    
    public function getSquarePreviewForUIFilename(){
        return SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix(basename($this->_current_file_path))).'_squiprv.jpg';
    }
    
    public function restrictToWidth($width){
        
        $width = (int) $width;
        
        $url = 'Resources/System/Cache/Images/'.$this->getWidthRestrictedVersionFilename($width);
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        if(file_exists($full_path)){
            
            $newversion = new SmartestImage;
            $newversion->loadFile($full_path);
            return $newversion;
            
        }else{
            
            $new_height = (int) ($width/$this->getWidth()*$this->getHeight());
            $r = ImageCreateTrueColor($width, $new_height);
            
            if($this->getImageType() == self::PNG){
                imagealphablending($r, false);
                imagesavealpha($r, true);
            }
            
            imagecopyresampled($r, $this->getResource(), 0,0, 0,0, $width, $new_height, $this->getWidth(), $this->getHeight());
            
            $newversion = new SmartestImage;
            
            if($this->saveToFile($r, $full_path)){
                $newversion->loadFile($full_path);
                return $newversion;
            }
            
        }
        
    }
    
    public function getWidthRestrictedVersionFilename($w){
        return SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix(basename($this->_current_file_path))).'_width_'.$w.'.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
    }
    
    public function restrictToHeight($height){
        
        $width = (int) $width;
        
        $url = 'Resources/System/Cache/Images/'.$this->getHeightRestrictedVersionFilename($height);
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        if(file_exists($full_path)){
            
            $newversion = new SmartestImage;
            $newversion->loadFile($full_path);
            return $newversion;
            
        }else{
            
            $new_width = (int) ($height/$this->getHeight()*$this->getWidth());
            $r = ImageCreateTrueColor($new_width, $height);
            
            if($this->getImageType() == self::PNG){
                imagealphablending($r, false);
                imagesavealpha($r, true);
            }
            
            imagecopyresampled($r, $this->getResource(), 0,0, 0,0, $new_width, $height, $this->getWidth(), $this->getHeight());
            
            $newversion = new SmartestImage;
            
            if($this->saveToFile($r, $full_path)){
                $newversion->loadFile($full_path);
                return $newversion;
            }
            
        }
        
    }
    
    public function getHeightRestrictedVersionFilename($h){
        return SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix(basename($this->_current_file_path))).'_height_'.$h.'.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
    }
    
    public function getResizedVersionFromMaxLongSide($max_long_side){
        
        $long_side = max($this->getWidth(), $this->getHeight());
        $percentage = ceil($max_long_side/$long_side*100);
        return $this->getResizedVersionFromPercentage($percentage);
        
    }
    
    public function overlayWith($full_file_path){
        
        if(is_file($full_file_path)){
            
            $new_file_path = SM_ROOT_DIR.'Public/Resources/System/Cache/Images/'.SmartestStringHelper::removeDotSuffix(SmartestFileSystemHelper::getFileName($this->_current_file_path)).'_O_'.SmartestStringHelper::removeDotSuffix(SmartestFileSystemHelper::getFileName($full_file_path)).'.'.$this->getSuffix();
            
            if(is_file($new_file_path)){
                
                $img = new SmartestImage;
                $img->loadFile($new_file_path);
                return $img;
                
            }else{
            
                $overlaid_image_resource = $this->createResource($full_file_path);
                $new_image_rsrc = $this->getResource();
                imagealphablending($new_image_rsrc, true);
                imagesavealpha($new_image_rsrc, FALSE);
                imagecopy($new_image_rsrc, $overlaid_image_resource, 0,0,0,0, $this->getWidth(), $this->getHeight());
            
                if($this->saveToFile($new_image_rsrc, $new_file_path)){
                    // dispose of overlaid image resource to free up memory. The new image resource has already been disposed of by saveToFile(), assuming it was successful
                    imagedestroy($overlaid_image_resource);
                    $new_image = new SmartestImage;
                    $new_image->loadFile($new_file_path);
                    return $new_image;
                }
            
            }
            
        }else{
            return $this;
        }
        
    }
    
    public function getResizedVersionFromPercentage($percentage){
        
        $file = $this->getResizeFilenameFromPercentage($percentage);
        $url = 'Resources/System/Cache/Images/'.$file;
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        // check the work hasn't already been done
        if(file_exists($full_path)){
            
            $thumbnail = new SmartestImage;
            $thumbnail->loadFile($full_path);
            return $thumbnail;
            
        }else{
        
            $new_width = ceil($percentage/100*$this->getWidth());
            $new_height = ceil($percentage/100*$this->getHeight());
            $thumbnail_resource = ImageCreateTrueColor($new_width, $new_height);
            
            if($this->getImageType() == self::PNG){
                imagealphablending($thumbnail_resource, false);
                imagesavealpha($thumbnail_resource, true);
            }
            
            imagecopyresampled($thumbnail_resource, $this->getResource(), 0,0,0,0, $new_width, $new_height, $this->getWidth(), $this->getHeight());
            $thumbnail = new SmartestImage;
            
            if($this->saveToFile($thumbnail_resource, $full_path)){
                $thumbnail->loadFile($full_path);
                return $thumbnail;
            }
        
        }
        
    }
    
    public function getConstrainedVersionWithin($width, $height){
        
        $width_change = $width/$this->getWidth();
        $height_change = $height/$this->getHeight();
        
        if($width_change >= 1 && $height_change >= 1){
            return $this;
        }
        
        if($width_change < $height_change){
            $percentage = ceil($width_change * 100);
        }else{
            $percentage = ceil($height_change * 100);
        }
        
        return $this->getResizedVersionFromPercentage($percentage);
        
    }
    
    public function getResizedVersionNoScale($width, $height){
        
        $file = $this->getResizeFilenameNoScale($width, $height);
        $url = 'Resources/System/Cache/Images/'.$file;
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        // check the work hasn't already been done
        if(file_exists($full_path)){
            
            $thumbnail = new SmartestImage;
            $thumbnail->loadFile($full_path);
            return $thumbnail;
            
        }else{
        
            /* $new_width = ceil($percentage/100*$this->getWidth());
            $new_height = ceil($percentage/100*$this->getHeight()); */
            $thumbnail_resource = ImageCreateTrueColor($width, $height);
            
            if($this->getImageType() == self::PNG){
                imagealphablending($thumbnail_resource, false);
                imagesavealpha($thumbnail_resource, true);
            }
            
            imagecopyresampled($thumbnail_resource, $this->getResource(), 0,0,0,0, $width, $height, $this->getWidth(), $this->getHeight());
            $thumbnail = new SmartestImage;
            
            if($this->saveToFile($thumbnail_resource, $full_path)){
                $thumbnail->loadFile($full_path);
                return $thumbnail;
            }
        
        }
        
    }
    
    public function getResizeFilenameNoScale($width, $height){
        
        return $filename = SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix(basename($this->_current_file_path))).'_resize_noscale_'.$width.'x'.$height.'.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
        
    }
    
    public function clearThumbnailResource(){
        
        imagedestroy($this->_thumbnail_resource);
        
    }
    
    public function getResizeFilenameFromPercentage($percentage){
        
        return $filename = SmartestStringHelper::toVarName(SmartestStringHelper::removeDotSuffix(basename($this->_current_file_path))).'_resize_'.$percentage.'pc.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
        
    }
    
    public function getWidth(){
        if(isset($this->_width)){
            return $this->_width;
        }else{
            
            list($width, $height) = getimagesize($this->getPath(), $data);
            
            $this->_width = $width;
            $this->_height = $height;
            return $this->_width;
        }
    }
    
    public function getHeight(){
        if(isset($this->_height)){
            return $this->_height;
        }else{
            
            list($width, $height) = getimagesize($this->getPath(), $data);
            
            $this->_width = $width;
            $this->_height = $height;
            return $this->_height;
        }
    }
    
    public function resize($percentage){
        
        return $this->getResizedVersionFromPercentage($percentage);
        
    }
    
    public function getIptcData() { // retrieves IPTC data from the file
        
        if(function_exists('iptcparse')){
        
			$size = getimagesize ($this->getFullPath(), $info);       
			
			if(is_array($info)) {   
				
				$iptc = iptcparse($info["APP13"]);
				$iptcData = array();
				
				foreach (array_keys($iptc) as $key=>$s) {             
					$c = count ($iptc[$s]);
					for ($i=0; $i <$c; $i++){
						$iptcData[$key] = $iptc[$s][$i];
					}
				}
				
				return $iptcData;
			}else{
				// $this->error("Did not receive array from getimagesize.", "getIptcData");
				return false;
			}
		}
	}
	
	public function getExifData($section='ANY_TAG') { // retrieves EXIF data from the file
		
		if(function_exists('exif_read_data')){
		    
		    if($this->getImageType() == self::JPG){
			    
			    $exif = @exif_read_data($this->getFullPath(), $section, true); // there is an EXIF bug here that causes an error. the error must be suppressed.
			    
			    if($exif===false){
    				return null;
    			}else{
				
    				$exifData = array();
				
    				foreach ($exif as $key => $section) {
    					foreach ($section as $name => $val) {
    						$exifData[$key][$name] = $val;
    					}
    				}
				
    				return $exifData;
    			}
			
		    }else{
		        
		        SmartestLog::getInstance('system')->log("SmartestImage::getExifData() can only be used on JPEG images. ".strtoupper($this->getImageType()).' image given.');
    			return null;
		        
		    }
			
		}else{
			// $this->error("File could not be found.", "getExifData");
			SmartestLog::getInstance('system')->log("SmartestImage::getExifData() failed because PHP couldn't find the exif_read_data() function.");
			return null;
		}
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "width":
	        return $this->getWidth();
	        
	        case "height":
	        return $this->getHeight();
	        
	        case "is_portrait":
	        return $this->isPortrait();
	        
	        case "is_landscape":
	        return $this->isLandscape();
	        
	        case "is_square":
	        return $this->isSquare();
	        
	        case "web_path":
	        return $this->getWebPath();
	        
	        case "_ui_preview":
	        $prev = $this->getSquarePreviewForUI();
	        if($this->_resource){
	            imagedestroy($this->_resource);
            }
	        return $prev;
	        
	        case "empty":
	        return !is_file($this->_current_file_path);
	        
	    }
	    
	    if(preg_match('/^(\d+)x(\d+)/', $offset, $m)){
	        return $this->resizeAndCrop($m[1], $m[2]);
	    }elseif(preg_match('/square_(\d+)/', $offset, $m)){
	        return $this->getSquareVersion($m[1]);
	    }elseif(preg_match('/width_(\d+)/', $offset, $m)){
	        return $this->restrictToWidth($m[1]);
	    }elseif(preg_match('/height_(\d+)/', $offset, $m)){
	        return $this->restrictToHeight($m[1]);
	    }elseif(preg_match('/constrain_(\d+)x(\d+)/', $offset, $m)){
	        return $this->getConstrainedVersionWithin($m[1], $m[2]);
	    }elseif(preg_match('/stretch_(\d+)x(\d+)/', $offset, $m)){
            return $this->getResizedVersionNoScale($m[1], $m[2]);
        }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function render(){
	    
	    $sm = new SmartyManager('BasicRenderer');
        $r = $sm->initialize($this->getShortHash());
        $r->assignImage($this);
        $content = $r->renderImage($this->_render_data);
        
	    return $content;
	    
	}
	
	public function send(){
	    
	    $suffix = strtoupper(SmartestStringHelper::getDotSuffix($this->_current_file_path));
        
        switch($suffix){

            case "JPG":
            case "JPEG":
            header("Content-type: image/jpeg");
            imagejpeg($this->getResource());
            break;

            case "PNG":
            header("Content-type: image/png");
            imagepng($this->getResource());
            break;

            case "GIF":
            header("Content-type: image/gif");
            imagegif($this->getResource());
            break;
            
        }
        
        exit;
	    
	}
    
}