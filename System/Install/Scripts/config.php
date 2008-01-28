#!/usr/bin/env php
<?php

define('SM_ROOT_DIR', getcwd().DIRECTORY_SEPARATOR);

$files = array(
    "system", "options", "smarty", "general", "units"
);

foreach($files as $f){

    $sample = $f.'-sample.ini';
    $real = $f.'.ini';

    if(!file_exists(SM_ROOT_DIR.'Configuration/'.$real)){
        if(file_exists(SM_ROOT_DIR.'Configuration/'.$sample)){
            copy(SM_ROOT_DIR.'Configuration/'.$sample, SM_ROOT_DIR.'Configuration/'.$real);
            fwrite(STDOUT, "* Creating ".$real."...\n");
        }else{
            fwrite(STDOUT, "* ERROR: ".SM_ROOT_DIR.'Configuration/'.$sample." could not be found! \n");
            exit(0);
        }
    }else{
        fwrite(STDOUT, "* ".$real." already exists.\n");
    }

}

$sample = 'Public/.htaccess-sample';
$real = 'Public/.htaccess';

if(!file_exists(SM_ROOT_DIR.$real)){
    if(file_exists(SM_ROOT_DIR.$sample)){
        copy(SM_ROOT_DIR.$sample, SM_ROOT_DIR.$real);
        fwrite(STDOUT, "* Creating ".$real."...\n");
    }else{
        fwrite(STDOUT, "* ERROR: ".SM_ROOT_DIR.$sample." could not be found! \n");
        exit(0);
    }
}else{
    fwrite(STDOUT, "* ".$real." already exists.\n");
}

$sample = 'Configuration/controller-sample.xml';
$real = 'Configuration/controller.xml';

if(!file_exists(SM_ROOT_DIR.$real)){
    if(file_exists(SM_ROOT_DIR.$sample)){
        copy(SM_ROOT_DIR.$sample, SM_ROOT_DIR.$real);
        fwrite(STDOUT, "* Creating ".$real."...\n");
    }else{
        fwrite(STDOUT, "* ERROR: ".SM_ROOT_DIR.$sample." could not be found! \n");
        exit(0);
    }
}else{
    fwrite(STDOUT, "* ".$real." already exists.\n");
}

if(file_exists(SM_ROOT_DIR."System/Install/default.tpl") && !file_exists(SM_ROOT_DIR."Presentation/Masters/default.tpl")){
    fwrite(STDOUT, "* Adding default page template...\n");
    copy(SM_ROOT_DIR."System/Install/default.tpl", SM_ROOT_DIR."Presentation/Masters/default.tpl");
}

exit(0);

?>