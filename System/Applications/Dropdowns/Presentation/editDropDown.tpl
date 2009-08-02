<div id="work-area">

  <h3>Edit dropdown</h3>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="dropdown_id" id="drop_down" value="{$dropdown.id}" />
    <input type="hidden" name="dropdown_value_id" id="item_id_input" value="" />
  </form>

  <form id="pageViewForm" method="post" action="{$domain}{$section}/updateDropDown">
  
    <input type="hidden" name="dropdown_id" value="{$dropdown.id}" />
  
    <div id="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Label </div>
        <input type="text" name="dropdown_label" value="{$dropdown.label}" />
      </div>
      
      <div class="edit-form-row">
        
        <div class="form-section-label">Values in {$dropdown.label}. </div>

{if $dropdown.num_options > 0}

        <div class="special-box">
          Preview:
          <select name="preview">
{foreach from=$dropdown.values item="value"}
            <option value="">{$value.label}</option>
{/foreach}
          </select> <a href="{$domain}{$section}/addDropDownValue?dropdown_id={$dropdown.id}">+ Add a value</a>
        </div>

        	<ul class="options-list" id="options">

{foreach from=$dropdown.options key=key item="option"}
         	  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editDropDownValue?drop_down={$dropdown_details.id}&amp;drop_down_value_id={$option.id}'">
         	    <a class="option" id="item_{$option.id}" onclick="setSelectedItem('{$option.id}', '', 'value');" >
                  <img border="0" src="{$domain}Resources/Icons/package.png"> {$option.label}</a></li>
{/foreach}
        	</ul>
{else}
          <div class="special-box">This dropdown menu has no values yet. <a href="{$domain}{$section}/addDropDownValue?dropdown_id={$dropdown.id}">Click here</a> to add a new value.</div>
{/if}
      
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onlick="cancelForm();">
          <input type="submit"  value="Save changes">
        </div>
      </div>

    </div>

  </form>

</div>

<div id="actions-area">
  <ul class="actions-list" id="value-specific-actions" style="display:none">
    <li><b>Selected value</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('editDropDownValue');"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit value</a></li>
{if $dropdown.num_options > 1}
    <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('moveDropDownValueUp');"><img border="0" src="{$domain}Resources/Icons/arrow_up.png"> Move value up</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('moveDropDownValueDown');"><img border="0" src="{$domain}Resources/Icons/arrow_down.png"> Move value down</a></li>
{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteDropDownValue');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete value</a></li>
  </ul>
</div>