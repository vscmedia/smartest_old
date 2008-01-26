<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var domain = '{$domain}';
var section = '{$section}';

// alert(document.getElementById('pageViewForm'));
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

<h3><a href="{$domain}smartest/data">Data Manager</a> &gt; Models</h3>
<a name="top"></a>

<div class="instruction">Your data is collected into functionally distinct types called models. Please choose one to continue.</div>

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
      {$itemClass.plural_name}</a>
	{if $itemClass.number_properties < 1}{*(<a class="normal" href="{$domain}{$section}/addPropertyToClass?class_id={$itemClass.id}">No Properties</a>)*}{/if}</li>
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
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addItem');"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Add a new member item</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('addSet');"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Build a new set</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('editModel');"><img border="0" src="{$domain}Resources/Icons/page_edit.png"> Edit Model</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('getItemClassProperties');"><img border="0" src="{$domain}Resources/Icons/page_edit.png"> Edit Model Properties</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteItemClass');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete This Model</a></li>
  {* <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('importData');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Import Data</a></li> *}
  {* Remember this option is now being moved to datasets <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('exportData');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export XML</a></li> *}
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItemClass'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Build a New Model</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View Sets From Your Data</a></li>
  <li class="permanent-action"><a href="{$domain}sets/getDataExports"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Feeds</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/schemas'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li>

</ul>

</div>





