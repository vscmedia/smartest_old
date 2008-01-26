<script language="javascript" type="text/javascript">
{literal}
var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;

// alert(document.getElementById('pageViewForm'));

function setSelectedItem(page_id, pageName, rowColor){
	
	var row='item_'+page_id;
	var editForm = document.getElementById('pageViewForm');
	rowColor='#'+rowColor;
	selectedPage = page_id;
	selectedPageName = pageName;
	
	document.getElementById('item-specific-actions').style.display = 'block';
	
	if(lastRow){
		document.getElementById(lastRow).className="option";
		// document.getElementById('pageNameField').innerHTML='';
	}
	
	document.getElementById(row).className="selected-option";
	
	lastRow = row;
	lastRowColor = rowColor;
	editForm.schema_id.value = page_id;
}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	// alert(editForm);
	// alert(selectedPage);
	
	if(selectedPage && editForm){
		// alert('required Vars defined');
{/literal}		editForm.action="/{$section}/"+pageAction;{literal}
		// alert(editForm.action);
		editForm.submit();
	}
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

<h3><a href="{$domain}smartest/data">Data Manager</a> &gt; XML Schemas</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="schema_id" value="" />
</form>

<div id="options-view-chooser">
View as:
<a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-grid" id="options_grid">{$content.count}
{foreach from=$schemas key=key item=schema}
{if $schema.schema_id}
  <li ondblclick="window.location='{$domain}{$section}/schemaDefinition?schema_id={$schema.schema_id}'">
	<a class="option" id="item_{$schema.schema_id}" onclick="setSelectedItem('{$schema.schema_id}', '{$schema.schema_name|escape:quotes}', 'fff');" >
	  <img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/page_code.png">{$schema.schema_name}</a></li>
{/if}
{/foreach}

</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="workWithItem('schemaDefinition');">Edit This Schema</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="workWithItem('duplicateSchemaAction');">Duplicate This Schema</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="workWithItem('renameSchema');">Rename This Schema</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="workWithItem('deleteSchemaAction');">Delete This Schema</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="workWithItem('exportXmlSchema');">Export This Schema</a></li>

</ul>

<ul class="actions-list" id="non-specific-actions">
  <li class="permanent-action"><b>Options</b></li>
  <li class="permanent-action"><td class="text"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="window.location='{$domain}{$section}/createSchema'">Add a Schema</a></li>
  <li class="permanent-action"><td class="text"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{$domain}smartest/models">View Your Data</a></li>
</ul>

</div>