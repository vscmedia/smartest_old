<script language="javascript">
{literal}
var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');

	if(editForm){
		
{/literal}		editForm.action="/{$section}/"+pageAction;{literal}
		
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
function setSelectedItem(page_id, pageName, rowColor,dataexport){
	
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
	editForm.export_name.value = dataexport;editForm.pairing_id.value = page_id;
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">Sets</a> &gt;{$set.set_name}  &gt; Choose a Pair to Countinue...</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="pairing_id" />
  <input type="hidden" id="export_name" >
</form>

<div id="options-view-chooser">
View as:
<a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-grid" id="options_grid">
{foreach from=$pairing key=key item=pair}
{if $pair.dataexport_id}
  <li>
	<a class="option" id="item_{$pair.paring_id}" onclick="setSelectedItem('{$pair.paring_id}', '{$pair.dataexport_name|escape:quotes}', 'fff','{$pair.dataexport_varname}');" >
	   <img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/package.png">{$pair.dataexport_name}</a></li>
{/if}
{/foreach}

</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="window.location='{$domain}{$section}/editExportData?schema_id={$content.schema_id}&set_id={$set.set_id}&pair_id='+document.getElementById('pairing_id').value">Edit Pairing</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="#" onclick="window.location='{$domain}{$section}/exportSuccess?set_id={$set.set_id}&schema={$content.schema}&dataexport='+document.getElementById('export_name').value">View Exported Data</a></li>
</ul>
<ul class="actions-list" id="non-specific-actions">
  <li class="permanent-action">Select a Pairing</li>
</ul>

</div>
