<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; DropDowns</h3>
<a name="top"></a>

<div class="instruction">Your data is collected into functionally distinct types called DropDowns. Please choose one to continue.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="drop_down" id="item_id_input" value="" />
</form>

<ul class="{if $content.count > 10}options-list{else}options-grid{/if}" id="{if $content.count > 10}options_list{else}options_grid{/if}">
{foreach from=$dropdowns key=key item=dropdown}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/viewDropDown?drop_down={$dropdown.dropdown_id}'">
  <a class="option" id="item_{$dropdown.dropdown_id}" onclick="setSelectedItem('{$dropdown.dropdown_id}');" >
  <img border="0" src="{$domain}Resources/Icons/package.png">
  {$dropdown.dropdown_label}</a></li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editDropDown');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit</a></li>
 <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteDropDown');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete</a></li>
 <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('viewDropDown');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> View DropDownValues</a></li>
</ul>
<ul class="actions-list">
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addDropDown'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Create DropDown</a></li>  
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}datamanager/'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Data Manager</a></li>
</ul>

</div>





