<?php

class SmartestImage extends SmartestFile{
    
    protected $_resource;
    protected $_image_type;
    protected $_width;
    protected $_height;
    
    const JPEG = 'jpeg';
    const JPG = 'jpeg';
    const GIF = 'gif';
    const PNG = 'png';
    
    public function loadFile($file_path, $parse_image=true){
        if(is_file($file_path)){
            
            $this->_original_file_path = $file_path;
            $this->_current_file_path = $file_path;
            
            if($parse_image){
            
                $suffix = SmartestStringHelper::getDotSuffix($this->_current_file_path);
            
                switch($suffix){
                
                    case "JPG":
                    case "JPEG":
                    $resource = @imagecreatefromjpeg($this->_current_file_path);
                    break;
                
                    case "PNG":
                    $resource = @imagecreatefrompng($this->_current_file_path);
                    break;
                
                    case "GIF":
                    $resource = @imagecreatefromgif($this->_current_file_path);
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
        return $this->_directory.$this->_file_name;
    }
    
    public function getType(){
        
    }
    
    public function setType($type){
        
    }
    
    public function setFilename($filename){
        
    }
    
    public function getThumbnail($max_width, $max_height, $refresh=false, $round_corners=false){
        $proposed_thumbnail_filename = SmartestStringHelper::removeDotSuffix($this->_current_file_path).'_'.$max_width.'_'.$max_height.'.'.SmartestStringHelper::getDotSuffix($this->_current_file_path);
        
    }
    
    public function getWidth(){
        if(isset($this->_width)){
            return $this->_width;
        }else{
            // echo $this->getPath();
            list($width, $height) = getimagesize($this->getPath(), $data);
            // print_r($width);
            $this->_width = $width;
            $this->_height = $height;
            return $this->_width;
        }
    }
    
    public function getHeight(){
        if(isset($this->_height)){
            return $this->_height;
        }else{
            // echo $this->getPath();
            list($width, $height) = getimagesize($this->getPath(), $data);
            // print_r($width);
            $this->_width = $width;
            $this->_height = $height;
            return $this->_height;
        }
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
				// $this->error("No EXIF data was found in the image.", "getExifData");
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
    
}