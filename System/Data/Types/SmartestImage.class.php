<?php

class SmartestImage extends SmartestFile{
    
    protected $_resource;
    protected $_image_type;
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
    
    public function loadFile($file_path, $parse_image=true){
        
        if(is_file($file_path)){
            
            $this->_original_file_path = realpath($file_path);
            $this->_current_file_path = realpath($file_path);
            
            if($parse_image){
            
                $suffix = strtoupper(SmartestStringHelper::getDotSuffix($this->_current_file_path));
                
                switch($suffix){
                
                    case "JPG":
                    case "JPEG":
                    $resource = imagecreatefromjpeg($this->_current_file_path);
                    $this->_image_type = self::JPEG;
                    break;
                
                    case "PNG":
                    $resource = imagecreatefrompng($this->_current_file_path);
                    $this->_image_type = self::PNG;
                    break;
                
                    case "GIF":
                    $resource = imagecreatefromgif($this->_current_file_path);
                    $this->_image_type = self::GIF;
                    break;
                }
            
                if($resource){
                    $this->_resource = $resource;
                    return true;
                }else{
                    return false;
                }
            
            }else{
                return true;
            }
            
        }else{
            // throw new SmartestException($file_path.' does not exist or is not a valid file.');
            return false;
        }
    }
    
    public function getFullPath(){
        // return $this->_directory.$this->_file_name;
        return $this->_current_file_path;
    }
    
    public function isPublic(){
        
        $path_start = SM_ROOT_DIR.'Public';
        $path_start_length = strlen($path_start);
        
        return substr($this->_current_file_path, 0, $path_start_length) == $path_start;
        
    }
    
    public function getPublicPath(){
        
        if($this->isPublic()){
            
            $path_start = SM_ROOT_DIR.'Public/';
            $path_start_length = strlen($path_start);
            return substr($this->_current_file_path, $path_start_length);
            
        }
        
    }
    
    public function getWebUrl(){
        if($this->isPublic()){
            return SM_CONTROLLER_DOMAIN.$this->getPublicPath();
        }
    }
    
    public function getType(){
        return $this->_image_type;
    }
    
    /* public function setFilename($filename){
        
    }
    
    public function getThumbnail($max_width, $max_height, $refresh=false, $round_corners=false){
        // $proposed_thumbnail_filename = SmartestStringHelper::removeDotSuffix($this->_current_file_path).'_'.$max_width.'_'.$max_height.'.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
        
    } */
    
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
            imagecopyresampled($this->_thumbnail_resource, $this->_resource, 0,0,0,0, $new_width, $new_height, $this->getWidth(), $this->getHeight());
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
	
	public function getExifData() { // retrieves EXIF data from the file
		
		if(function_exists('exif_read_data')){
			
			$exif = @exif_read_data($this->getFullPath(), "IFD0", true); // there is an EXIF bug here that causes an error. the error must be suppressed.
			
			if($exif===false ){
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
			// $this->error("File could not be found.", "getExifData");
			return null;
		}
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "width":
	        return $this->getWidth();
	        
	        case "height":
	        return $this->getHeight();
	        
	        case "url":
	        return $this->getWebUrl();
	        break;
	        
	        case "file_path":
	        return $this->getFullPath();
	        break;
	        
	        case "public_file_path":
	        return $this->getPublicPath();
	        break;
	        
	    }
	    
	}
    
}