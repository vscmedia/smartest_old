<h3>Elements used on page: {$page.title}</h3>

<div class="instruction">Double click a placeholder to set its content, or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="{$page.webid}" />
  {if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
</form>
    
{* <div style="margin-top:10px;width:95%;padding-left:10px;border-bottom:1px solid #999;padding-bottom:7px;margin-bottom:10px;" id="options-nav">
  <a href="{$domain}{$section}/getPageAssets?page_id={$page.webid}&amp;version=draft" class="prefpane-tab">Draft</a>
  <a href="{$domain}{$section}/getPageAssets?page_id={$page.webid}&amp;version=live" class="prefpane-tab">Live</a>
</div> *}

<div id="options-view-chooser">
<form id="templateSelect" action="{$domain}{$section}/setPageTemplate" method="get" style="margin:0px">

Viewing mode:
{if $version == "draft"}
<b>Edit</b> - <a href="{$domain}{$section}/pageAssets?page_id={$page.webid}&amp;version=live">Switch to live mode</a>
{else}
<b>Live</b> - <a href="{$domain}{$section}/pageAssets?page_id={$page.webid}&amp;version=draft">Switch to draft mode</a>
{/if}
  
<input type="hidden" name="page_id" value="{$page.webid}" />
<input type="hidden" name="site_id" value="{$site_id}" />
<input type="hidden" name="version" value="{$version}" />
  	  
{if $version == "draft"}
      Master Template:
      <select name="template_name" onchange="document.getElementById('templateSelect').submit();">
        <option value="">Not Selected</option>
        {foreach from=$templates item="template"}
          <option value="{$template.filename}"{if $templateMenuField == $template.filename} selected{/if}>{$template.filename}</option>
        {/foreach}
      </select>
      {if $template.filename}{*<input type="button" onclick="window.location='{$domain}templates/editTemplate?template_code=Master&amp;template_name={$templateMenuField}'" value="Edit" />
      <a href="{$domain}templates/editTemplate?template_code=Master&amp;template_name={$template.filename}">Edit</a>*}{/if}
{else}
	Master Template: <b title="Changing this value may affect which placeholders need to be defined on this page">{$templateMenuField}</b>
{/if}
    </form>
</div>

<div class="preference-pane" id="assets_draft" style="display:block">

{if !empty($assets)}

<ul class="tree-parent-node-open" id="tree-root">
  <li><img border="0" src="{$domain}Resources/Icons/page.png" />Current Page: {$page.title}</li>
  {defun name="menurecursion" list=$assets}
    
    {capture name="foreach_name" assign="foreach_name"}list_{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    
    {foreach from=$list item="assetclass" name=$foreach_name}
    
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
    {if ($assetclass.info.defined == "PUBLISHED" || $assetclass.info.defined == "DRAFT") && in_array($assetclass.info.assetclass_type, array("SM_ASSETTYPE_JAVASCRIPT", "SM_ASSETTYPE_STYLESHEET", "SM_ASSETTYPE_RICH_TEXT", "SM_ASSETTYPE_PLAIN_TEXT", "SM_ASSETTYPE_SL_TEXT")) && $version == "draft"}<a href="{$domain}assets/editAsset?asset_id={$assetclass.info.asset_id}&amp;from=pageAssets" style="float:right;display:block;margin-right:5px;">Edit This File</a>{/if}
      {if !empty($assetclass.children)}
      <a href="{dud_link}" onclick="toggleParentNodeFromOpenState('{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}')"><img src="{$domain}Resources/System/Images/open.gif" alt="" border="0" id="toggle_{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}" /></a>
      {else}
      <img src="{$domain}Resources/System/Images/blank.gif" alt="" border="0" />
      {/if}<a id="item_{$assetclass.info.assetclass_name|escape:quotes}" class="option" href="{if $version == "draft"}javascript:setSelectedItem('{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.type|lower}');{else}javascript:nothing();{/if}">		 
    {if $assetclass.info.exists == 'true'}
        
		{if $assetclass.info.defined == "PUBLISHED"}
		  {if $assetclass.info.type == 'attachment'}
		  <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/attach.png" />
		  {elseif $assetclass.info.type == 'asset'}
		    {if $assetclass.info.asset_type == "SM_ASSETTYPE_JPEG_IMAGE" || $assetclass.info.asset_type == "SM_ASSETTYPE_PNG_IMAGE" || $assetclass.info.asset_type == "SM_ASSETTYPE_GIF_IMAGE"}
	        <img src="{$domain}Resources/Icons/picture.png" style="border:0px" />
	      {elseif $assetclass.info.asset_type == "SM_ASSETTYPE_PLAIN_TEXT"}
	        <img src="{$domain}Resources/Icons/page_white_text.png" style="border:0px" />
	      {elseif $assetclass.info.asset_type == "SM_ASSETTYPE_RICH_TEXT"}
	        <img src="{$domain}Resources/Icons/style.png" style="border:0px" />
	      {else}
	        <img src="{$domain}Resources/Icons/page_white.png" style="border:0px" />
	      {/if}
		  {elseif $assetclass.info.type == 'template'}
  		  <img src="{$domain}Resources/Icons/page_white_code.png" style="border:0px" />
      {else}
		  <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/published_{$assetclass.info.type|lower}.gif" />
		  {/if}
		{elseif  $assetclass.info.defined == "DRAFT"}
		  {if $version == "draft"}
		    <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/Icons/draftonly_{$assetclass.info.type|lower}.gif" />
		  {else}
		    <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/Icons/undefined_{$assetclass.info.type|lower}.gif" />
		  {/if}
		{else}
		  <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} has not yet been defined" src="{$domain}Resources/Icons/undefined_{$assetclass.info.type|lower}.gif" />
		{/if}
	
	  <b>{$assetclass.info.assetclass_name}</b>
	  {if $assetclass.info.type == 'placeholder'}{/if}
	  
	  {if $assetclass.info.filename != ""}
	    {$assetclass.info.filename}
	  {else}
	    
	  {/if}
	  
	{else}
	{* <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/exclamation.png" /> *}
	
	{if $assetclass.info.type == "list"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist_list.gif" />
	{elseif $assetclass.info.type == "field"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist_field.gif" />
	{elseif $assetclass.info.type == "attachment"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/attach.png" />
	{else}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist.gif" />
	{/if}
	
	<b>{$assetclass.info.assetclass_name}</b> This {$assetclass.info.type} doesn't exist.&nbsp;
	  {if $assetclass.info.type=='container'}
	    <a href="{$domain}assets/addContainer?name={$assetclass.info.assetclass_name}&amp;type={$assetclass.info.type}">Add it</a>
	  {elseif $assetclass.info.type=='placeholder'}
	    <a href="{$domain}assets/addPlaceholder?name={$assetclass.info.assetclass_name}&amp;type={$assetclass.info.type}">Add it</a>
	  {elseif $assetclass.info.type=='list'}
	    <a href="{$domain}{$section}/addList?name={$assetclass.info.assetclass_name}">Add it</a>
	  {elseif $assetclass.info.type=='field'}
	    <a href="{$domain}metadata/addPageProperty?site_id={$site_id}&amp;name={$assetclass.info.assetclass_name}">Add it</a>
	  {/if}
	  
	{/if}
      </a>
      {if !empty($assetclass.children)}
      <ul class="tree-parent-node-open" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}">
        {fun name="menurecursion" list=$assetclass.children}
      </ul>
      {/if}
    </li>
    {/foreach}
    
  {/defun}
</ul>
{/if}
</div>