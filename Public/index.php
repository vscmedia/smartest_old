<?php

// phpinfo();
// apd_set_pprof_trace();

function debug_time(){
    $time = number_format(microtime(true)*1000, 0, ".", "");
    return $time;
}

ini_set('session.gc_maxlifetime', 30*60);

// error reporting control
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

// set the debug level for the controller
define("SM_CONTROLLER_DEBUG_LEVEL", 0);
define("SM_DEVELOPER_MODE", true);

// this is all you have to do. OOP from here on out.
require_once("../System/init.php");
SmartestInit::go();
