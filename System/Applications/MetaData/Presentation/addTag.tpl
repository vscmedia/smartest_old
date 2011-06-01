<div id="work-area">
<h3>Add Tag</h3>

<div class="instruction">Enter one or more tags. Separate multiple tags with spaces.</div>

<form action="{$domain}{$section}/insertTag" method="post">
  <div id="edit-form-layout">
    <div class="edit-form-row">
      <div class="form-section-label">Tag Name(s): </div>
      <input type="text" name="tag_label" />
    </div>
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm();" />
        <input type="submit" name="action" value="Save" />
      </div>
    </div>
  </div>
</form>

</div>

<div id="actions-area">
  
</div>