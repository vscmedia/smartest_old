<div id="work-area">
  <h3>Publish {$item._model.name}: {$item.name}</h3>
  
  <div class="instruction">Publish options</div>
  
  <form action="{$domain}{$section}/updateItem">
    
    <input type="hidden" name="item_id" value="{$item.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Update pages where this {$item._model.name|strtolower} is referenced?</div>
      <select name="update_itemspaces">
        <option value="IGNORE">No, I'll do that manually</option>
        <option value="UPDATE" selected="selected">Yes, but don't actually publish them</option>
        <option value="PUBLISH">Yes, and try to publish them (where authorized)</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Publish" />
        <input type="button" value="Cancel" onclick="cancelForm()" />
      </div>
    </div>
    
  </form>
  
</div>