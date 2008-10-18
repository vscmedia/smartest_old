<?php

define("SM_ERROR_MISC", 100);
define("SM_ERROR_TMPL", 101);
define("SM_ERROR_FILES", 102);
define("SM_ERROR_PERMISSIONS", 102);
define("SM_ERROR_AUTH", 103);
define("SM_ERROR_DB", 104);
define("SM_ERROR_DATABASE", 104);
define("SM_ERROR_PHP", 105);
define("SM_ERROR_USER", 106);
define("SM_ERROR_MODEL", 107);
define("SM_ERROR_SMARTEST_INTERNAL", 108);

define('SM_QUERY_ALL_DRAFT', 0); // 
define('SM_QUERY_ALL_DRAFT_ARCHIVED', 1);
define('SM_QUERY_ALL_DRAFT_CURRENT', 2);

define('SM_QUERY_ALL_LIVE', 3);
define('SM_QUERY_ALL_LIVE_ARCHIVED', 4);
define('SM_QUERY_ALL_LIVE_CURRENT', 5);

define('SM_QUERY_PUBLIC_DRAFT', 6); // 
define('SM_QUERY_PUBLIC_DRAFT_ARCHIVED', 7);
define('SM_QUERY_PUBLIC_DRAFT_CURRENT', 8);

define('SM_QUERY_PUBLIC_LIVE', 9);
define('SM_QUERY_PUBLIC_LIVE_ARCHIVED', 10);
define('SM_QUERY_PUBLIC_LIVE_CURRENT', 11);

define('SM_STATUS_ALL', 0);

define('SM_STATUS_HIDDEN', 1);
define('SM_STATUS_HIDDEN_CHANGED', 2);
define('SM_STATUS_HIDDEN_APPROVED', 3);

define('SM_STATUS_LIVE', 4);
define('SM_STATUS_LIVE_CHANGED', 5);
define('SM_STATUS_LIVE_APPROVED', 6);

define('SM_STATUS_CURRENT', 7);
define('SM_STATUS_ARCHIVED', 8);

define('SM_CONTEXT_GENERAL', 100);
define('SM_CONTEXT_SYSTEM_UI', 101);
define('SM_CONTEXT_CONTENT_PAGE', 102);
define('SM_CONTEXT_DYNAMIC_TEXTFRAGMENT', 103);
define('SM_CONTEXT_COMPLEX_ELEMENT', 104);
define('SM_CONTEXT_ITEMSPACE_TEMPLATE', 105);

define('SM_USER_MESSAGE_INFO', 1);
define('SM_USER_MESSAGE_SUCCESS', 2);
define('SM_USER_MESSAGE_WARNING', 4);
define('SM_USER_MESSAGE_ERROR', 8);
define('SM_USER_MESSAGE_FAIL', 8);
define('SM_USER_MESSAGE_ACCESSDENIED', 16);
define('SM_USER_MESSAGE_ACCESS_DENIED', 16);
