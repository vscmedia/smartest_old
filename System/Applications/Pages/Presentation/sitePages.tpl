<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();

{literal}

function viewPage(){

	var pageURL = domain+'website/renderPageFromId?page_id='+selectedPage;
	window.open(pageURL);

}

{/literal}
</script>

<div id="work-area">

<h3>Site Hierarchy</h3>
<a name="top"></a>
<div class="instruction">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="page_id" id="item_id_input" value="" />
</form>

{* <table cellpadding="0" cellspacing="0" border="0" style="width:100%;border:0px;">
  <tr class="mainpanel-row-ccf">
    <td style="padding-left:10px" id="pageNameField" class="text">Sitemap: Pages in site "{$content.data[0].info.site_name}"</td></tr>
{foreach from=$content.data item=page key=key}
{capture name=indent assign=indent}{math equation="x*y+z" x=30 y=$page.treeLevel z=10}{/capture}
{capture name=indent assign=doubleIndent}{math equation="x*y+z" x=30 y=$page.treeLevel z=40}{/capture}
{if isset($content.data[0].info.site_name) }
  <tr class="mainpanel-row-{cycle values="ddd,fff"}" id="page_{$page.info.webid}" ondblclick="window.location='{$domain}{$section}/getPageAssets?page_id={$page.info.webid}&amp;site_id={$content.data[0].info.site_id}'"><!-- onmouseover="this.style.backgroundColor='#f90'" onmouseout="this.style.backgroundColor='{cycle name="return" values="fff,ddd"}'-->
    <td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$page.info.webid}', '{$page.info.title|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text"><img src="{$domain}Resources/System/Images/spacer.gif" style="width:1px;height:22px;display:inline" border="0" alt="" />{if $page.info.type != "NORMAL"}<img src="{$domain}Resources/Icons/page_gear.gif" border="0" alt="">{else}{if $page.treeLevel == 0}<img src="{$domain}Resources/Icons/world.png" border="0" alt="">{else}<img src="{$domain}Resources/Icons/page.gif" border="0" alt="">{/if}{/if} <a href="javascript:void(0);" class="mainpanel-link">{$page.info.title}</a>{if $page.treeLevel == 1 && $page.info.type == "NORMAL"} (Section page){/if}</td></tr>
{else}
  <tr style="background-color:#{cycle values="ddd,fff"};height:22px">
    <td><img src="{$domain}Resources/System/Images/spacer.gif" style="width:1px;height:22px;display:inline" border="0" alt="" />There are no pages yet. Click <a href="{$domain}{$section}addPage">here</a> to add one.</td></tr>
{/if}
{/foreach}

</table> *}

<ul class="tree-parent-node-open" id="tree-root">
  {defun name="menurecursion" list=$tree}
    
    {capture name="foreach_name" assign="foreach_name"}list_{if $page.info.id}{$page.info.id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $page.info.id}{$page.info.id}{else}0{/if}{/capture}
    {foreach from=$list item="page" name=$foreach_name}
    
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
      
      {if !empty($page.child_items) || !empty($page.children)}
      <a href="javascript:toggleParentNodeFromOpenState('{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}')"><img src="{$domain}Resources/System/Images/open.gif" alt="" border="0" id="toggle_{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}" /></a>
      {else}
      <img src="{$domain}Resources/System/Images/blank.gif" alt="" border="0" />
      {/if}
      
      <a id="item_{$page.info.webid}" class="option" href="javascript:nothing()" onclick="setSelectedItem('{$page.info.webid}', '{$page.info.title|escape:quotes}', '{if $page.info.type == 'ITEMCLASS'}meta-page{else}static-page{/if}');" ondblclick="window.location='{$domain}{$section}/openPage?page_id={$page.info.webid}&amp;site_id={$content.data[0].info.site_id}'">		 
        {if $page.info.type == 'ITEMCLASS'}<img border="0" src="{$domain}Resources/Icons/page_gear.gif" />{else}<img border="0" src="{$domain}Resources/Icons/page.gif" />{/if}
        {$page.info.title} {if $page.info.is_published == "TRUE"}(published)
        {else}(not published){/if}
      </a>
      {if !empty($page.children)}
      {* if !empty($page.child_items) || !empty($page.children) *}
          <ul class="tree-parent-node-open" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}">
           {*   {foreach from=$page.child_items item="child_item" name="child_item_list" }
            <li {if $smarty.foreach.child_item_list.last && empty($page.children)}class="last"{elseif $smarty.foreach.child_item_list.first}class="first"{else}class="middle"{/if}>
            
            <img src="{$domain}Resources/System/Images/blank.gif" alt="" border="0" />
            <a id="{$page.info.webid}_{$child_item.webid}" class="option" onclick="setSelectedItem('{$page.info.webid}_{$child_item.webid}', '{$child_item.name|escape:quotes}', 'set-member')"><img src="{$domain}Resources/Icons/package.png" alt="" border="0" />&nbsp;{$child_item.name}</a>
            
            </li>
            {/foreach} *}
      
      {* <ul class="tree-parent-node-open" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}"> *}
        {fun name="menurecursion" list=$page.children}
      </ul>
      {/if}
      
    </li>
    {/foreach}
  {/defun}
</ul>

</div>

<div id="actions-area">

  <ul class="actions-list" id="home-page-specific-actions" style="display:none">

  	<li><b>Home Page Options</b></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('openPage'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this page</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add a new page under this one</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('publishPageConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('preview'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/eye.png" border="0" alt=""> Preview This Page</a></li>

  </ul>

<ul class="actions-list" id="static-page-specific-actions" style="display:none">

	<li><b>Static Page Options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('openPage'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this page</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add a new page under this one</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('publishPageConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('preview'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/eye.png" border="0" alt=""> Preview This Page</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('movePageUp'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/arrow_up.png" border="0" alt=""> Move Up</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('movePageDown'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/arrow_down.png" border="0" alt=""> Move Down</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deletePage');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Delete Page</a></li>
	
</ul>

<ul class="actions-list" id="meta-page-specific-actions" style="display:none">

	<li><b>Meta Page Options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('openPage'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this page</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add a new page under this one</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('publishPageConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('preview'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/eye.png" border="0" alt=""> Preview This Page</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deletePage');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Delete Page</a></li>
    	
</ul>

<ul class="actions-list" id="set-member-specific-actions" style="display:none">

	<li><b>Item Options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ window.location=sm_domain+'datamanager/editItem?item_id='+selectedPage); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit This Item</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add a New Page</a></li>
	
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    {* <li class="permanent-action"><a href="{$domain}{$section}/editSite" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit site parameters</a></li> *}
    <li class="permanent-action"><a href="{$domain}websitemanager/releaseCurrentUserHeldPages" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" alt=""> Release all pages</a></li>
    <li class="permanent-action"><a href="{$domain}websitemanager/placeholders" class="right-nav-link"><img src="{$domain}Resources/Icons/published_placeholder.gif" border="0" alt=""> Placeholders</a></li>
    <li class="permanent-action"><a href="{$domain}websitemanager/clearPagesCache" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Clear cached pages</a></li>
    <li class="permanent-action"><a href="{$domain}desktop/closeCurrentSite" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this site</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited pages</span></li>
  {foreach from=$recent_pages item="recent_page"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_page.action_url}'"><img border="0" src="{$recent_page.small_icon}" /> {$recent_page.label|summary:"28"}</a></li>
  {/foreach}
</ul>

</div>