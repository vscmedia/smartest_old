<div id="work-area">

<h3>Edit dropdown value</h3>

<form id="pageViewForm" method="post" action="{$domain}{$section}/updateDropDownValue"  onsubmit="return check();">

<input type="hidden" name="dropdown_id" value="{$dropdown.id}" />
<input type="hidden" name="dropdown_value_id" value="{$option.id}" />

<div class="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Label</div>
    <input type="text" name="dropdown_label" id="drop_down_label" value="{$option.label}">
  </div>
  
  <div class="edit-form-row">
      <div class="form-section-label">Value</div>
      {$option.value}
    </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save Changes" />
    </div>
  </div>

</div>

</form>

</div>