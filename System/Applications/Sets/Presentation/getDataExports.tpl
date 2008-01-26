<script language="javascript" type="text/javascript">
{literal}
var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;

function setSelectedItem(page_id, pageName, rowColor,pname){
	
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
	editForm.export_id.value = page_id;editForm.pairing_name.value = pname;
}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');	
	if(editForm){
		// alert('required Vars defined');
{/literal}		editForm.action="/{$section}/"+pageAction; {literal}
		// alert(editForm.action);
		editForm.submit();
	}
}

{/literal}
</script>

<div id="work-area">
<h3><a href="{$domain}smartest/data">Data Manager</a> &gt; XML Feeds</h3>
<a name="top"></a>

<div class="instruction">Please choose an XML feed to continue.</div>

<form id="pageViewForm" method="get" action="">
    <input type="hidden" id="export_id" name="export_id"/>
  <input type="hidden" id="pairing_name" name="pairing_name">  
</form>

<ul class="{if $content.count > 10}options-list{else}options-grid{/if}" id="{if $content.count > 10}options_list{else}options_grid{/if}">
{foreach from=$pairing key=key item=pair}
<li style="list-style:none;" ondblclick="">
	<a class="option" id="item_{$pair.dataexport_id}" onclick="setSelectedItem('{$pair.dataexport_id}', '{$pair.dataexport_name|escape:quotes}', 'fff','{$pair.dataexport_name}');"  >
	   <img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/package.png">{$pair.dataexport_name}</a></li>
 
{/foreach}
</ul>
</div>

<div id="actions-area">
<ul class="actions-list" id="item-specific-actions">
 <li class="permanent-action"><b>Selection Options</b></li>
 <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editDataExportFeed');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_add.png">Edit Exported Data Feed</a></li>
</ul>
<ul class="actions-list">
  <li class="permanent-action"><b>Options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View Sets From Your Data</a></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addSet'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Create A New Data Set</a></li>  
  <li class="permanent-action"><a href="{$domain}smartest/models"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View Your Data</a></li>
  <li class="permanent-action"><a href="{$domain}smartest/schemas"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li>
</ul>

</div>




