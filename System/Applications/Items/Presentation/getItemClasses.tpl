<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var domain = '{$domain}';
var section = '{$section}';

{literal}

function viewmodel(){
	var editForm = document.getElementById('pageViewForm');
	var schema = editForm.schema_id.value;
	var pageURL = domain+'modeltemplates/schemaDefinition?schema_id='+schema;
	window.location=pageURL;
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

<h3>Items</h3>

{load_interface file="items_front_tabs.tpl"}

{if empty($models)}
<div class="special-box">No models yet. Click <a href="{$domain}{$section}/addItemClass?createmetapage=true">here</a> to create one.</div>
{/if}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="class_id" id="item_id_input" value="" />
</form>

<div id="options-view-chooser">
View: <a href="{dud_link}" onclick="setView('list', 'options_grid')">List</a> /
<a href="{dud_link}" onclick="setView('grid', 'options_grid')">Icon</a>
</div>

<ul class="options-grid" id="options_grid">
{foreach from=$models key="key" item="itemClass"}
  <li ondblclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$itemClass.id}'">
    <a id="item_{$itemClass.id}" class="option" href="{dud_link}" onclick="setSelectedItem('{$itemClass.id}');">
      <img border="0" src="{$domain}Resources/Icons/model.png">
      {$itemClass.plural_name}</a></li>
{/foreach}
</ul>

</div>


<div id="actions-area">

{* Marcus edited November 9th 2006 *}
{* Please make sure Browse Items is top of the navigation *}
<ul class="actions-list" id="null-specific-actions" style="display:none">
</ul>

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected Model</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('getItemClassMembers');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Browse items</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addItem');"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Add a new item</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('editModel');"><img border="0" src="{$domain}Resources/Icons/information.png"> Model info</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('getItemClassProperties');"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit Model Properties</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('getItemClassComments');"><img border="0" src="{$domain}Resources/Icons/comments.png"> Browse comments</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('getItemClassSets');"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View data sets for this model</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addSet');"><img border="0" src="{$domain}Resources/Icons/folder_add.png"> Create a new set from this model</a></li>
  {if $allow_create_models}<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(confirm('Are you sure you want to permanently delete this model and all its items?')){workWithItem('deleteItemClass');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete This Model</a></li>{/if}
  {* <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('importData');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Import Data</a></li> *}
  {* Remember this option is now being moved to datasets <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('exportData');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export XML</a></li> *}
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  {if $allow_create_models}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItemClass'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Build a New Model</a></li>{/if}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" /> View Sets From Your Data</a></li>
  {* <li class="permanent-action"><a href="{$domain}sets/getDataExports"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Feeds</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/schemas'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li> *}
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited items</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label|summary:"28"}</a></li>
  {/foreach}
</ul>

</div>





