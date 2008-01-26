<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var domain = "{$domain}";
var page_webid = "{$content.page.page_webid}";

{literal}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('editForm');
	
	if(editForm){
		editForm.action=pageAction;
		editForm.submit();
	}
}

function viewDraftPage(){

	var pageURL = domain+'website/renderEditableDraftPage?page_id='+page_webid;
	window.location=pageURL;

}

function viewLivePage(){

	var pageURL = domain+'website/renderPageFromId?page_id='+page_webid;
	window.open(pageURL);

}

function setView(viewName, list_id){
	if(viewName == "grid"){
		document.getElementById(list_id).className="options-grid";
	}else if(viewName == "list"){
		document.getElementById(list_id).className="options-list";
	}
}

{/literal}
</script>

<div id="work-area">

<h3>Define {if $content.assetClass.assettype_code=="TMPL"}Container{else}Placeholder{/if}</h3>

<a name="top"></a>

<div class="text" style="margin-bottom:10px">
{if $content.numAssets > 0}
Choose an asset to use in 

{if $content.assetClass.assettype_code=="TMPL"}
container
{else}
placeholder
{/if}

"{$content.assetClass.assetclass_label}" ({$content.assetClass.assetclass_name})
on page "{$content.page.page_title}"

{else}<a href="{$domain}assets/addAsset?assettype_code={$content.assetClass.assettype_code}">[icon] Add a new <b>{$content.assetClass.assettype_label}</b> asset.</a>{/if}</div>

<form id="editForm" method="get" action="">
  <input type="hidden" name="asset_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="{$content.page.page_webid}" />
  <input type="hidden" name="assetclass_id" value="{$content.assetClass.assetclass_name}" />
  <input type="hidden" name="assettype_code" value="{$content.assetClass.assettype_code}" />
</form>

{if $content.numAssets > 0 }

<div id="options-view-chooser">
{if $content.assetClass.assettype_code=="TMPL"}Templates{else}{$content.assetClass.assettype_label} ({$content.assetClass.assettype_code}) assets.{/if} View as:
<a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-list" style="margin-top:0px" id="options_grid">
{foreach from=$content.assets item="asset"}

<li ondblclick="{literal}if(confirm('Are you sure you want to select this asset?')){{/literal}window.location='{$domain}{$section}/setDraftAsset?asset_id={$asset.asset_webid}&page_id={$content.page.page_webid}&assetclass_id={$content.assetClass.assetclass_name}&assettype_code={$content.assetClass.assettype_code}'{literal}}{/literal}">
  <a href="javascript:setSelectedItem('{$asset.asset_id}', '{$asset.asset_stringid}');" class="option" id="item_{$asset.asset_id}">
    <img border="0" src="{$domain}Resources/Icons/page.png" />{$asset.asset_stringid}</a></li>

{/foreach}

</ul>


{else}
<div class="notify-warning">There are no available assets of this type ({$content.assetClass.assettype_label}) yet.</div>
{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected Asset</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('setDraftAsset');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Use This Asset</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>{$content.assetClass.assettype_label} assets</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}assets/addAsset?assettype_code={$content.assetClass.assettype_code}'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_picture.png" border="0" alt=""> Add a new {$content.assetClass.assettype_label} asset</a></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$content.page.page_webid}'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> View page assets</a></li>
</ul>

</div>