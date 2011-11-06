<div id="work-area">
  
  <h3>Add a page group</h3>
  <form method="post" action="{$domain}{$section}/insertPageGroup">

    <div class="edit-form-layout">

      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="pagegroup_label" id="pagegroup" value="" />			
      </div>

      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="checkbox" name="continue_to_pages" value="1" id="continue-to-pages" checked="checked" />
          <label for="continue-to-pages">Add pages after saving</label>
          <input type="button" value="Cancel" onclick="cancelForm();">
          <input type="submit" value="Next &gt;&gt;" />
        </div>
      </div>

    </div>

  </form>
</div>

<div id="actions-area">
  
</div>