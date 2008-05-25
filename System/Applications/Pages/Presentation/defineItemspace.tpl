<div id="work-area">
  
  <h3>Define Itemspace</h3>
  
  <div class="instruction">Choose an item to fill itemspace '{$itemspace.label}' on page '{$page.title}'</div>
  
  <form action="{$domain}{$section}/updateItemspaceDefinition" method="post">
    
    <input type="hidden" name="itemspace_id" value="{$itemspace.id}" />
    <input type="hidden" name="itemspace_name" value="{$itemspace.name}" />
    <input type="hidden" name="page_id" value="{$page.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Chosen Item</div>
      <select name="item_id">
        {foreach from=$options item="option"}
        <option value="{$option.id}"{if $option.id == $definition_id} selected="sselected"{/if}>{$option.name}</option>
        {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Save" />
        <input type="button" value="Cancel" onclick="cancelForm()" />
      </div>
    </div>
    
  </form>
  
</div>