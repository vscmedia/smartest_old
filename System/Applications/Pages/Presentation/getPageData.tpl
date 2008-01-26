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
<h3>Lists on page: {$content.page.page_title}</h3>
<a name="top"></a>
<div class="instruction"></div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="{$content.page.page_webid}" />
</form>

<div id="work-area">
    
<div id="options-view-chooser">
<form id="templateSelect" action="{$domain}{$section}/setPageTemplate" method="get" style="margin:0px">

{*Viewing mode:{if $content.version == "draft"}
<b>Draft</b> - <a href="{$domain}{$section}/getPageAssets?page_id={$content.page.page_webid}&amp;version=live">Switch to live mode</a>
{else}
<b>Live</b> - <a href="{$domain}{$section}/getPageAssets?page_id={$content.page.page_webid}&amp;version=draft">Switch to draft mode</a>
{/if}*}
  
<input type="hidden" name="page_id" value="{$content.page.page_webid}" />
<input type="hidden" name="site_id" value="{$content.data[0].info.site_id}" />
<input type="hidden" name="version" value="{$content.version}" />
  	  
{*{if $content.version == "draft"}
      Master Template:
      <select name="template_name" onchange="document.getElementById('templateSelect').submit();">
        <option value="">Not Selected</option>
        {foreach from=$content.templates item="template"}
          <option value="{$template.filename}"{if $content.templateMenuField == $template.filename} selected{/if}>{$template.filename}</option>
        {/foreach}
      </select>
{else}
	Master Template: <b title="Changing this value may affect which placeholders need to be defined on this page">{$content.templateMenuField}</b>
{/if}*}
    </form>
</div>
<div class="preference-pane" id="assets_draft" style="display:block">

<ul class="tree-parent-node-open" id="tree-root">


 {defun name="menurecursion" list=$lists}
    {capture name="foreach_name" assign="foreach_name"}list_{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    
    {foreach from=$list item="list" name=$foreach_name}
    
    {if $smarty.foreach.$foreach_name.iteration == 1 && $foreach_id == 0}
    <li><img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/page.png" /> {$page.page_title}</li>
    {else}{/if}
    
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
     <img src="{$domain}Resources/Images/blank.gif" alt="" border="0" />
      <a id="item_{$list.list_name|escape:quotes}" class="option" href="#" onclick="setSelectedItem('{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.assetclass_name|escape:quotes}', 'fff');" ondblclick="window.location='{$domain}{$section}/getPageAssets?page_id={$assetclass.info.page_webid}&amp;site_id='">		 
  	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/published.gif" />
	<b>{$list.list_name}</b> 
      </a>
      </li>
    {/foreach}
    
  {/defun}


</ul>

</div>

<!-- Key: <div style="display:inline"><img src="{$domain}Resources/Icons/flag_green.png" alt="" />Published&nbsp;&nbsp;
<img src="{$domain}Resources/Icons/flag_yellow.png" alt="" />Draft Only&nbsp;&nbsp;
<img src="{$domain}Resources/Icons/flag_red.png" alt="" />Undefined</div>-->

</div>

<div id="actions-area">

<!--Navigation Bar-->

<ul class="actions-list" id="item-specific-actions">
  <li><b>Placeholder Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineAssetClass');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define </a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  <!--<li class="permanent-action"><a href="{$domain}{$section}/setPageStylesheets?page_id={$content.page.page_webid}" class="right-nav-link">[icon] Set Page Stylesheets</a></li>-->
  <li class="permanent-action"><a href="{$domain}{$section}/editPage?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Edit Page Properties</a></li>
  <!--<li class="permanent-action"><a href="#" onclick="viewDraftPage();" class="right-nav-link"><img src="{$domain}Resources/Icons/page_red.png" border="0" alt=""> Preview this page</a></li>-->
<li class="permanent-action"><a href="{$domain}{$section}/managePageData?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_red.png" border="0" alt=""> Preview this page</a></li>
    <!--<li class="permanent-action"><a href="#" onclick="workWith('publishPageConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>-->

  <li class="permanent-action"><a href="#" onclick="viewLivePage();" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Go to this page</a></li>
   <li class="permanent-action"><a href="{$domain}{$section}/getSitePages?site_id={$content.site_id}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>
</ul>

</div>
