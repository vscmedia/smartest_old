<script language="javascript" type="text/javascript">
{literal}
var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;


function setSelectedItem(item_id, pageName, rowColor){
	
	var row='vocabulary_'+item_id;
	var vocabulary_id = document.getElementById('vocabulary_id');
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
	vocabulary_id.value = item_id;
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
	var pageURL = '{$domain}{$section}/editVocabulary?vocabulary_id='+selectedPage;
	window.open(pageURL);
{literal}
}
{/literal}
</script>
<h3><a href="{$domain}{$section}">Data Manager</a> &gt; Vocabularies </h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="vocabulary_id" id="vocabulary_id" value="" />
</form>

<table border="0" cellspacing="10" cellpadding="0" style="width:850px">
  <tr>
    <td valign="top" style="width:550px">
<table cellpadding="0" cellspacing="0" border="0" style="width:550px;border:1px solid #ccc">

  <tr class="mainpanel-row-{cycle values="ddd,fff"}" id="vocabulary_0">
    <td style="padding-left:{$indent}px;cursor:pointer" class="text">
			<b>name<b>
		</td>
    <td style="padding-left:{$indent}px;cursor:pointer" class="text">
			<b>namespace</b>
		</td>
    <td style="padding-left:{$indent}px;cursor:pointer" class="text">
			<b>type</b>
		</td>
    <td style="padding-left:{$indent}px;cursor:pointer" class="text">
			<b>description</b>
		</td>
	</tr>
{foreach from=$content.vocabulary key=key item=vocabulary}
	
  <tr class="mainpanel-row-{cycle values="ddd,fff"}" id="vocabulary_{$vocabulary.vocabulary_id}" ondblclick="window.location='{$domain}{$section}/editVocabulary?vocabulary_id={$vocabulary.vocabulary_id}&vocabulary_id={$vocabulary.vocabulary_id}'"><!-- onmouseover="this.style.backgroundColor='#f90'" onmouseout="this.style.backgroundColor='{cycle name="return" values="fff,ddd"}'-->
    <td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$vocabulary.vocabulary_id}', '{$vocabulary.vocabulary_id|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text">
			<img src="{$domain}Resources/Images/spacer.gif" style="width:1px;height:22px;display:inline" border="0" alt="" /><img src="{$domain}Resources/Icons/page.png" border="0" alt=""> <a href="javascript:void(0);" class="mainpanel-link">{$vocabulary.vocabulary_name}</a>
		</td>
    <td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$vocabulary.vocabulary_id}', '{$vocabulary.vocabulary_id|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text">
			{$vocabulary.vocabulary_prefix}
		</td>
    <td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$vocabulary.vocabulary_id}', '{$vocabulary.vocabulary_id|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text">
			{$vocabulary.vocabulary_type}
		</td>
    <td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$vocabulary.vocabulary_id}', '{$vocabulary.vocabulary_id|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text">
			{$vocabulary.vocabulary_description}
		</td>
	</tr>
	
{/foreach}


</table></td>

<td valign="top" style="width:250px">Actions<ul class="actions-list">
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('newVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Add</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt="">Edit</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSchemaElement');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Delete</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSchemaElement');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Search</a></li>
</ul>

</td>

</tr>

</table>
