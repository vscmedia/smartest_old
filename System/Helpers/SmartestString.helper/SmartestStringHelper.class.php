<?php

SmartestHelper::register('String');

define("SM_OPTIONS_MAGIC_QUOTES", (bool) ini_get('magic_quotes_gpc'));

require_once(SM_ROOT_DIR.'Library/Textile/classTextile.php');

class SmartestStringHelper extends SmartestHelper{
    
    const EMAIL_ADDRESS = '/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i';
    
    public static function convertObject($data){
        if(is_object($data)){
            if($data instanceof SmartestRenderableAsset){
                return $data->render();
            }elseif(method_exists($data, '__toString')){
                return $data->__toString();
            }else{
                throw new SmartestException('Tried to convert non-convertible object to string');
            }
        }else{
            return $data;
        }
    }
    
	public static function random($size){
	
		$possValues = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s"
			    , "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", 
			    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "$");
		$stem = "";
	
		for($i=0; $i<$size; $i++){
			$randNum = rand(0, count($possValues)-1);
			$shoot = $possValues[$randNum];
			$plant = $stem.$shoot;
			$stem = $plant;
		}	
	
		return $plant;
	
	}
	
	public static function randomFromFormat($format){
	    
	    $uppercase_letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	    $lowercase_letters = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	    $hex_letters = array("a", "b", "c", "d", "e", "f");
	    $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	    $symbols = array("-", "*", "^", "!", "(", ")");
	    
	    $all_letters = array_merge($uppercase_letters, $lowercase_letters);
	    $random_chars = array_merge($all_letters, $numbers);
	    $all_hex = array_merge($hex_letters, $numbers);
	    
	    $all = array_merge($random_chars, $symbols);
	    
	    $l = strlen($format);
	    $s = '';
	    
	    for($i=0;$i<$l;$i++){
	        $c = $format{$i};
	        
	        if($c == "L"){
	            $nl = $uppercase_letters[rand(0, count($uppercase_letters)-1)];
	        }else if($c == "l"){
	            $nl = $lowercase_letters[rand(0, count($lowercase_letters)-1)];
	        }else if($c == "a" || $c == "A"){
	            $nl = $all_letters[rand(0, count($all_letters)-1)];
	        }else if($c == "h" || $c == "H"){
	            $nl = $all_hex[rand(0, count($all_hex)-1)];
	        }else if($c == "n" || $c == "N"){
    	        $nl = $numbers[rand(0, 9)];
    	    }else if($c == "r" || $c == "R"){
    	        $nl = $random_chars[rand(0, count($random_chars)-1)];
    	    }else if($c == "s" || $c == "S"){
    	        $nl = $symbols[rand(0, count($symbols)-1)];
    	    }else if($c == "*"){
    	        $nl = $all[rand(0, count($all)-1)];
    	    }else{
    	        $nl = $c;
    	    }
    	    
    	    $s .= $nl;
	        
	    }
	    
	    return $s;
	    
	}
	
	public static function generateUUID(){
	    
	    $hex = md5(microtime(true).self::random(12));
	    $digits = array('a','b',8,9);
	    $hex{12} = 4;
	    $hex{16} = $digits[rand(0,3)];
	    return substr($hex, 0, 8).'-'.substr($hex, 8, 4).'-'.substr($hex, 12, 4).'-'.substr($hex, 16, 4).'-'.substr($hex, 20, 12);
	    
	}
	
	public static function isAscii(){
	    return (preg_match('/(?:[^\x00-\x7F])/',$str) !== 1);
	}
	
	public static function toAscii($string){

        static $UTF8_LOWER_ACCENTS = NULL;
        static $UTF8_UPPER_ACCENTS = NULL;

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

        $string = str_replace(array_keys($UTF8_LOWER_ACCENTS), array_values($UTF8_LOWER_ACCENTS), $string);

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
            
        $string = str_replace(array_keys($UTF8_UPPER_ACCENTS), array_values($UTF8_UPPER_ACCENTS), $string);

        return $string;

    }
    
    public static function stripNewLines($s){
        $s = self::convertObject($s);
        $s = str_replace("\n", " ", $s);
        $s = str_replace("\r", " ", $s);
        return $s;
    }
    
	public static function toSlug($page_name, $clean_non_ascii=false){
	
		if($clean_non_ascii){
		    $page_name = self::toAscii($page_name);
		}
		
		$page_name = strtolower($page_name);
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = str_replace(' - ', '-', $page_name);
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w-]+/", "-", $page_name);
		return $page_name;
	
	}
	
	public static function toUsername($normal_string, $clean_non_ascii=false){
	
		$page_name = self::toAscii(strtolower($normal_string));
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = preg_replace("/[^\w\._]+/", "", $page_name);
		return $page_name;
	
	}
	
	public static function toValidDomain($normal_string, $clean_non_ascii=false){
	
		$page_name = strtolower($normal_string);
		
		if($clean_non_ascii && !self::isAscii($page_name)){
		    $page_name = self::toAscii($page_name);
		}
		
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w-]+/", ".", $page_name);
		return $page_name;
	
	}
	
	public static function toValidExternalUrl($url){
	    
	    if(!strlen(trim($url, ' /.,:htps'))) return '';
	    
	    if(preg_match('/^((htt?ps?):\/\/?\/?)?(.+)\/?$/', $url, $matches)){
            
            if(isset($matches[2]{4})){
                return 'https://'.$matches[3];
            }else{
                return 'http://'.$matches[3];
            }
        
        }else{
            
            return 'http://'.$url;
            
        }
	    
	}
	
	public static function toUrlStringWithoutProtocol($url){
	    
	    $url = self::toValidExternalUrl($url);
	    preg_match('/^((htt?ps?):\/\/?)?(.+)\/?$/', $url, $matches);
	    return $matches[3];
	    
	}
	
	public static function toVarName($normal_string, $clean_non_ascii=false){
	    
	    if(is_object($normal_string)){
		    $was_object = true;
		    $normal_string = self::convertObject($normal_string);
		}else{
		    $was_object = false;
		}
	    
		$page_name = strtolower($normal_string);
		
		if($clean_non_ascii && !self::isAscii($page_name)){
		    $page_name = self::toAscii($page_name);
		}
		
		$page_name = trim($page_name, " ?!%$#&£*|()/\\-");
		$page_name = preg_replace('/\s&(amp;)?\s/', '_and_', $page_name);
		$page_name = preg_replace("/[\"'\.,\(\)]+/", "", $page_name);
		$page_name = preg_replace("/[^\w_]+/", "_", $page_name);
		
		return $was_object ? new SmartestString($page_name) : $page_name;
	
	}
	
	public static function toConstantName($string, $clean_non_ascii=false){
		
		if(is_object($string)){
		    $was_object = true;
		    $string = self::convertObject($string);
		}else{
		    $was_object = false;
		}
		
		$constant_name = trim($string, " ?!%$#&£*|/\\-");
		
		if($clean_non_ascii){
		    $constant_name = self::toAscii($constant_name);
		}
		
		$constant_name = preg_replace("/[\"'\.,]+/", "", $constant_name);
		$constant_name = preg_replace("/[^\w_]+/", "_", $constant_name);
		$constant_name = strtoupper($constant_name);
    	
    	return $was_object ? new SmartestString($constant_name) : $constant_name;
    	
	}
	
	public static function toCamelCase($string, $omitfirst=false){
	    
	    if(is_object($string)){
		    $was_object = true;
		    $string = self::convertObject($string);
		}else{
		    $was_object = false;
		}
		
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
		
		return $was_object ? new SmartestString($final) : $final;
		
	}
	
	
	public static function toQueryString($array, $escapeAmpersand=false){
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
	
	public static function parseQueryString($string, $htmlAmpersand=false){
	    
	    if($string){
	        
	        $amp = $htmlAmpersand ? '&amp;' : '&';
	        
	        $pairs = explode($amp, $string);
    	    $data = array();

    	    foreach($pairs as $nvp){
    	        $parts = explode('=', $nvp);
    	        $data[trim($parts[0])] = trim($parts[1]);
    	    }
	        
	        return $data;
	        
	    }else{
	        return array();
	    }
	}
	
	public static function capitalizeFirstLetter($string){
	    if(strlen($string)){
	        $string{0} = strtoupper($string{0});
	        return $string;
        }else{
            return '';
        }
	}
	
	public static function toTitleCase($string, $strict=false){
	    
	    if(is_object($string)){
		    $was_object = true;
		    $string = self::convertObject($string);
		}else{
		    $was_object = false;
		}
	    
        $non_capitalised_words = array('to', 'the', 'and', 'in', 'of', 'with', 'a', 'an');
        $words = explode(' ', $string);
        
        $new_string = '';
        $modified_words = array();
        
        foreach($words as $key=>$word){
            
            if($strict){
                $word = strtolower($word);
            }
            
            if(in_array($word, $non_capitalised_words) && $k!=0){
                $modified_words[] = $word;
            }else{
                $modified_words[] = self::capitalizeFirstLetter($word);
            }
                
        }
        
        $final = trim(implode(' ', $modified_words));
        return $was_object ? new SmartestString($final) : $final;
	}
	
	public static function toTitleCaseFromVarName($string){
	  
	    return self::toTitleCase(str_replace('_', ' ', $string));
	  
	}
	
	public static function toTitleCaseFromFileName($string){
	  
	    return self::toTitleCase(preg_replace('/[-_]/', ' ', $string));
	  
	}
	
	public static function toHexUrlEncoded($string){
	    
	    for ($c = 0; $c < strlen($string); $c++) {
            if(preg_match('!\w!', $string{$c})) {
                $string_encode .= '%'.bin2hex($string{$c});
            }else{
                $string_encode .= $string{$c};
            }
        }
        
        return $string_encode;
	}
	
	public static function toHtmlEncoded($string){
	    
	    for ($c = 0; $c < strlen($string); $c++) {
            if(preg_match('!\w!', $string{$c})) {
                $string_encode .= '&#x'.bin2hex($string{$c}).';';
            }else{
                $string_encode .= $string{$c};
            }
        }
        
        return $string_encode;
	}
	
	public static function toHash($string, $length=32, $type='MD5'){
	    
	    if($type == 'SHA1'){
	        $hash = sha1($string);
	    
	    }else{
	        $hash = md5($string);
	    }
	    
	    return substr($hash, 0, $length);
	}
	
	public static function toHmacSha1($key, $data){
	  
      // Adjust key to exactly 64 bytes
      if (strlen($key) > 64) {
          $key = str_pad(sha1($key, true), 64, chr(0));
      }
      
      if (strlen($key) < 64) {
          $key = str_pad($key, 64, chr(0));
      }

      // Outter and Inner pad
      $opad = str_repeat(chr(0x5C), 64);
      $ipad = str_repeat(chr(0x36), 64);

      // Xor key with opad & ipad
      for ($i = 0; $i < strlen($key); $i++) {
          $opad[$i] = $opad[$i] ^ $key[$i];
          $ipad[$i] = $ipad[$i] ^ $key[$i];
      }

      return sha1($opad.sha1($ipad.$data, true));
  }
	
	public static function isMd5Hash($string){
		if(preg_match("/^[0-9a-f]{32}$/", $string)){ // if
			return true;
		}else{
			return false;
		}
	}
	
	public static function isFalse($string){
	    if(strlen($string) == 0 || in_array(strtolower($string), array('false', 'off', '0', 'none')) || $string === false){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public static function toRealBool($string){
	    
	    $string = self::convertObject($string);
	    
	    if($string){
	        return self::isFalse($string) ? false : true;
        }else{
            return false;
        }
	}
	
	public static function endsWith($word, $symbol){
	    
	    $string = self::convertObject($s);
	    
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
	
	public static function startsWith($word, $symbol){
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
	
	public static function getDotSuffix($filename){
	    
	    $file = end(explode('/', $filename));
	    
	    if(strpos($file, '.') === false){
	        return null;
	    }else{
	        return end(explode('.', $file));
	    }
	}
	
	public static function removeDotSuffix($filename){
	    
	    $dot_pieces = explode('.', $filename);
	    
	    if(count($dot_pieces) < 2){
	        return $filename;
        }else{
            $suffix = end($dot_pieces);
            $ns = ((mb_strlen($suffix)+1) * -1);
            $result = mb_substr($filename, 0, $ns);
            return $result;
        }
	}
	
	// now deprecated
	public static function sanitizeFileContents($string){
	    
	    return self::sanitize($string);
	    
	}
	
	// protects the database
	public static function sanitize($string){
	    
	    if(is_string($string)){
	        $string = str_replace('<?php', '', $string);
	        $string = str_replace('DELETE FROM', '', $string);
	        $string = str_replace('DROP TABLE', '', $string);
	        $string = str_replace('DROP DATABASE', '', $string);
        }
	    
	    return $string;
	    
	}
	
	public static function toSensibleFileName($string){
	    $suffix = self::getDotSuffix($string);
	    $base = self::removeDotSuffix($string);
	    $base = preg_replace("/[\s\.]/", '_', $base);
	    return $base.'.'.$suffix;
	}
	
	public static function getFirstParagraph($string){
	    
	    $paras = self::getParagraphs($string);
	    return $paras[0];
	    
	}
	
	public static function getParagraphs($string){
	    
	    $string = str_replace('<br /><br />', "</p>\n<p>", $string);
	    preg_match_all('/<p[^>]*>(.+?)<\/p>/mi', self::stripNewLines($string), $paragraphs);
	    
	    if(count($paragraphs[0])){
	        return $paragraphs[1];
	    }else{
	        return array($string);
	    }
	    
	}
	
	public static function toSummary($string, $char_length=300){
	    
	    /* $string = str_replace('<br /><br />', "</p>\n<p>", $string);
	    
	    // find the end of first paragraph and cut there
	    preg_match_all('/<p[^>]*>(.+?)<\/p>/mi', self::stripNewLines($string), $paragraphs);

        if(count($paragraphs[0])){
	        $first_paragraph = $paragraphs[1][0];
	    }else{
	        $first_paragraph = $string;
	    } */
	    
	    // strip tags
	    // $final_string = strip_tags($first_paragraph);
	    $final_string = strip_tags(self::getFirstParagraph($string));
	    
	    // if it is longer that $char_length, truncate it and add '...'
	    if(strlen($final_string) > $char_length){
	        $final_string = substr($final_string, 0, ($char_length - 3)).'...';
	    }
	    
	    return trim($final_string);
	    
	}
	
	public static function isEmailAddress($string){
		return preg_match(self::EMAIL_ADDRESS, $string);
	}
	
	public static function isValidExternalUri($string){
		return preg_match('/^https?:\/\//i', $string);
	}
	
	public static function parseNameValueString($string){
	    
	    $pairs = preg_split('/\s*;\s*/', $string);
	    $data = array();
	    
	    foreach($pairs as $key=>$nvp){
	        $parts = preg_split('/\s*:\s*/', $nvp);
	        
	        if(strlen($parts[0])){
	            $data[trim($parts[0])] = trim($parts[1]);
            }
	    }
	    
	    return $data;
	}
	
	
	public static function toNameValueString($array){
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
	
	public static function toAttributeString($array){
	    if(!is_array($array)){
	        return '';
	    }else{
	        
	        foreach($array as $key=>$value){
	            $string .= ' '.$key.'="'.$value.'"';
	        }
	        
	        return $string;
	    }
	}
	
	public static function toUrlParameterString($array, $encode_values=true){
	    if(!is_array($array)){
	        return '';
	    }else{
	        
	        $i = 0;
	        
	        foreach($array as $key=>$value){
	            
	            if($i > 0) $string .= '&';
	            
	            if($encode_values){
	                $string .= $key.'='.urlencode($value);
                }else{
                    $string .= $key.'='.$value;
                }
              
	            $i++;
	        }
	        
	        return $string;
	    }
	}
	
	public static function toHtmlEntities($string){
    	return htmlentities($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    public static function toXmlEntities($string){
    	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    public static function protectSmartestTags($string){
        
        $string = preg_replace('/([\w_]+)=&(amp;)?quot;([\w\s\.:_-]*)&(amp;)?quot;/i', '$1="$3"', $string);
        
        $string = str_replace('<?sm:', '<!--PROTECTED-SMARTEST-TAG:', $string);
        $string = str_replace('&lt;?sm:', '<!--PROTECTED-SMARTEST-TAG:', $string);
        $string = str_replace(':?>', ':PROTECTED-SMARTEST-TAG-->', $string);
        $string = str_replace(':?&gt;', ':PROTECTED-SMARTEST-TAG-->', $string);
        
        return $string;
    }
    
    public static function unProtectSmartestTags($string){
        
        $string = preg_replace('/([\w_]+)=&(amp;)?quot;([^&]*)&(amp;)?quot;/i', '$1="$3"', $string);
        
        $string = str_replace('<p><!--NewColumn--></p>', '<!--NewColumn-->', $string);
        
        $string = str_replace('<p><!--PROTECTED-SMARTEST-TAG:', '<?sm:', $string);
        $string = str_replace(':PROTECTED-SMARTEST-TAG--></p>', ':?>', $string);
        $string = str_replace('<!--PROTECTED-SMARTEST-TAG:', '<?sm:', $string);
        $string = str_replace(':PROTECTED-SMARTEST-TAG-->', ':?>', $string);
        
        return $string;
    }
    
    public static function separateParagraphs($string){
        $string = str_replace('</p><p', "</p>\n\n<p", $string);
        return $string;
    }
    
    public static function toParagraphs($string, $classes=''){
        
        $parts = preg_split('/[\r\n\t]+/', $string);
        
        if(strlen($classes)){
            $open_tag = '<p class="'.$classes.'">';
        }else{
            $open_tag = '<p>';
        }
        
        return $open_tag.implode("</p>\r\n".$open_tag, $parts).'</p>';
    }
    
    public static function toRegularExpression($string, $add_slashes=false){
	    
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
	
	public static function toCommaSeparatedList($array, $grammatical=true){
	    
	    if(is_array($array)){
	        
	        $num_words = count($array);
	        $last_index = $num_words - 1;
	        $string = "";
	        
	        foreach(array_values($array) as $key => $word){
	            
	            if($key > 0){
	                if($key == $last_index && $grammatical){
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
	
	public static function fromCommaSeparatedList($string){
	    return preg_split('/[\s]*[,][\s]*/', $string);
	}
	
	public static function spanify($string, $spaces=false){
	    $s = $spaces ? ' ' : '';
	    $parts = explode(' ', $string);
        return '<span>'.implode('</span>'.$s.'<span>', $parts).'</span>';
	}
	
	public static function guaranteeUnique($string, $taken_strings, $separator='-'){
	    
	    if(!is_array($taken_strings)){
	        SmartestLog::getInstance('system')->log('SmartestStringHelper::guaranteeUnique() called with invalid data type for $taken_strings argument (2)', SmartestLog::WARNING);
	        return false;
	    }
	    
	    if(in_array($string, $taken_strings)){
	        
	        if(preg_match('/(.+)'.self::toRegularExpression($separator).'(\d+)$/', $string, $matches)){
    	        $number = $matches[2];
    	        $trunk = $matches[1];
    	        $try_string = $string;
    	    }else{
	            $trunk = $string;
	            $try_string = $string.$separator.'1';
    	    }
    	    
    	    $max_tries = 10000;
            $counter = 1;
            
            while($counter < $max_tries && in_array($try_string, $taken_strings)){
                
                $counter++;
                $try_string = $trunk.$separator.$counter;
                
            }
            
            return $try_string;
    	    
	    }else{
	        return $string;
	    }
	    
	}
	
	public static function parseEmailAddresses($content){
	    
	    preg_match_all(self::EMAIL_ADDRESS, $content, $matches);
	    
	}
	
	public static function parseTextile($content){
	    
	    $content = str_replace(' (R)', ' ®', $content);
        $content = str_replace(' (C)', ' ©', $content);
	    
	    $textile = new Textile();
        $content = $textile->TextileThis($content);
        $content = str_replace('<3', '♥', $content);
        
        return $content;
	    
	}
	
	public static function parseTextileIntoColumns($content){
	    
	    $text = str_ireplace('~~NewColumn~~', '~~NewColumn~~', $content);
	    $columns = preg_split('/~~NewColumn~~/i', $text);
	    $num_columns = count($columns);
	    
	    if($num_columns > 1){
	        
	        $newtext = '';
	        $column_open = '<div class="smartest-column column-width-'.$num_columns.'">';
	        $last_column_open = '<div class="smartest-column column-width-'.$num_columns.' last">';
	        $column_close = "</div>\n";
	        $i = 1;
	        
	        foreach($columns as $c){
	            if($i<$num_columns){
	                $newtext .= $column_open.self::parseTextile($c).$column_close;
                }else{
                    $newtext .= $last_column_open.self::parseTextile($c).$column_close;
                }
	            ++$i;
	        }
	        
	        return $newtext;
	        
	    }else{
	        return $content;
	    }
	    
	}
	
	public static function separateIntoColumns($text){
	    
	    $text = str_ireplace('<p><!--NewColumn--></p>', '<!--NewColumn-->', $text);
	    $columns = preg_split('/<!--NewColumn-->/i', $text);
	    $num_columns = count($columns);
	    
	    if($num_columns > 1){
	        
	        $newtext = '';
	        $column_open = '<div class="smartest-column column-width-'.$num_columns.'">';
	        $last_column_open = '<div class="smartest-column column-width-'.$num_columns.' last">';
	        $column_close = "</div>\n";
	        $i = 1;
	        
	        foreach($columns as $c){
	            if($i<$num_columns){
	                $newtext .= $column_open.$c.$column_close;
                }else{
                    $newtext .= $last_column_open.$c.$column_close;
                }
	            ++$i;
	        }
	        
	        return $newtext;
	        
	    }else{
	        return $text;
	    }
	    
	}
	
	public static function getWordCount($string){
	    return count(preg_split('/\s+/', strip_tags($string)));
	}

}
