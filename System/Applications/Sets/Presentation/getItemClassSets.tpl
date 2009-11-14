<div id="work-area">

<h3><a href="{$domain}smartest/data">Items</a> &gt; <a href="{$domain}smartest/models">Models</a> &gt; <a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Sets</h3>

<div class="instruction">Use Data Sets to organize your data into smaller groups.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id" id="item_id_input" value="" />
</form>


<ul class="{if $content.itemClassMemberCount > 10}options-list{else}options-grid{/if}" id="{if $content.itemClassMemberCount > 10}options_list{else}options_grid{/if}">
{foreach from=$sets key="key" item="set"}
  <li style="list-style:none;" 
			ondblclick="window.location='{$domain}{$section}/editSet?set_id={$set.id}'">
			<a class="option" id="item_{$set.id}" onclick="setSelectedItem('{$set.id}', 'fff', '{if $set.type == 'STATIC'}static{else}dynamic{/if}');" >
			  <img border="0" src="{$domain}Resources/Icons/folder.png">
			  {$set.name} ({$set.type|lower})</a></li>
{/foreach}
</ul>
</div>

<div id="actions-area">
  
<ul class="actions-list" id="static-specific-actions" style="display:none">
  <li><b>Selected Data Set</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editSet');}{/literal}"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Edit contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('previewSet');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_go.png"> View contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editStaticSetOrder');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/arrow_switch.png"> Change order</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('copySet');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Duplicate<!--structure, not template because it does not propigate back to template--></a></li> *}
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSet');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_delete.png"> Delete</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('chooseSchemaForExport');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export</a></li> *}
</ul>

<ul class="actions-list" id="dynamic-specific-actions" style="display:none">
  <li><b>Selected Dynamic Data Set</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editSet');}{/literal}"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Edit Contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('previewSet');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_go.png"> View Contents</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('copySet');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Duplicate<!--structure, not template because it does not propigate back to template--></a></li> *}
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSet');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_delete.png"> Delete</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('chooseSchemaForExport');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export</a></li> *}
</ul>

<ul class="actions-list">
  <li><b>Data Options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addSet?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/folder_add.png"> Make a new set of {$model.plural_name}</a></li>  
  <li class="permanent-action"><a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}"><img border="0" src="{$domain}Resources/Icons/package.png" style="width:16px;height:18px"> Browse all {$model.plural_name}</a></li>
  {* <li class="permanent-action"><a href="{$domain}sets/getDataExports"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Feeds</a></li>
  <li class="permanent-action"><a href="{$domain}smartest/schemas"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li> *}
</ul>

</div>




