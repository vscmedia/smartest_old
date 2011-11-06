{if $version == "draft"}
<div class="instruction">Structure of elements as they are being rendered on the draft version of this page. {help id="websitemanager:page_elements_tree"}What is this?{/help}</div>
{else}
<div class="instruction">Structure of elements as they are being rendered on the live version of this page. {help id="websitemanager:page_elements_tree"}What is this?{/help}</div>
{/if}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="{$page.webid}" />
  {if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
</form>

<script type="text/javascript">
var elementTree = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'page-element');
</script>

{if $show_deleted_warning}
  <div class="warning">Warning: This page is currently in the trash.</div>
{/if}

{if $show_metapage_warning}
  <div class="warning">Warning: the meta-page you are editing is not the default meta-page for this item. Most automatically generated links will not link to this page. <a href="{$domain}{$section}/pageAssets?page_id={$default_metapage_webid}&amp;item_id={$item.id}"></a></div>
{/if}

<div class="special-box">
  <form id="viewSelect" action="{$domain}{$section}/pageAssets" method="get" style="margin:0px">
    
    <input type="hidden" name="page_id" value="{$page.webid}" />
    <input type="hidden" name="site_id" value="{$site_id}" />
    <input type="hidden" name="version" value="{$version}" />
    {if $page.type == 'ITEMCLASS' && $item.id}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
    
    <div class="special-box-key">Viewing mode:</div>
    
    <select name="version" onchange="document.getElementById('viewSelect').submit();">
      <option value="draft"{if $version == "draft"} selected="selected"{/if}>Draft</option>
      <option value="live"{if $version == "live"} selected="selected"{/if}>Live</option>
    </select>
    
    {*action_button text="Switch to live mode"}{$domain}websitemanager/pageAssets?page_id=jl02c1D042YTWF6w85TO9j808iW7wNjQ&amp;version=live{/action_button*}
    {*action_button text="Switch to draft mode"}{$domain}websitemanager/pageAssets?page_id=jl02c1D042YTWF6w85TO9j808iW7wNjQ&amp;version=draft{/action_button*}

  </form>

  <form id="templateSelect" action="{$domain}{$section}/setPageTemplate" method="get" style="margin:0px">

    <input type="hidden" name="page_id" value="{$page.webid}" />
    <input type="hidden" name="site_id" value="{$site_id}" />
    <input type="hidden" name="version" value="{$version}" />
  	  
{if $version == "draft"}
    <span>
      <div class="special-box-key">Page Template:</div>
      <select name="template_name" onchange="$('templateSelect').submit();">
        <option value="">Not Selected</option>
{foreach from=$templates item="t"}
        <option value="{$t.url}"{if $templateMenuField == $t.url} selected{/if}>{$t.url}</option>
{/foreach}
      </select>
      {if $page_template.filename && $version == "draft"}<a href="{$domain}templates/editTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={if $page_template.status == "imported"}{$page_template.id}{else}{$page_template.url}{/if}">Edit</a>{/if}
    </span>
{else}
    <div class="special-box-key">Page template: </div>{if strlen($templateMenuField)}<strong>{$templateMenuField}</strong>{else}<em style="color:#666">None yet specified</em>{/if}</span>
{/if}

  </form>
</div>

{if $show_template_warning}
  <div class="warning">Warning: the page template you are currently using is not yet in the templates repository. Click <a href="{$domain}templates/importSingleTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={$templateMenuField}">here</a> to import it.</div>
{/if}

<div class="preference-pane" id="assets_draft" style="display:block">

{if !empty($assets)}

<ul class="tree-parent-node-open" id="tree-root">
  <li class="page-element"><img border="0" src="{$domain}Resources/Icons/page.png" />Current Page: {$page.title}</li>
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
      {/if}
      
      <a id="{$assetclass.info.type|lower}_{$assetclass.info.assetclass_name|escape:quotes}" class="option" href="{if $version == "draft"}javascript:elementTree.setSelectedItem('{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.type|lower}');{else}javascript:nothing();{/if}">		 
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
  		{elseif $assetclass.info.type == 'item'}
    		<img src="{$domain}Resources/Icons/package_small.png" style="border:0px;width:16px;height:16px" />
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
	
	  <b>{$assetclass.info.assetclass_name|end|escape:html}</b>
	  {if $assetclass.info.type == 'placeholder'}{/if}
	  
	  {if $assetclass.info.filename != ""}
	    {$assetclass.info.filename}
	  {else}
	    
	  {/if}
	  
	{else}
	
	{if $assetclass.info.type == "list"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist_list.gif" />
	{elseif $assetclass.info.type == "field"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist_field.gif" />
	{elseif $assetclass.info.type == "attachment"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/attach.png" />
	{else}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist.gif" />
	{/if}
	
	<b>{$assetclass.info.assetclass_name}</b> Smartest needs more information about this new {$assetclass.info.type}.&nbsp;
	  {if $assetclass.info.type=='container'}
	    <a href="{$domain}{$section}/addContainer?name={$assetclass.info.assetclass_name}">Enter it now</a>
	  {elseif $assetclass.info.type=='placeholder'}
	    <a href="{$domain}{$section}/addPlaceholder?placeholder_name={$assetclass.info.assetclass_name}">Enter it now</a>
	  {elseif $assetclass.info.type=='list'}
	    <a href="{$domain}{$section}/addList?name={$assetclass.info.assetclass_name}">Enter it now</a>
	  {elseif $assetclass.info.type=='field'}
	    <a href="{$domain}metadata/addPageProperty?site_id={$site_id}&amp;name={$assetclass.info.assetclass_name}">Enter it now</a>
	  {elseif $assetclass.info.type=='itemspace'}
  	  <a href="{$domain}{$section}/addItemSpace?site_id={$site_id}&amp;name={$assetclass.info.assetclass_name}">Enter it now</a>
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