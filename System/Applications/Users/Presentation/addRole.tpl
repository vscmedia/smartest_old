<div id="work-area">
    
  <h3>Add a new role</h3>
  <div class="instruction">Roles are pre-defined lists of permissions that can be easily assigned when new users are created</div>
  <form action="{$domain}{$section}/insertRole" method="post">
    
    <div class="edit-form-row">
      <div class="form-section-label">Role name</div>
      <input type="text" name="role_label" style="width:200px" /><span class="form-hint">Pick a name that is descriptive of what this type of user will be doing</span>
    </div>
    
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
    
  </form>
  
</div>

<div id="actions-area">

</div>