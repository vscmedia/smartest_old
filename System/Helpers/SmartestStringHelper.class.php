<?php

SmartestHelper::register('String');

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

	static function toSlug($normal_string){
	
		$page_name = strtolower($normal_string);
		$page_name = trim($page_name, " ?!%$#&£*|()/\\");
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w-]+/", "-", $page_name);
		return $page_name;
	
	}
	
	static function toVarName($normal_string){
	
		$page_name = strtolower($normal_string);
		$page_name = trim($page_name, " ?!%$#&£*|()/\\");
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w_-]+/", "_", $page_name);
		return $page_name;
	
	}
	
	static function toConstantName($string){
		
		$constant_name = trim($string, " ?!%$#&£*|/\\");
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
	    if(in_array(strtolower($string), array('false', 'off', '0'))){
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
	
	static function sanitizeFileContents($string){
	    
	    $string = str_replace('<?php', '', $string);
	    $string = str_replace('DELETE FROM', '', $string);
	    $string = str_replace('DROP TABLE', '', $string);
	    $string = str_replace('DROP DATABASE', '', $string);
	    
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
	    preg_match_all('/<p[^>]*>(.+?)<\/p>/', $string, $paragraphs);

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
	    
	    return $final_string;
	    
	}
	
	static function isEmailAddress($string){
		
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
	
	static function toHtmlEntities($string){
    	return htmlentities($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    static function toXmlEntities($string){
    	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    static function protectSmartestTags($string){
        
        $string = str_replace('<?sm:', '<!--PROTECTED-SMARTEST-TAG:', $string);
        $string = str_replace('&lt;?sm:', '<!--PROTECTED-SMARTEST-TAG:', $string);
        $string = str_replace(':?>', ':PROTECTED-SMARTEST-TAG-->', $string);
        $string = str_replace(':?&gt;', ':PROTECTED-SMARTEST-TAG-->', $string);
        
        return $string;
    }
    
    static function unProtectSmartestTags($string){
        
        $string = preg_replace('/([\w_]+)=&quot;([\w\s\._-]*)&quot;/i', '$1="$2"', $string);
        $string = str_replace('<p><!--PROTECTED-SMARTEST-TAG:', '<?sm:', $string);
        $string = str_replace(':PROTECTED-SMARTEST-TAG--></p>', ':?>', $string);
        $string = str_replace('<!--PROTECTED-SMARTEST-TAG:', '<?sm:', $string);
        $string = str_replace(':PROTECTED-SMARTEST-TAG-->', ':?>', $string);
        
        return $string;
    }

}