<div id="work-area">

<h3>Values in {$dropdown.label}</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="dropdown_id" id="drop_down" value="{$dropdown.id}" />
  <input type="hidden" name="dropdown_value_id" id="item_id_input" value="" />
</form>

{if !empty($options)}
	<ul class="{if count($options) > 10}options-list{else}options-grid{/if}" id="options">

{foreach from=$dropdown_options key=key item="option"}
 	  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editDropDownValue?drop_down={$dropdown_details.id}&drop_down_value_id={$option.id}'">
 	    <a class="option" id="item_{$option.id}" onclick="setSelectedItem('{$option.id}', 'fff');" >
          <img border="0" src="{$domain}Resources/Icons/package.png"> {$option.label}</a></li>
{/foreach}
	</ul>
{else}
  <div class="special-box">This dropdown menu has no values yet. <a href="{$domain}{$section}/addDropDownValue?dropdown_id={$dropdown.id}">Click here</a> to add a new value.</div>
  
{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  <li><b>Selected value</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editDropDownValue');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteDropDownValue');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete</a></li>
</ul>

<ul class="actions-list">
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addDropDownValue?drop_down={$dropdown.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Add DropDown value</a></li> 
  {if !empty($options)}<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/reorderDropDownValue?drop_down={$dropdown.id}'"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Re-order DropDownValues</a></li>{/if}
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Drops Down</a></li>
</ul>

</div>