<div id="work-area">

<h3>Create a new dropdown menu</h3>

<form method="post" action="{$domain}{$section}/insertDropDown"  onsubmit="return check();">

  <div class="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <input type="text" name="dropdown_label" id="drop_down" value="" />			
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="checkbox" name="continue_to_values" value="1" id="continue-to-values" checked="checked" />
        <label for="continue-to-values">Add values after saving</label>
        <input type="button" value="Cancel" onlick="cancelForm();">
        <input type="submit" value="Next &gt;&gt;" />
      </div>
    </div>
    
  </div>
				
</form>

</div>