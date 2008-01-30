#!/usr/bin/env php
<?php

define('SM_ROOT_DIR', getcwd().DIRECTORY_SEPARATOR);

$files = array(
    "system", "options", "smarty", "general", "units"
);

foreach($files as $f){

    $sample = SM_ROOT_DIR.'System/Install/Samples/'.$f.'-sample.ini';
    $real = SM_ROOT_DIR.'Configuration/'.$f.'.ini';

    if(!file_exists($real)){
        if(file_exists($sample)){
            copy($sample, $real);
            fwrite(STDOUT, "* Creating ".$real."...\n");
        }else{
            fwrite(STDOUT, "* ERROR: ".$sample." could not be found! \n");
            exit(0);
        }
    }else{
        fwrite(STDOUT, "* ".$real." already exists.\n");
    }

}

$sample = SM_ROOT_DIR.'System/Install/Samples/.htaccess-sample';
$real = SM_ROOT_DIR.'Public/.htaccess';

if(!file_exists($real)){
    if(file_exists($sample)){
        copy($sample, $real);
        fwrite(STDOUT, "* Creating ".$real."...\n");
    }else{
        fwrite(STDOUT, "* ERROR: ".$sample." could not be found! \n");
        exit(0);
    }
}else{
    fwrite(STDOUT, "* ".$real." already exists.\n");
}

$sample = SM_ROOT_DIR.'System/Install/Samples/controller-sample.xml';
$real = SM_ROOT_DIR.'Configuration/controller.xml';

if(!file_exists($real)){
    if(file_exists($sample)){
        copy($sample, $real);
        fwrite(STDOUT, "* Creating ".$real."...\n");
    }else{
        fwrite(STDOUT, "* ERROR: ".$sample." could not be found! \n");
        exit(0);
    }
}else{
    fwrite(STDOUT, "* ".$real." already exists.\n");
}

if(file_exists(SM_ROOT_DIR."System/Install/Samples/default.tpl") && !file_exists(SM_ROOT_DIR."Presentation/Masters/default.tpl")){
    fwrite(STDOUT, "* Adding default page template...\n");
    copy(SM_ROOT_DIR."System/Install/Samples/default.tpl", SM_ROOT_DIR."Presentation/Masters/default.tpl");
}

exit(0);

?>