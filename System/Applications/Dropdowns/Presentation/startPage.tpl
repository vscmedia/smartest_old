<div id="work-area">

<h3>Dropdown menus</h3>

{if count($dropdowns)}

<div class="instruction">These dropdown menus can be used as inputs anywhere on your site.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="dropdown_id" id="item_id_input" value="" />
</form>

<ul class="{if $count > 10}options-list{else}options-grid{/if}" id="{if $count > 10}options_list{else}options_grid{/if}">
{foreach from=$dropdowns key=key item=dropdown}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/dropdownInfo?dropdown_id={$dropdown.dropdown_id}'">
    <a class="option" id="item_{$dropdown.id}" onclick="setSelectedItem('{$dropdown.id}');" >
      <img border="0" src="{$domain}Resources/Icons/package.png" />{$dropdown.dropdown_label}</a></li>
{/foreach}
</ul>
{else}

<div class="special-box">You have no dropdown menus yet. <a href="{$domain}{$section}/addDropDown">Click here</a> to create one.</div>

{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected dropdown</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){workWithItem('dropdownInfo');}{/literal}"><img border="0" src="{$domain}Resources/Icons/information.png"> Dropdown info</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){workWithItem('editValues');}{/literal}"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit values</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteDropDown');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete dropdown</a></li>
</ul>
<ul class="actions-list">
  <li><b>Dropdown options</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addDropDown'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Create a new dropdown</a></li>  
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/metadata'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Back to metadata</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/models'"><img border="0" src="{$domain}Resources/Icons/package_small.png"> Items</a></li>
</ul>

</div>





