#!/usr/bin/env php
<?php

define('SM_ROOT_DIR', getcwd().DIRECTORY_SEPARATOR);

if(!is_file('/usr/share/php/smartest.php')){

    if(@copy(SM_ROOT_DIR.'System/Install/Scripts/smartest.php', '/usr/share/php/')){
        fwrite(STDOUT, "* Created /usr/share/php/smartest.php.\n");
    }

}else{
    
    fwrite(STDOUT, "* /usr/share/php/smartest.php exists.\n");
    
}

if(!is_file('/usr/bin/smartest')){

    if(@copy(SM_ROOT_DIR.'System/Install/Scripts/smartest', '/usr/bin/smartest')){
        fwrite(STDOUT, "* Created /usr/bin/smartest.\n");
    }

}else{
    
    fwrite(STDOUT, "* /usr/bin/smartest exists.\n");
    
}

exit(0);

?>