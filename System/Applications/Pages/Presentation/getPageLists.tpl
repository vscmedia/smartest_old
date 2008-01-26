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

<div id="work-area">

{include file="System/Pages/editPage.tabs.tpl"}

<h3>Lists on page: {$content.page.page_title}</h3>
<a name="top"></a>
<div class="instruction"></div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="list_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="{$content.page.page_webid}" />
</form>
    
<div id="options-view-chooser">

Viewing mode:{if $content.version == "draft"}
<b>Draft</b> - <a href="{$domain}{$section}/getPageLists?page_id={$content.page.page_webid}&amp;version=live">Switch to live mode</a>
{else}
<b>Live</b> - <a href="{$domain}{$section}/getPageLists?page_id={$content.page.page_webid}&amp;version=draft">Switch to draft mode</a>
{/if}
  
<input type="hidden" name="page_id" value="{$content.page.page_webid}" />
<input type="hidden" name="site_id" value="{$content.data[0].info.site_id}" />
<input type="hidden" name="version" value="{$content.version}" />
  	  
</div>
<div class="preference-pane" id="assets_draft" style="display:block">

<ul>
{foreach from=$pageListNames item="list"}

<li><a href="{$domain}{$section}/defineList?list_id={$list.list_name}&amp;page_id={$content.page.page_webid}">{$list.list_name}</a></li>

{/foreach}
</ul>

</div>

</div>

<div id="actions-area">

<!--Navigation Bar-->

<ul class="actions-list" id="item-specific-actions">
  <li><b>Placeholder Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineLists');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define Lists</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  <!--<li class="permanent-action"><a href="{$domain}{$section}/setPageStylesheets?page_id={$content.page.page_webid}" class="right-nav-link">[icon] Set Page Stylesheets</a></li>-->
  <li class="permanent-action"><a href="{$domain}{$section}/editPage?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Edit Page Properties</a></li>
  <li class="permanent-action"><a href="#" onclick="viewDraftPage();" class="right-nav-link"><img src="{$domain}Resources/Icons/page_red.png" border="0" alt=""> Preview this page</a></li>
<li class="permanent-action"><a href="#" onclick="workWith('publishListsConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Publish Lists</a></li>
{*<li class="permanent-action"><a href="{$domain}{$section}/managePageData?page_id={$content.page.page_webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_red.png" border="0" alt=""> Preview this page</a></li>*}
    <!--<li class="permanent-action"><a href="#" onclick="workWith('publishPageConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>-->

  <li class="permanent-action"><a href="#" onclick="viewLivePage();" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Go to this page</a></li>
   <li class="permanent-action"><a href="{$domain}{$section}/getSitePages?site_id={$content.site_id}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>
</ul>

</div>
