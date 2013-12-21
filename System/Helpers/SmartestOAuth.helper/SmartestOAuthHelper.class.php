<?php

class SmartestOAuthHelper{
    
    private $database;
    
    public function __construct(){
        $this->database = SmartestDatabase::getInstance('SMARTEST');
    }
    
    public static function getServices(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/oauth_services.yml');
        return $data['services'];
        
    }
    
    public function getAccounts(){
        
        $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' ORDER BY user_firstname");
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestSystemUser;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
}