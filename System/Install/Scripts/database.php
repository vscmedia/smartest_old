#!/usr/bin/env php
<?php

define('SM_ROOT_DIR', getcwd().DIRECTORY_SEPARATOR);
define('SM_DEVELOPER_MODE', true);

require SM_ROOT_DIR."System/Base/SmartestException.class.php";
require SM_ROOT_DIR."System/Data/SmartestDataAccessClass.interface.php";
require SM_ROOT_DIR."System/Data/Types/SmartestParameterHolder.class.php";
require SM_ROOT_DIR."System/Data/SmartestMysql.class.php";
require SM_ROOT_DIR."System/Data/SmartestCache.class.php";
require SM_ROOT_DIR."System/Helpers/SmartestHelper.class.php";

SmartestHelper::loadAll();

function getDetailsFromUser(){
    
    $details = array();
    
    fwrite(STDOUT, "Please enter the MySQL USERNAME you want this Smartest Installation to use:\n");
    $smartest_username = trim(fread(STDIN, 1024));

    fwrite(STDOUT, "Please enter the MySQL PASSWORD you want this Smartest Installation to use:\n");
    $smartest_password = trim(fread(STDIN, 1024));

    fwrite(STDOUT, "Please enter the MySQL DATABASE you want this Smartest Installation to use:\n");
    $smartest_database = trim(fread(STDIN, 1024));

    fwrite(STDOUT, "Please enter the MySQL HOST you want this Smartest Installation to use (default: 'localhost'):\n");
    $smartest_host = trim(fread(STDIN, 1024));
    
    if(!strlen(trim($smartest_host))){
        $smartest_host = 'localhost';
    }
    
    fwrite(STDOUT, "\n* USERNAME:".$smartest_username."\n* PASSWORD:".$smartest_password."\n* DATABASE:".$smartest_database."\n* HOST:".$smartest_host."\n\n"."Are these details correct? (Y/n)");
    
    $correct = (strtolower(trim(fread(STDIN, 1024))) == 'n') ? false : true;
    
    if($correct){
        return array("u"=>$smartest_username, "p"=>$smartest_password, "d"=>$smartest_database, "h"=>$smartest_host);
    }else{
        return getDetailsFromUser();
    }
    
}

function getSmartestDetailsFromUser(){
    
    $details = array();
    
    $smartest_fullname = '';
    
    while(strlen(SmartestStringHelper::toVarName($smartest_firstname)) < 3){
        fwrite(STDOUT, "Please enter your FIRST NAME:\n");
        $smartest_firstname = trim(fread(STDIN, 1024));
    }
    
    fwrite(STDOUT, "Please enter your LAST NAME:\n");
    $smartest_lastname = trim(fread(STDIN, 1024));
    
    fwrite(STDOUT, "Please enter the USERNAME you would like your Smartest account to use:\n");
    $smartest_username = SmartestStringHelper::toVarName(trim(fread(STDIN, 1024)));
    
    if(!strlen(trim($smartest_username))){
        $smartest_username = SmartestStringHelper::toVarName($smartest_firstname.$smartest_lastname);
    }

    fwrite(STDOUT, "Please enter the PASSWORD you would like your Smartest account to use:\n");
    $smartest_password = trim(fread(STDIN, 1024));
    
    fwrite(STDOUT, "Please enter the CONTACT E-MAIL you would like your Smartest account to use:\n");
    $smartest_email = trim(fread(STDIN, 1024));
    
    fwrite(STDOUT, "Please enter the name of the [first] website you are going to create using Smartest:\n");
    $smartest_sitename = trim(fread(STDIN, 1024));

    fwrite(STDOUT, "Please enter the domain (no http://) of the [first] website you are going to create using Smartest:\n");
    $smartest_domain = trim(fread(STDIN, 1024));
    
    if(!strlen(trim($smartest_host))){
        $smartest_host = 'localhost';
    }
    
    fwrite(STDOUT, "\n* FULL NAME:".$smartest_firstname.' '.$smartest_lastname.
    "\n* USERNAME:".$smartest_username.
    "\n* E-MAIL:".$smartest_email.
    "\n* TITLE:".$smartest_sitename.
    "\n* DOMAIN:".$smartest_domain.
    "\n\n"."Are these details correct? (Y/n)");
    
    $correct = (strtolower(trim(fread(STDIN, 1024))) == 'n') ? false : true;
    
    if($correct){
        return array("f"=>$smartest_firstname, "l"=>$smartest_lastname, "u"=>$smartest_username, "p"=>$smartest_password, "e"=>$smartest_email, "n"=>$smartest_sitename, "d"=>$smartest_domain);
    }else{
        return getSmartestDetailsFromUser();
    }
    
}

if(!file_exists(SM_ROOT_DIR."Configuration/database.ini")){
    
    $details = getDetailsFromUser();
    
    if(file_exists(SM_ROOT_DIR."System/Install/Samples/database-sample.ini")){
        
        $config = file_get_contents(SM_ROOT_DIR."System/Install/Samples/database-sample.ini");
        $config = str_replace("__USERNAME__", $details['u'], $config);
        $config = str_replace("__PASSWORD__", $details['p'], $config);
        $config = str_replace("__DATABASE__", $details['d'], $config);
        $config = str_replace("__HOST__", $details['h'], $config);
        $config = str_replace("__NOW__", date("Y-m-d h:i:s"), $config);
        
        if(!file_put_contents(SM_ROOT_DIR."Configuration/database.ini", $config)){
            fwrite(STDOUT, "* ERROR: Could not write to ".SM_ROOT_DIR."Configuration/database.ini.\n");
            exit(0);
        }    
        
        $database = new SmartestMysql($details['h'], $details['u'], $details['d'], $details['p']);
        
        fwrite(STDOUT, "* Creating Smartest database structure from schema file...\n");
        
        $schema = file_get_contents("System/SqlScripts/schema.sql");
        $queries = explode(';', $schema);
        
        foreach($queries as $query){
            if(strlen(trim($query))){
                try{
                    $database->rawQuery(trim($query).';');
                }catch (SmartestException $e) {
                    continue;
                }
            }
        }
        
        fwrite(STDOUT, "* Table structure complete. Now what about you?\n");
        
        $setup = file_get_contents(SM_ROOT_DIR."System/SqlScripts/setup.sql");
        $install_details = getSmartestDetailsFromUser();
        
        $setup = str_replace("__FIRSTNAME__", $install_details['f'], $setup);
        $setup = str_replace("__LASTNAME__", $install_details['l'], $setup);
        $setup = str_replace("__USERNAME__", $install_details['u'], $setup);
        $setup = str_replace("__PASSWORD__", md5($install_details['p']), $setup);
        $setup = str_replace("__EMAIL__", $install_details['e'], $setup);
        $setup = str_replace("__TITLE__", $install_details['n'], $setup);
        $setup = str_replace("__DOMAIN__", $install_details['d'], $setup);
        $setup = str_replace("__SITE_ROOT__", SM_ROOT_DIR, $setup);
        $setup = str_replace("__NOW__", time(), $setup);
        
        file_put_contents(SM_ROOT_DIR."System/SqlScripts/mysetup.sql", $setup);
        
        fwrite(STDOUT, "* Adding essential data to database...\n");
        
        $queries = explode(';', $setup);
        
        foreach($queries as $query){
            if(strlen(trim($query))){
                try{
                    $database->rawQuery(trim($query).';');
                }catch (SmartestException $e) {
                    continue;
                }
            }
        }
        
        $log_info = $database->getDebugInfo();
        $log_contents = '';
        
        foreach($log_info as $query){
            $log_contents .= $query."\n----------------------------------------------------------\n";
        }
        
        file_put_contents(SM_ROOT_DIR."System/Install/install.log", $setup);
        
    }else{
        fwrite(STDOUT, "* ERROR: ".SM_ROOT_DIR."System/Install/Samples/database-sample.ini could not be found! \n");
        exit(0);
    }
    
}else{
    fwrite(STDOUT, "The database has already been set up. Please edit file: ".SM_ROOT_DIR."Configuration/database.ini\n");
}

exit(0);

?>