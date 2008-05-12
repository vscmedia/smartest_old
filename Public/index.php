<?php

// phpinfo();
// apd_set_pprof_trace();

// error reporting control
error_reporting(E_ALL ^ E_NOTICE);

// set the debug level for the controller
define("SM_CONTROLLER_DEBUG_LEVEL", 0);
define("SM_DEVELOPER_MODE", true);

// this is all you have to do. OOP from here on out.
require_once("../System/init.php");
SmartestInit::go();
