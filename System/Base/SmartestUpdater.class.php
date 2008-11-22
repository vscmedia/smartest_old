<?php

class SmartestUpdater{
    
    public static function run(){
        if(is_file(SM_ROOT_DIR.'System/Core/Info/build-number')){
            $last_build_number = file_get_contents(SM_ROOT_DIR.'System/Core/Info/build-number');
        }else{
            file_put_contents(SM_ROOT_DIR.'System/Core/Info/build-number', SM_SVN_REVISION_NUMBER);
            $last_build_number = SM_SVN_REVISION_NUMBER;
        }
    }
    
    
}