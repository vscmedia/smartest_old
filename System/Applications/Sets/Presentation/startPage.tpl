<div id="work-area">

<h3><a href="{$domain}smartest/data">Items</a> &gt; Sets</h3>

{load_interface file="items_front_tabs.tpl"}

<div class="instruction">Use Data Sets to organize your data into smaller groups.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id" id="item_id_input" value="" />
</form>


<ul class="{if count($sets) > 10}options-list{else}options-grid{/if}" id="{if count($sets) > 10}options_list{else}options_grid{/if}">
{foreach from=$sets key="key" item="set"}
  <li style="list-style:none;" 
			ondblclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}'">
			<a class="option" id="item_{$set.id}" onclick="setSelectedItem('{$set.id}');" >
			  <img border="0" src="{$domain}Resources/Icons/folder.png">
			  {$set.name} ({$set.type|lower})</a></li>
{/foreach}
</ul>
</div>

<div id="actions-area">
  
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected Data Set</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editSet');}{/literal}"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Modify data set contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('previewSet');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_go.png"> List data set contents</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('copySet');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Duplicate<!--structure, not template because it does not propigate back to template--></a></li> *}
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('deleteSetConfirm');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_delete.png"> Delete this data set</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('chooseSchemaForExport');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export</a></li> *}
</ul>

<ul class="actions-list">
  <li><b>Data Options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addSet'"><img border="0" src="{$domain}Resources/Icons/folder_add.png"> Create A New Data Set</a></li>  
  <li class="permanent-action"><a href="{$domain}smartest/models"><img border="0" src="{$domain}Resources/Icons/package.png" style="width:16px;height:18px"> Browse Data in Models</a></li>
  {* <li class="permanent-action"><a href="{$domain}sets/getDataExports"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Feeds</a></li>
  <li class="permanent-action"><a href="{$domain}smartest/schemas"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li> *}
</ul>

</div>




