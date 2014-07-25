<?php

class SmartestCookiesHelper{
    
    public static function getCookie($name){
        
        if(isset($_COOKIE[$name])){
            return urldecode($_COOKIE[$name]);
        }else{
            return null;
        }
        
    }
    
    public static function cookieExists($name){
        return isset($_COOKIE[$name]);
    }
    
    public static function setCookie($name, $value, $duration=30, $domain='_C', $secure=false){ // default duration is 30 days
        
        $expire = time() + 86400 * (int) $duration; // 86400 is the number of seconds in one day
        $domain = ($domain == '_C') ? $_SERVER['HTTP_HOST'] : $domain;
        return setcookie($name, $value, $expire, SmartestPersistentObject::get('controller')->getCurrentRequest()->getDomain(), $domain, (bool) $secure);
        
    }
    
    public static function clearCookie($name, $domain='_C', $secure=false){
        
        // $expire = time() - 86400; // now, minus one day
        $domain = ($domain == '_C') ? $_SERVER['HTTP_HOST'] : $domain;
        return setcookie($name, '', 1000, SmartestPersistentObject::get('controller')->getCurrentRequest()->getDomain(), $domain, (bool) $secure);
        
    }
    
}