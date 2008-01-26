<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var domain = '{$domain}';
var pageWebId = '{$content.page.page_webid}';

{literal}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	// alert(editForm);
	
	if(selectedPage && editForm){
		editForm.action=pageAction;
		editForm.submit();
	}
}

function workWith(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	// alert(editForm);
	
	if(editForm){
		editForm.action=pageAction;
		editForm.submit();
	}
}

function toggleParentNodeFromOpenState(node_id){

	var list_id = 'list_'+node_id;
	
	// alert(node_id);

	if(treeNodes[list_id] == 0){
		document.getElementById(list_id).style.display = 'block';
		document.getElementById('toggle_'+node_id).src = domain+'Resources/Images/open.gif';
		treeNodes[list_id] = 1;
	}else{
		document.getElementById(list_id).style.display = 'none';
		document.getElementById('toggle_'+node_id).src = domain+'Resources/Images/close.gif';
		treeNodes[list_id] = 0;
	}
}

function viewDraftPage(){

	var pageURL = domain+'website/renderEditableDraftPage?page_id='+pageWebId;
	window.location=pageURL;

}

function viewLivePage(){

	var pageURL = domain+'website/renderPageFromId?page_id='+pageWebId;
	window.open(pageURL);

}

{/literal}
</script>

{include file="System/Pages/editPage.tabs.tpl"}

<h3>Elements used on page: {$content.page.page_title}</h3>

<a name="top"></a>
<div class="instruction">Double click a placeholder to set its content, or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="{$content.page.page_webid}" />
</form>

<div id="work-area">
    
{* <div style="margin-top:10px;width:95%;padding-left:10px;border-bottom:1px solid #999;padding-bottom:7px;margin-bottom:10px;" id="options-nav">
  <a href="{$domain}{$section}/getPageAssets?page_id={$content.page.page_webid}&amp;version=draft" class="prefpane-tab">Draft</a>
  <a href="{$domain}{$section}/getPageAssets?page_id={$content.page.page_webid}&amp;version=live" class="prefpane-tab">Live</a>
</div> *}

<div id="options-view-chooser">
<form id="templateSelect" action="{$domain}{$section}/setPageTemplate" method="get" style="margin:0px">
Viewing mode:
{if $content.version == "draft"}
<b>Edit</b> - <a href="{$domain}{$section}/getPageAssets?page_id={$content.page.page_webid}&amp;version=live">Switch to live mode</a>
{else}
<b>Live</b> - <a href="{$domain}{$section}/getPageAssets?page_id={$content.page.page_webid}&amp;version=draft">Switch to draft mode</a>
{/if}
  
<input type="hidden" name="page_id" value="{$content.page.page_webid}" />
<input type="hidden" name="site_id" value="{$content.data[0].info.site_id}" />
<input type="hidden" name="version" value="{$content.version}" />
  	  
{if $content.version == "draft"}
      Master Template:
      <select name="template_name" onchange="document.getElementById('templateSelect').submit();">
        <option value="">Not Selected</option>
        {foreach from=$content.templates item="template"}
          <option value="{$template.filename}"{if $content.templateMenuField == $template.filename} selected{/if}>{$template.filename}</option>
        {/foreach}
      </select>
      {if $template.filename}<input type="button" onclick="window.location='{$domain}templates/editTemplate?template_code=Master&amp;template_name={$content.templateMenuField}'" value="Edit" />
      {*<a href="{$domain}templates/editTemplate?template_code=Master&amp;template_name={$template.filename}">Edit</a>*}{/if}
{else}
	Master Template: <b title="Changing this value may affect which placeholders need to be defined on this page">{$content.templateMenuField}</b>
{/if}
    </form>
</div>

<div class="preference-pane" id="assets_draft" style="display:block">

{if !empty($assets)}

<ul class="tree-parent-node-open" id="tree-root">
  {defun name="menurecursion" list=$assets}
    {capture name="foreach_name" assign="foreach_name"}list_{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    
    {foreach from=$list item="assetclass" name=$foreach_name}
    
    {if $smarty.foreach.$foreach_name.iteration == 1 && $foreach_id == 0}
    <li><img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/page.png" /> {$page.page_title}</li>
    {else}{/if}
    
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
    {if ($assetclass.info.defined == "PUBLISHED" || $assetclass.info.defined == "DRAFT") && in_array($assetclass.info.assetclass_type_code, array("JSCR", "CSS", "HTML", "TMPL", "TEXT", "LINE")) && $content.version == "draft"}<a href="{$domain}assets/editAsset?asset_id={$assetclass.info.asset_webid}" style="float:right;display:block;margin-right:5px;" class="button">Edit This {$assetclass.info.assetclass_type_code} Asset</a>{/if}
      {if !empty($assetclass.children)}
      <a href="#" onclick="toggleParentNodeFromOpenState('{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}')"><img src="{$domain}Resources/Images/open.gif" alt="" border="0" id="toggle_{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}" /></a>
      {else}
      <img src="{$domain}Resources/Images/blank.gif" alt="" border="0" />
      {/if}
      <a id="item_{$assetclass.info.assetclass_name|escape:quotes}" class="option" href="{if $content.version == "draft"}javascript:setSelectedItem('{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.assetclass_name|escape:quotes}', 'fff');{else}javascript:void(0);{/if}">		 
    {if $assetclass.info.exists=='true'}
        {* <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/{$assetclass.info.assetclass_type_code|lower}.{$assetclass.info.defined|lower}.png" /> *}


		{if $assetclass.info.defined == "PUBLISHED"}
		  <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/published_{$assetclass.info.type|lower}.gif" />
		{elseif  $assetclass.info.defined == "DRAFT"}
		  {if $content.version == "draft"}
		    <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/Icons/draftonly_{$assetclass.info.type|lower}.gif" />
		  {else}
		    <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/Icons/undefined_{$assetclass.info.type|lower}.gif" />
		  {/if}
		{else}
		  <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} has not yet been defined" src="{$domain}Resources/Icons/undefined_{$assetclass.info.type|lower}.gif" />
		{/if}
	
	  <b>{$assetclass.info.assetclass_name}</b>
	  {if $assetclass.info.type != container} ({$assetclass.info.assetclass_type_code|lower}){/if}
	  {if $assetclass.info.filename != ""} : 
	    {if $assetclass.info.assetclass_type_code == "JPEG" || $assetclass.info.assetclass_type_code == "PNG" || $assetclass.info.assetclass_type_code == "GIF"}
	      <img src="{$domain}Resources/Icons/picture.png" style="border:0px" />
	    {elseif $assetclass.info.assetclass_type_code == "TEXT"}
	      <img src="{$domain}Resources/Icons/page_white_text.png" style="border:0px" />
	    {elseif $assetclass.info.assetclass_type_code == "HTML"}
	      <img src="{$domain}Resources/Icons/page_code.png" style="border:0px" />
	    {else}
	      
	    {/if}
	  {$assetclass.info.filename}
	  {else}
	    {* <i>undefined</i>*}
	  {/if}
	  
	{else}
	{* <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/exclamation.png" /> *}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist.gif" />
	<b>{$assetclass.info.assetclass_name}</b> This {$assetclass.info.type} doesn't exist.&nbsp;
	  {if $assetclass.info.type=='container'}
	    <a href="{$domain}assets/addContainer?name={$assetclass.info.assetclass_name}&amp;type={$assetclass.info.type}">Add it</a>
	  {else}
	    <a href="{$domain}assets/addPlaceholder?name={$assetclass.info.assetclass_name}&amp;type={$assetclass.info.type}">Add it</a>
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

<!-- Key: <div style="display:inline"><img src="{$domain}Resources/Icons/flag_green.png" alt="" />Published&nbsp;&nbsp;
<img src="{$domain}Resources/Icons/flag_yellow.png" alt="" />Draft Only&nbsp;&nbsp;
<img src="{$domain}Resources/Icons/flag_red.png" alt="" />Undefined</div>-->

</div>

<div id="actions-area">

<!--Navigation Bar-->

<ul class="actions-list" id="item-specific-actions">
  <li><b>Placeholder Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineAssetClass');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define Placeholder/Container</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  <!--<li class="permanent-action"><a href="{$domain}{$section}/setPageStylesheets?page_id={$content.page.page_webid}" class="right-nav-link">[icon] Set Page Stylesheets</a></li>-->
  <li class="permanent-action"><a href="{$domain}{$section}/editPage?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Edit Page Properties</a></li>
  <li class="permanent-action"><a href="{$domain}{$section}/getPageLists?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Manage Data On This Page</a></li>
  
  {if $content.version == "draft"}<li class="permanent-action"><a href="#" onclick="workWith('publishPageConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>{/if}
  {* <li class="permanent-action"><a href="#" onclick="workWith('publishPageContainersConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Publish Containers</a></li> *}
  {if $content.version == "draft"}<li class="permanent-action"><a href="#" onclick="workWith('publishPagePlaceholdersConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Publish Placeholders Only</a></li>{/if}
  <li class="permanent-action"><a href="#" onclick="viewLivePage();" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Go to this page</a></li>

  <li class="permanent-action"><a href="{$domain}{$section}/layoutPresetForm?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Create layout preset from this page</a></li>

  {if $content.draftAsset.asset_id && $content.draftAsset.asset_id != $content.liveAsset.asset_id}<li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to publish your changes right now?')){workWithItem('setLiveAsset');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Publish This Asset Class</a>{/if}
  <li class="permanent-action"><a href="{$domain}smartest/assets/types" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Browse Assets Library</a></li>
  <li class="permanent-action"><a href="{$domain}{$section}/getSite" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>
</ul>

</div>