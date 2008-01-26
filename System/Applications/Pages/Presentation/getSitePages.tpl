<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var domain = '{$domain}';

{literal}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm){
		
		editForm.action=pageAction;
		editForm.submit();
		
	}
}

function viewPage(){

	var pageURL = domain+'website/renderPageFromId?page_id='+selectedPage;
	window.open(pageURL);

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

function toggleParentNodeFromClosedState(node_id){

	var list_id = 'list_'+node_id;
	
	// alert(domain);

	if(treeNodes[list_id] == 1){
		document.getElementById(list_id).style.display = 'none';
		document.getElementById('toggle_'+node_id).src = domain+'Resources/Images/close.gif';
		treeNodes[list_id] = 0;
	}else{
		document.getElementById(list_id).style.display = 'block';
		document.getElementById('toggle_'+node_id).src = domain+'Resources/Images/open.gif';
		treeNodes[list_id] = 1;
	}
}



{/literal}
</script>
<h3>Website Manager</h3>
<a name="top"></a>
<div class="instruction">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="page_id" id="item_id_input" value="" />
  <input type="hidden" name="site_id" value="{$content.data[0].info.site_id}" />
</form>
{if $content.saved_url_status=="false"}
<table>
<tr>
      <td  colspan="2"><font size="-1" >
Sorry!The Url you have entered for the new page already exists. </font></td>
</tr>
{/if}
</table>
<div id="work-area">

{* <table cellpadding="0" cellspacing="0" border="0" style="width:100%;border:0px;">
  <tr class="mainpanel-row-ccf">
    <td style="padding-left:10px" id="pageNameField" class="text">Sitemap: Pages in site "{$content.data[0].info.site_name}"</td></tr>
{foreach from=$content.data item=page key=key}
{capture name=indent assign=indent}{math equation="x*y+z" x=30 y=$page.treeLevel z=10}{/capture}
{capture name=indent assign=doubleIndent}{math equation="x*y+z" x=30 y=$page.treeLevel z=40}{/capture}
{if isset($content.data[0].info.site_name) }
  <tr class="mainpanel-row-{cycle values="ddd,fff"}" id="page_{$page.info.page_webid}" ondblclick="window.location='{$domain}{$section}/getPageAssets?page_id={$page.info.page_webid}&amp;site_id={$content.data[0].info.site_id}'"><!-- onmouseover="this.style.backgroundColor='#f90'" onmouseout="this.style.backgroundColor='{cycle name="return" values="fff,ddd"}'-->
    <td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$page.info.page_webid}', '{$page.info.page_title|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text"><img src="{$domain}Resources/Images/spacer.gif" style="width:1px;height:22px;display:inline" border="0" alt="" />{if $page.info.page_type != "NORMAL"}<img src="{$domain}Resources/Icons/page_gear.png" border="0" alt="">{else}{if $page.treeLevel == 0}<img src="{$domain}Resources/Icons/world.png" border="0" alt="">{elseif $page.treeLevel == 1}<img src="{$domain}Resources/Icons/page_red.png" border="0" alt="">{else}<img src="{$domain}Resources/Icons/page.png" border="0" alt="">{/if}{/if} <a href="javascript:void(0);" class="mainpanel-link">{$page.info.page_title}</a>{if $page.treeLevel == 1 && $page.info.page_type == "NORMAL"} (Section page){/if}</td></tr>
{else}
  <tr style="background-color:#{cycle values="ddd,fff"};height:22px">
    <td><img src="{$domain}Resources/Images/spacer.gif" style="width:1px;height:22px;display:inline" border="0" alt="" />There are no pages yet. Click <a href="{$domain}{$section}addPage">here</a> to add one.</td></tr>
{/if}
{/foreach}

</table> *}

<ul class="tree-parent-node-open" id="tree-root">
  {defun name="menurecursion" list=$tree}
    {capture name="foreach_name" assign="foreach_name"}list_{if $page.info.page_id}{$page.info.page_id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $page.info.page_id}{$page.info.page_id}{else}0{/if}{/capture}
    {foreach from=$list item="page" name=$foreach_name}
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
      {if !empty($page.children)}
      <a href="#" onclick="toggleParentNodeFromOpenState('{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}')"><img src="{$domain}Resources/Images/open.gif" alt="" border="0" id="toggle_{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}" /></a>
      {else}
      <img src="{$domain}Resources/Images/blank.gif" alt="" border="0" />
      {/if}
      <a id="item_{$page.info.page_webid}" class="option" href="#" onclick="setSelectedItem('{$page.info.page_webid}', '{$page.info.page_title|escape:quotes}', 'fff');" ondblclick="window.location='{$domain}{$section}/getPageAssets?page_id={$page.info.page_webid}&amp;site_id={$content.data[0].info.site_id}'">		 
        <img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/page.png" />
        {$page.info.page_title}
      </a>
      {if !empty($page.children)}
      <ul class="tree-parent-node-open" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}">
        {fun name="menurecursion" list=$page.children}
      </ul>
      {/if}
    </li>
    {/foreach}
  {/defun}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
	<li><b>Page Options</b></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('getPageAssets'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Manage Page Assets</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('getPageLists'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Manage Page Data</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editPage'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit Page Properties</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deletePage');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Delete Page</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ viewPage(); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> View Page On Website</a></li>
	
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="#" onclick="workWithItem('addPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add a Page</a></li>
    <li class="permanent-action"><a href="#" onclick="workWithItem('editSite');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Edit Site Parameters</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to stop working with this site?')){workWithItem('closeCurrentSite');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/control_eject_blue.png" border="0" alt=""> Finish working with this site</a></li>
</ul>

</div>