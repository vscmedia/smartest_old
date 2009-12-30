<script language="javascript" type="text/javascript">

/* var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var domain = '{$domain}'; */

{literal}

/* function setSelectedItem(item_id,pageName, rowColor){
	
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
	
}  */

{/literal}

</script>
<div id="work-area">

{load_interface file="edit_model_tabs.tpl"}

<h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Properties</h3>

<div class="text" style="margin-bottom:10px">Click a property once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="class_id" value="{$model.id}" />
<input type="hidden" name="itemproperty_id" value="" id="item_id_input" />
</form>

<ul class="options-list" id="tree-root">
  {defun name="menurecursion" list=$definition}
       {foreach from=$list item="element"}
    <li>
       <a id="item_{$element.id}" class="option" href="javascript:nothing()" onclick="setSelectedItem('{$element.id}');" ondblclick="window.location='{$domain}{$section}/editItemProperty?class_id={$model.id}&amp;itemproperty_id={$element.id}'">		 
        <img border="0" src="{$domain}Resources/Icons/page_code.png" />{$element.name}
      </a>
     
    </li>
    {/foreach}
  {/defun}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
	<li><b>Selected item property</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('editItemClassProperty'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt="" /> Edit this property</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteProperty');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/package_delete.png" border="0" alt="" /> Delete this property</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addPropertyToClass?class_id={$model.id}';" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Add a property to this model</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}';" class="right-nav-link"> <img src="{$domain}Resources/Icons/package_add.png" border="0" alt="" /> Add a new {$model.name|strtolower}</a></li>
</ul>

</div>
