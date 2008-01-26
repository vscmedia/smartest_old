<script language="javascript" type="text/javascript">
{literal}
var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;


function setSelectedItem(item_id, pageName, rowColor){
	
	var row='schemadefinition_'+item_id;
	var editForm = document.getElementById('pageViewForm');
	rowColor='#'+rowColor;
	selectedPage = item_id;
	selectedPageName = pageName;
	if(lastRow){
		document.getElementById(lastRow).style.backgroundColor=lastRowColor;
	}
	document.getElementById(row).style.backgroundColor='#99F';
	{/literal}
	{literal}
	lastRow = row;
	lastRowColor = rowColor;
	editForm.schemadefinition_id.value = item_id;
}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm){
		
		editForm.action=pageAction;
		editForm.submit();
		
	}
}

function viewPage(){
{/literal}
	var pageURL = '{$domain}vocabulary/editSchemaVocabulary?schemadefinition_id='+selectedPage;
	window.open(pageURL);
{literal}
}
{/literal}



</script>
<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}/getItemSchemas"> Templates</a>  &gt; {$content.schema.schema_name} </h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="schemadefinition_id" value="" />
<input type="hidden" name="schema_id" value="{$schema.schema_id}" />
<input type="hidden" name="vocabulary_id" value="{$schema.vocabulary_id}" />
</form>

<table border="0" cellspacing="10" cellpadding="0" style="width:850px">
  <tr>
    <td valign="top" style="width:550px">
<table cellpadding="0" cellspacing="0" border="0" style="width:550px;border:1px solid #ccc">


	<tr>
	<td>
		
		<ul style="list-style:none" class="options" id="options_list">
			{defun name="menurecursion" list=$definition}
			 {foreach from=$list item=element}
			 <li ondblclick="window.location='{$domain}vocabulary/editSchemaVocabulary?schemadefinition={$element.schemadefintion_id}'">			 <a id="item_{$element.schemadefinition_id}" href="#" onclick="setSelectedItem('{$element.schemadefinition_id}', '{$element.vocabulary_name}', 'fff');">
			 <a id="item_51" class="option" href="#" onclick="setSelectedItem('{$element.schemadefinition_id}', '{$element.vocabulary_name}', 'fff');">		 
			 <img border="0" src="http://smartest.dev.visudo.net/Resources/Icons/page_code.png" />
			 {$element.vocabulary_name}
			 </a>
			 {if !empty($element.children)}
					<ul style="list-style:none" class="options">{fun name="menurecursion" list=$element.children}</ul>
			 {/if}
			 </li>
			 {/foreach}
		{/defun}
		</ul>
	</td>
	</tr>


</table></td>

<td valign="top" style="width:250px">Actions<ul class="actions-list">
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('addVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Add</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editSchemaVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt="">Edit</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSchemaElement');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Delete</a></li>
</ul>

</td>

</tr>

</table>
