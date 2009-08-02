<div id="work-area">

<h3>Add dropdown value</h3>

<form id="pageViewForm" method="post" action="{$domain}{$section}/insertDropDownValue" onsubmit="return check();">

<input type="hidden" name="dropdown_id" value="{$dropdown.id}" />

<div class="instruction">You are adding a value to the dropdown menu "{$dropdown.label}"</div>

<div class="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Label:</div>
    <input type="text" name="dropdownvalue_label" id="drop_down_label" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Value:</div>
    <input type="text" name="dropdownvalue_value" id="drop_down_value" /><span class="form-hint">numbers, lowercase letters, and underscores only please</span>
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onlick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
  </div>

</div>		

</form>

</div>