<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>

	<title>{$title}</title>
	{$meta_description}
	{$meta_keywords}
	{stylesheet file="main.css" media="screen"}
  </head>

<body class="spare">
<div width="700" id="body_main">
{* {foreach from=$_navigation.breadcrumbs item="breadcrumb"}{capture name="page_link_name" assign="page_link_name"}page:{$breadcrumb.page_name}{/capture}{link to=$page_link_name with=$breadcrumb.page_title} >>{/foreach} *}

  <h1>Smartest Test Website</h1>
  
  {if $is404}
  
  Error 404 - Requested Page Not Found<br /><br />
  
  {else}
  
  You are in: {breadcrumbs linkclass="test"}<br /><br />
  
  {field name="title"}

  {placeholder name="test-text"}

  {container name="tmpl-test"}                                                                                          
  
  {container name="body_container"}
  
  {placeholder name="description_text"}
  {placeholder name="footer_image"}
  
  {list name="clients"}<br />
  
  {/if}
  
  Page Category: {field name="category"}<br />
  Page Color Scheme: {field name="color_scheme"}
  
  <ul>
  {repeat from="static_news"}
  <li>name: {$properties._name} {cycle values="odd,even"}</li>
  {/repeat}
  </ul>
  
  {delicious_link}
  
 </div>
 
 {* google_analytics id="UA-500258-2" *}

</body>
</html>