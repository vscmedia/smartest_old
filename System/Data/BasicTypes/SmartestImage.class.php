<?php

class SmartestImage extends SmartestFile{
    
    protected $_resource;
    protected $_image_type = null;
    protected $_width;
    protected $_height;
    protected $_thumbnail_resource;
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
      
       $suffix = strtoupper(SmartestStringHelper::getDotSuffix($this->_current_file_path));

        switch($this->getImageType()){

            case self::JPG:
            $resource = imagecreatefromjpeg($this->_current_file_path);
            break;

            case self::PNG:
            $resource = imagecreatefrompng($this->_current_file_path);
            break;

            case self::GIF:
            $resource = imagecreatefromgif($this->_current_file_path);
            break;
        }

        if($resource){
            $this->_resource = $resource;
            return $this->_resource;
        }else{
            return null;
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
    
    public function resizeAndCrop($width, $height){
        
        $url = 'Resources/System/Cache/Images/'.SmartestStringHelper::toVarName(basename($this->_current_file_path)).$width.'x'.$height.'.png';
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
            imagecopyresampled($r, $this->getResource(), 0,0, $src_x, $src_y, $width, $height, $src_w, $src_h);
            
            $newversion = new SmartestImage;
            
            if(imagepng($r, $full_path, 0)){
                imagedestroy($r);
                $newversion->loadFile($full_path);
                return $newversion;
            }
        
        }
        
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
            
            if(imagepng($this->_thumbnail_resource, $full_path, 0)){
                $this->clearThumbnailResource();
                $thumbnail->loadFile($full_path);
                return $thumbnail;
            }
            
        }
        
    }
    
    public function getSquareVersionFilename($side){
        return SmartestStringHelper::toVarName(basename($this->_current_file_path)).'_sqthumb_'.$side.'.png';
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
            imagecopyresampled($r, $this->getResource(), 0,0, 0,0, $width, $new_height, $this->getWidth(), $this->getHeight());
            
            $newversion = new SmartestImage;
            
            if(imagepng($r, $full_path, 0)){
                imagedestroy($r);
                $newversion->loadFile($full_path);
                return $newversion;
            }
            
        }
        
    }
    
    public function getWidthRestrictedVersionFilename($w){
        return SmartestStringHelper::toVarName(basename($this->_current_file_path)).'_width_'.$w.'.png';
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
            imagecopyresampled($r, $this->getResource(), 0,0, 0,0, $new_width, $height, $this->getWidth(), $this->getHeight());
            
            $newversion = new SmartestImage;
            
            if(imagepng($r, $full_path, 0)){
                imagedestroy($r);
                $newversion->loadFile($full_path);
                return $newversion;
            }
            
        }
        
    }
    
    public function getHeightRestrictedVersionFilename($h){
        return SmartestStringHelper::toVarName(basename($this->_current_file_path)).'_height_'.$h.'.png';
    }
    
    public function getResizedVersionFromMaxLongSide($max_long_side){
        
        $long_side = max($this->getWidth(), $this->getHeight());
        $percentage = ceil($max_long_side/$long_side*100);
        return $this->getResizedVersionFromPercentage($percentage);
        
    }
    
    public function getResizedVersionFromPercentage($percentage){
        
        $file = $this->getResizeFilenameFromPercentage($percentage);
        $url = 'Resources/System/Cache/Images/'.$this->getResizeFilenameFromPercentage($percentage);
        $full_path = SM_ROOT_DIR.'Public/'.$url;
        
        // check the work hasn't already been done
        if(file_exists($full_path)){
            
            $thumbnail = new SmartestImage;
            $thumbnail->loadFile($full_path);
            return $thumbnail;
            
        }else{
        
            $new_width = ceil($percentage/100*$this->getWidth());
            $new_height = ceil($percentage/100*$this->getHeight());
            $this->_thumbnail_resource = ImageCreateTrueColor($new_width, $new_height);
            imagecopyresampled($this->_thumbnail_resource, $this->getResource(), 0,0,0,0, $new_width, $new_height, $this->getWidth(), $this->getHeight());
            $thumbnail = new SmartestImage;
            
            switch($this->_image_type){
            
                case self::JPEG:
            
                if(imagejpeg($this->_thumbnail_resource, $full_path, 100)){
                    $this->clearThumbnailResource();
                    $thumbnail->loadFile($full_path);
                    return $thumbnail;
                }
            
                break;
            
                case self::PNG:
            
                if(imagepng($this->_thumbnail_resource, $full_path, 100)){
                    $this->clearThumbnailResource();
                    $thumbnail->loadFile($full_path);
                    return $thumbnail;
                }
            
                break;
            
                case self::GIF:
            
                if(imagegif($this->_thumbnail_resource, $full_path, 100)){
                    $this->clearThumbnailResource();
                    $thumbnail->loadFile($full_path);
                    return $thumbnail;
                }
            
                break;
            
            }
        
        }
        
    }
    
    public function clearThumbnailResource(){
        
        imagedestroy($this->_thumbnail_resource);
        
    }
    
    public function getResizeFilenameFromPercentage($percentage){
        
        return $filename = SmartestStringHelper::toVarName(basename($this->_current_file_path)).'_resize_'.$percentage.'pc.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
        
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
	    
	    if(preg_match('/(\d+)x(\d+)/', $offset, $m)){
	        return $this->resizeAndCrop($m[1], $m[2]);
	    }elseif(preg_match('/square_(\d+)/', $offset, $m)){
	        return $this->getSquareVersion($m[1]);
	    }elseif(preg_match('/width_(\d+)/', $offset, $m)){
	        return $this->restrictToWidth($m[1]);
	    }elseif(preg_match('/height_(\d+)/', $offset, $m)){
	        return $this->restrictToHeight($m[1]);
	    }
	    
	    switch($offset){
	        
	        case "width":
	        return $this->getWidth();
	        
	        case "height":
	        return $this->getHeight();
	        
	        case "web_path":
	        return $this->getWebPath();
	        
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