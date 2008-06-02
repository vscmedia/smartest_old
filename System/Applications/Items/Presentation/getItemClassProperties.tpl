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
	editForm.itemproperty_id.value = item_id;
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

<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}">Model Structure</a>  &gt;  {$itemclass.name}</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Click a property once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="class_id" value="{$itemclass.id}" />
<input type="hidden" name="itemproperty_id" value="" />
</form>

<ul class="tree-parent-node-open" id="tree-root">
  {defun name="menurecursion" list=$definition}
       {foreach from=$list item="element"}
    <li >
       <a id="item_{$element.id}" class="option" href="javascript:nothing()" onclick="setSelectedItem('{$element.id}','{$element.varname}', 'fff');" ondblclick="window.location='{$domain}{$section}/editItemProperty?class_id={$itemclass.id}&amp;itemproperty_id={$element.id}'">		 
        <img border="0" src="{$domain}Resources/Icons/page_code.png" />
        {$element.varname}
      </a>
     
    </li>
    {/foreach}
  {/defun}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
	<li class="permanent-action"><b>Node Options</b></li>
	
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editItemProperty'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt="">Edit</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteProperty');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Delete</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li class="disabled-action"><i>Please Select a Node</i></li>
<li class="permanent-action"><a href="#" onclick="{literal}workWithItem('addPropertyToClass');{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Add Property</a></li>
</ul>

</div>
