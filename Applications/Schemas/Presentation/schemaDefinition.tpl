<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var domain = '{$domain}';

{literal}

function setSelectedItem(item_id, vid,pageName, rowColor,type,node){
	
	var row='item_'+item_id;
	var editForm = document.getElementById('pageViewForm');
	
	rowColor='#'+rowColor;
	selectedPage = item_id;
	selectedPageName = pageName;
	
	document.getElementById('item-specific-actions').style.display = 'block';
	if(type=='TRUE'){
	document.getElementById('item-specific-repeat').style.display = 'block';
	document.getElementById('add_child').style.display = 'block';
	}
	else{
	document.getElementById('item-specific-repeat').style.display = 'none';
	document.getElementById('add_child').style.display = 'none';
	}

	if(lastRow){
		// document.getElementById(lastRow).style.backgroundColor=lastRowColor;
	}
	
	// document.getElementById(row).style.backgroundColor='#99F';
	
	lastRow = row;
	lastRowColor = rowColor;
	editForm.schemadefinition_id.value = item_id;editForm.vocabulary_id.value = vid;editForm.type_node.value = node;
}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm){
		
		editForm.action="/modeltemplates/"+pageAction;
		editForm.submit();
		
	}
}

function viewPage(){

	var pageURL = domain+'editSchemaVocabulary?schemadefinition_id='+selectedPage;
	window.open(pageURL);
	
}

function toggleParentNodeFromOpenState(node_id){

	var list_id = 'list_'+node_id;
	
	// alert(domain);

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

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}/"> Templates</a>  &gt; {$content.schema.schema_name} </h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="schemadefinition_id" value="" />
<input type="hidden" name="vocabulary_id" value="" /><input type="hidden" name="type_node" value="" />
<input type="hidden" name="schema_id" value="{$schema.schema_id}" />
</form>



<ul class="tree-parent-node-open" id="tree-root">
  {defun name="menurecursion" list=$definition}
    {capture name="foreach_name" assign="foreach_name"}list_{if $element.vocabulary_id}{$element.vocabulary_id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $element.vocabulary_id}{$element.vocabulary_id}{else}0{/if}{/capture}
    {*$foreach_name*}
    {foreach from=$list item=element name=$foreach_name}
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
      {if !empty($element.children)}
      <a href="javascript:toggleParentNodeFromOpenState('{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}')"><img src="{$domain}Resources/Images/open.gif" alt="" border="0" id="toggle_{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}" /></a>
      {else}
      <img src="{$domain}Resources/Images/blank.gif" alt="" border="0" />
      {/if}
   
	<a id="item_{$element.schemadefinition_id}" class="option" href="#" onclick="setSelectedItem('{$element.schemadefinition_id}','{$element.vocabulary_id}', '{$element.vocabulary_name}', 'fff','{$element.vocabulary_definition}','{$element.vocabulary_type}');" ondblclick="window.location='{$domain}modeltemplates/editSchemaVocabulary?schemadefinition_id={$element.schemadefinition_id}'">		 
      {if $element.vocabulary_definition=='TRUE'}
	<img src="{$domain}Resources/Icons/page_code.png" alt="" border="0" />
      {else}
	<img src="{$domain}Resources/Icons/page_white_code.png" alt="" border="0" />
      {/if}
        {$element.vocabulary_name} {if $element.vocabulary_type}({$element.vocabulary_type} ){/if}
	{if $element.vocabulary_iterates==1}Repeating Element
	{/if}
      </a>
      {if !empty($element.children)}
      <ul class="tree-parent-node-open" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}">
        {fun name="menurecursion" list=$element.children}
      </ul>
      {/if}
    </li>
    {/foreach}
  {/defun}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
	<li class="permanent-action"><b>Node Options</b></li>
	<li class="permanent-action"  id='add_child' ><a href="#" onclick="{literal}if(selectedPage){ workWithItem('addSchemaVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Add A Child</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editSchemaVocabularyDetails'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt="">Edit</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSchemaElement');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Delete</a></li>
	<li class="permanent-action" id="item-specific-repeat" style="display:none"><a href="#" onclick="{literal}if(selectedPage){workWithItem('setRepeatElement');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Set As Repeating Element</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li class="disabled-action"><i>Please Select a Node</i></li>
</ul>

</div>

</tr>

</table>
