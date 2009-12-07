<?php

SmartestHelper::register('String');

define("SM_OPTIONS_MAGIC_QUOTES", (bool) ini_get('magic_quotes_gpc'));

class SmartestStringHelper extends SmartestHelper{

	static function random($size){
	
		$possValues = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s"
			    , "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", 
			    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		$stem = "";
	
		for($i=0; $i<$size; $i++){
			$randNum = rand(0, count($possValues)-1);
			$shoot = $possValues[$randNum];
			$plant = $stem.$shoot;
			$stem = $plant;
		}	
	
		return $plant;
	
	}
	
	static function isAscii(){
	    return (preg_match('/(?:[^\x00-\x7F])/',$str) !== 1);
	}
	
	static function toAscii($string){

        static $UTF8_LOWER_ACCENTS = NULL;
        static $UTF8_UPPER_ACCENTS = NULL;

        if($case <= 0){

            if ( is_null($UTF8_LOWER_ACCENTS) ) {
                $UTF8_LOWER_ACCENTS = array(
      'à' => 'a', 'ô' => 'o', 'ď' => 'd', 'ḟ' => 'f', 'ë' => 'e', 'š' => 's', 'ơ' => 'o',
      'ß' => 'ss', 'ă' => 'a', 'ř' => 'r', 'ț' => 't', 'ň' => 'n', 'ā' => 'a', 'ķ' => 'k',
      'ŝ' => 's', 'ỳ' => 'y', 'ņ' => 'n', 'ĺ' => 'l', 'ħ' => 'h', 'ṗ' => 'p', 'ó' => 'o',
      'ú' => 'u', 'ě' => 'e', 'é' => 'e', 'ç' => 'c', 'ẁ' => 'w', 'ċ' => 'c', 'õ' => 'o',
      'ṡ' => 's', 'ø' => 'o', 'ģ' => 'g', 'ŧ' => 't', 'ș' => 's', 'ė' => 'e', 'ĉ' => 'c',
      'ś' => 's', 'î' => 'i', 'ű' => 'u', 'ć' => 'c', 'ę' => 'e', 'ŵ' => 'w', 'ṫ' => 't',
      'ū' => 'u', 'č' => 'c', 'ö' => 'o', 'è' => 'e', 'ŷ' => 'y', 'ą' => 'a', 'ł' => 'l',
      'ų' => 'u', 'ů' => 'u', 'ş' => 's', 'ğ' => 'g', 'ļ' => 'l', 'ƒ' => 'f', 'ž' => 'z',
      'ẃ' => 'w', 'ḃ' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', 'ḋ' => 'd', 'ť' => 't',
      'ŗ' => 'r', 'ä' => 'a', 'í' => 'i', 'ŕ' => 'r', 'ê' => 'e', 'ü' => 'u', 'ò' => 'o',
      'ē' => 'e', 'ñ' => 'n', 'ń' => 'n', 'ĥ' => 'h', 'ĝ' => 'g', 'đ' => 'd', 'ĵ' => 'j',
      'ÿ' => 'y', 'ũ' => 'u', 'ŭ' => 'u', 'ư' => 'u', 'ţ' => 't', 'ý' => 'y', 'ő' => 'o',
      'â' => 'a', 'ľ' => 'l', 'ẅ' => 'w', 'ż' => 'z', 'ī' => 'i', 'ã' => 'a', 'ġ' => 'g',
      'ṁ' => 'm', 'ō' => 'o', 'ĩ' => 'i', 'ù' => 'u', 'į' => 'i', 'ź' => 'z', 'á' => 'a',
      'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u', 'ĕ' => 'e');
            }

            $string = str_replace(array_keys($UTF8_LOWER_ACCENTS), array_values($UTF8_LOWER_ACCENTS), $string);
        }

        if($case >= 0){
            
            if ( is_null($UTF8_UPPER_ACCENTS) ) {
                $UTF8_UPPER_ACCENTS = array(
      'À' => 'A', 'Ô' => 'O', 'Ď' => 'D', 'Ḟ' => 'F', 'Ë' => 'E', 'Š' => 'S', 'Ơ' => 'O',
      'Ă' => 'A', 'Ř' => 'R', 'Ț' => 'T', 'Ň' => 'N', 'Ā' => 'A', 'Ķ' => 'K',
      'Ŝ' => 'S', 'Ỳ' => 'Y', 'Ņ' => 'N', 'Ĺ' => 'L', 'Ħ' => 'H', 'Ṗ' => 'P', 'Ó' => 'O',
      'Ú' => 'U', 'Ě' => 'E', 'É' => 'E', 'Ç' => 'C', 'Ẁ' => 'W', 'Ċ' => 'C', 'Õ' => 'O',
      'Ṡ' => 'S', 'Ø' => 'O', 'Ģ' => 'G', 'Ŧ' => 'T', 'Ș' => 'S', 'Ė' => 'E', 'Ĉ' => 'C',
      'Ś' => 'S', 'Î' => 'I', 'Ű' => 'U', 'Ć' => 'C', 'Ę' => 'E', 'Ŵ' => 'W', 'Ṫ' => 'T',
      'Ū' => 'U', 'Č' => 'C', 'Ö' => 'O', 'È' => 'E', 'Ŷ' => 'Y', 'Ą' => 'A', 'Ł' => 'L',
      'Ų' => 'U', 'Ů' => 'U', 'Ş' => 'S', 'Ğ' => 'G', 'Ļ' => 'L', 'Ƒ' => 'F', 'Ž' => 'Z',
      'Ẃ' => 'W', 'Ḃ' => 'B', 'Å' => 'A', 'Ì' => 'I', 'Ï' => 'I', 'Ḋ' => 'D', 'Ť' => 'T',
      'Ŗ' => 'R', 'Ä' => 'A', 'Í' => 'I', 'Ŕ' => 'R', 'Ê' => 'E', 'Ü' => 'U', 'Ò' => 'O',
      'Ē' => 'E', 'Ñ' => 'N', 'Ń' => 'N', 'Ĥ' => 'H', 'Ĝ' => 'G', 'Đ' => 'D', 'Ĵ' => 'J',
      'Ÿ' => 'Y', 'Ũ' => 'U', 'Ŭ' => 'U', 'Ư' => 'U', 'Ţ' => 'T', 'Ý' => 'Y', 'Ő' => 'O',
      'Â' => 'A', 'Ľ' => 'L', 'Ẅ' => 'W', 'Ż' => 'Z', 'Ī' => 'I', 'Ã' => 'A', 'Ġ' => 'G',
      'Ṁ' => 'M', 'Ō' => 'O', 'Ĩ' => 'I', 'Ù' => 'U', 'Į' => 'I', 'Ź' => 'Z', 'Á' => 'A',
      'Û' => 'U', 'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae', 'Ĕ' => 'E');
            }
            
            $string = str_replace(array_keys($UTF8_UPPER_ACCENTS), array_values($UTF8_UPPER_ACCENTS), $string);
        }

        return $string;

    }
    
    static function stripNewLines($s){
        return preg_replace("/[\n\r]/m", " ", $s);
    }
    
	static function toSlug($normal_string, $clean_non_ascii=false){
	
		$page_name = strtolower($normal_string);
		
		if($clean_non_ascii && !self::isAscii($page_name)){
		    $page_name = self::toAscii($page_name);
		}
		
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w-]+/", "-", $page_name);
		return $page_name;
	
	}
	
	static function toValidDomain($normal_string, $clean_non_ascii=false){
	
		$page_name = strtolower($normal_string);
		
		if($clean_non_ascii && !self::isAscii($page_name)){
		    $page_name = self::toAscii($page_name);
		}
		
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w-]+/", ".", $page_name);
		return $page_name;
	
	}
	
	static function toVarName($normal_string, $clean_non_ascii=false){
	
		$page_name = strtolower($normal_string);
		
		if($clean_non_ascii && !self::isAscii($page_name)){
		    $page_name = self::toAscii($page_name);
		}
		
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w_-]+/", "_", $page_name);
		return $page_name;
	
	}
	
	static function toConstantName($string, $clean_non_ascii=false){
		
		$constant_name = trim($string, " ?!%$#&£*|/\\-");
		
		if($clean_non_ascii && !self::isAscii($constant_name)){
		    $constant_name = self::toAscii($constant_name);
		}
		
		$constant_name = preg_replace("/[\"'\.,]+/", "", $constant_name);
		$constant_name = preg_replace("/[^\w-_]+/", "_", $constant_name);
		$constant_name = strtoupper($constant_name);
    	
    	return $constant_name;
	}
	
	static function toCamelCase($string, $omitfirst=false){
		// takes a string, splits it by -, _, or '', and returns it lowercase with the first letter of each 'word' being uppercase
		$string = self::toSlug($string);
		$words = preg_split('/[_-]/', $string);
		$final = '';
		
		$i = 0;
		
		foreach($words as $word){
			$word = strtolower($word);
			
			if($omitfirst == true && $i == 0){
			    
		    }else{
		        $word = self::capitalizeFirstLetter($word);
		    }
			
			$final .= $word;
			$i++;
		}
		
		return $final;
		
	}
	
	
	static function toQueryString($array, $escapeAmpersand=false){
	    if(is_array($array)){
	        
	        $i = 0;
	        $string = '';
	        $amp = $escapeAmpersand ? '&amp;' : '&';
	        
	        foreach($array as $key => $value){
	            
	            if($i > 0){
	                $string .= $amp;
	            }
	            
	            $string .= $key .'='.$value;
	            
	            $i++;
	        }
	        
	        return $string;
	    }
	}
	
	static function capitalizeFirstLetter($string){
	    $string{0} = strtoupper($string{0});
	    return $string;
	}
	
	static function toTitleCase($string){
	    
        $non_capitalised_words = array('to', 'the', 'and', 'in', 'of', 'with', 'a', 'an');
        $words = explode(' ', $string);
        $new_string = '';
        
        foreach($words as $key=>$word){
            if($key = 0){
                $new_string = $word;
            }else{
                if(in_array($word, $non_capitalised_words)){
                    $new_string .= ' '.$word;
                }else{
                    $new_string .= ' '.self::capitalizeFirstLetter($word);
                }
            }
        }
        
        return $new_string;
	}
	
	static function toHash($string, $length=32, $type='MD5'){
	    
	    if($type == 'SHA1'){
	        $hash = sha1($string);
	    }else{
	        $hash = md5($string);
	    }
	    
	    return substr($hash, 0, $length);
	}
	
	static function isMd5Hash($string){
		if(preg_match("/^[0-9a-f]{32}$/", $string)){ // if
			return true;
		}else{
			return false;
		}
	}
	
	static function isFalse($string){
	    if(strlen($string) == 0 || in_array(strtolower($string), array('false', 'off', '0'))){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	static function toRealBool($string){
	    if($string){
	        return self::isFalse($string) ? false : true;
        }else{
            return false;
        }
	}
	
	static function endsWith($word, $symbol){
	    if(mb_strlen($word)){
	        $pos = (mb_strlen($word) - 1);
	        if($word{$pos} == $symbol){
	            return true;
	        }else{
	            return false;
	        }
	    }else{
	        return false;
	    }
	}
	
	static function startsWith($word, $symbol){
	    if(mb_strlen($word)){
	        $pos = 0;
	        if($word{$pos} == $symbol){
	            return true;
	        }else{
	            return false;
	        }
	    }else{
	        return false;
	    }
	}
	
	static function getDotSuffix($filename){
	    
	    $file = end(explode('/', $filename));
	    
	    if(strpos($file, '.') === false){
	        return null;
	    }else{
	        return end(explode('.', $file));
	    }
	}
	
	static function removeDotSuffix($filename){
	    
	    $dot_pieces = explode('.', $filename);
	    
	    if(count($dot_pieces) < 2){
	        return null;
        }else{
            $suffix = end($dot_pieces);
            $ns = ((mb_strlen($suffix)+1) * -1);
            $result = mb_substr($filename, 0, $ns);
            return $result;
        }
	}
	
	// now deprecated
	static function sanitizeFileContents($string){
	    
	    return self::sanitize($string);
	    
	}
	
	// protects the database
	static function sanitize($string){
	    
	    if(is_string($string)){
	        $string = str_replace('<?php', '', $string);
	        $string = str_replace('DELETE FROM', '', $string);
	        $string = str_replace('DROP TABLE', '', $string);
	        $string = str_replace('DROP DATABASE', '', $string);
        }
	    
	    return $string;
	    
	}
	
	static function toSensibleFileName($string){
	    $suffix = self::getDotSuffix($string);
	    $base = self::removeDotSuffix($string);
	    $base = preg_replace("/[\s\.]/", '_', $base);
	    return $base.'.'.$suffix;
	}
	
	static function toSummary($string, $char_length=300){
	    // find the end of first paragraph and cut there
	    preg_match_all('/<p[^>]*>(.+?)<\/p>/mi', self::stripNewLines($string), $paragraphs);
      
        if(count($paragraphs[0])){
	        $first_paragraph = $paragraphs[1][0];
	    }else{
	        $first_paragraph = $string;
	    }
	    
	    // strip tags
	    $final_string = strip_tags($first_paragraph);
	    
	    // if it is longer that $char_length, truncate it and add '...'
	    if(strlen($final_string) > $char_length){
	        $final_string = substr($final_string, 0, ($char_length - 3)).'...';
	    }
	    
	    return trim($final_string);
	    
	}
	
	static function isEmailAddress($string){
		return preg_match('/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i', $string);
	}
	
	static function isValidExternalUri($string){
		return preg_match('/^https?:\/\//i', $string);
	}
	
	static function getRelatedWords($word){
		// query wikipedia
		
	}
	
	static function parseNameValueString($string){
	    
	    $pairs = explode(';', $string);
	    $data = array();
	    
	    foreach($pairs as $nvp){
	        $parts = explode(':', $nvp);
	        $data[trim($parts[0])] = trim($parts[1]);
	    }
	    
	    return $data;
	}
	
	
	static function toNameValueString($array){
	    if(!is_array($array)){
	        return '0:'.$array;
	    }else{
	        $string = '';
	        
	        foreach($array as $key=>$value){
	            $string .= $key.':'.$value.';';
	        }
	        
	        return $string;
	    }
	}
	
	/* static function parseAttributeString($string){
	    
	    $pairs = explode(';', $string);
	    $data = array();
	    
	    foreach($pairs as $nvp){
	        $parts = explode(':', $nvp);
	        $data[trim($parts[0])] = trim($parts[1]);
	    }
	    
	    return $data;
	} */
	
	
	static function toAttributeString($array){
	    if(!is_array($array)){
	        return '';
	    }else{
	        
	        foreach($array as $key=>$value){
	            $string .= ' '.$key.'="'.$value.'"';
	        }
	        
	        return $string;
	    }
	}
	
	static function toHtmlEntities($string){
    	return htmlentities($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    static function toXmlEntities($string){
    	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    static function protectSmartestTags($string){
        
        $string = preg_replace('/([\w_]+)=&(amp;)?quot;([\w\s\.:_-]*)&(amp;)?quot;/i', '$1="$3"', $string);
        
        $string = str_replace('<?sm:', '<!--PROTECTED-SMARTEST-TAG:', $string);
        $string = str_replace('&lt;?sm:', '<!--PROTECTED-SMARTEST-TAG:', $string);
        $string = str_replace(':?>', ':PROTECTED-SMARTEST-TAG-->', $string);
        $string = str_replace(':?&gt;', ':PROTECTED-SMARTEST-TAG-->', $string);
        
        return $string;
    }
    
    static function unProtectSmartestTags($string){
        
        $string = preg_replace('/([\w_]+)=&(amp;)?quot;([^&]*)&(amp;)?quot;/i', '$1="$3"', $string);
        
        $string = str_replace('<p><!--PROTECTED-SMARTEST-TAG:', '<?sm:', $string);
        $string = str_replace(':PROTECTED-SMARTEST-TAG--></p>', ':?>', $string);
        $string = str_replace('<!--PROTECTED-SMARTEST-TAG:', '<?sm:', $string);
        $string = str_replace(':PROTECTED-SMARTEST-TAG-->', ':?>', $string);
        
        return $string;
    }
    
    static function separateParagraphs($string){
        $string = str_replace('</p><p', "</p>\n\n<p", $string);
        return $string;
    }
    
    static function toRegularExpression($string, $add_slashes=false){
	    $regexp = str_replace('/', '\/', $string);
	    $regexp = str_replace('|', '\|', $regexp);
		$regexp = str_replace('+', '\+', $regexp);
		$regexp = str_replace('(', '\(', $regexp);
		$regexp = str_replace(')', '\)', $regexp);
		$regexp = str_replace('[', '\[', $regexp);
		$regexp = str_replace(']', '\]', $regexp);
		$regexp = str_replace('{', '\{', $regexp);
		$regexp = str_replace('}', '\}', $regexp);
		$regexp = str_replace('.', '\.', $regexp);
		$regexp = str_replace('?', '\?', $regexp);
		$regexp = str_replace('$', '\$', $regexp);
		$regexp = str_replace('^', '\^', $regexp);
		$regexp = str_replace("\x39", "\x39\x39", $regexp);
		
		if($add_slashes){
		    return '/'.$regexp.'/';
		}else{
		    return $regexp;
		}
	}
	
	static function toCommaSeparatedList($array){
	    
	    if(is_array($array)){
	        
	        $num_words = count($array);
	        $last_index = $num_words - 1;
	        $string = "";
	        
	        foreach(array_values($array) as $key => $word){
	            
	            if($key > 0){
	                if($key == $last_index){
	                    $string .= ' and ';
	                }else{
	                    $string .= ', ';
	                }
	            }
	            
	            $string .= $word;
	            
	        }
	        
	        return $string;
	        
	    }else{
	        
	        return $array;
	        
	    }
	    
	}

}
