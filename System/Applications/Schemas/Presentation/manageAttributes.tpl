<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var domain = '{$domain}';

{literal}
function setSelectedItem(item_id,pageName, rowColor){
	
	var row='item_'+item_id;
	var editForm = document.getElementById('pageViewForm');
	
	rowColor='#'+rowColor;
	selectedPage = item_id;
	selectedPageName = pageName;
	
	document.getElementById('item-specific-actions').style.display = 'block';
	
	if(lastRow){
		// document.getElementById(lastRow).style.backgroundColor=lastRowColor;
	}
	
	// document.getElementById(row).style.backgroundColor='#99F';
	
	lastRow = row;
	lastRowColor = rowColor;
	editForm.attribute_id.value = item_id;
}
function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm){
		
		editForm.action=pageAction;
		editForm.submit();
		
	}
}

function viewPage(){

	var pageURL = '{$domain}{$section}/editItemProperty?property_id='+selectedPage;
	window.open(pageURL);
	
}

{/literal}

</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> > <a href="{$domain}modeltemplates/">Templates</a>  >  <a href="{$domain}modeltemplates/schemaDefinition?schema_id={$schema.schema_id}">{$schema.schema_name}</a> >  <a href="{$domain}modeltemplates/editSchemaVocabulary?schemadefinition_id={$vocabulary.schemadefinition_id}">{$vocabulary.vocabulary_name}</a> >Manage Attribute</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="attribute_id" value="">
<input type="hidden" name="vocabulary_id" value="{$vocabulary.vocabulary_id}" />
<input type="hidden" name="schema_id" value="{$schema.schema_id}" />
<input type="hidden" name="schemadefinition_id" value="{$vocabulary.schemadefinition_id}" />

</form>


<ul class="tree-parent-node-open" id="tree-root">
  {defun name="menurecursion" list=$attributes}
       {foreach from=$list item=element }
    <li >
       <a id="item_{$element.vocabulary_id}" class="option" href="#" onclick="setSelectedItem('{$element.vocabulary_id}', '{$element.vocabulary_name}','fff');" ondblclick="window.location='{$domain}modeltemplates/editAttribute?attribute_id={$element.vocabulary_id}'">		 
        <img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/page_code.png" />
        {$element.vocabulary_name}
      </a>
     
    </li>
    {/foreach}
  {/defun}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
	<li class="permanent-action"><b>Node Options</b></li>
	
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editAttribute'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt="">Edit Attribute</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteAttribute');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Delete Attribute</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li class="disabled-action"><i>Please Select a Node</i></li>
<li class="permanent-action"><a href="#" onclick="{literal}workWithItem('addAttribute');{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Add Attribute</a></li>
<li class="permanent-action"><a href="schemaDefinition?schema_id={$schema.schema_id}"  class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Back to Schema </a></li>
</ul>

</div>
