<div id="work-area">
  <h3>Edit Itemspace: {$itemspace.name}</h3>
  <form action="{$domain}{$section}/updateItemspace" method="post">
    
    <input type="hidden" name="itemspace_id" value="{$itemspace.id}" />
    
    <div id="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Name</div>
        {$itemspace.name}
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="itemspace_label" value="{$itemspace.label}" />
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Data set</div>
        <select name="itemspace_dataset_id">
{foreach from=$sets item="set"}
          <option value="{$set.id}"{if $itemspace.params.dataset_id == $set.id} selected="selected"{/if}>{$set.label}</option>
{/foreach}
        </select>
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Template</div>
        <select name="itemspace_template_id">
          <option value="NONE">None</option>
{foreach from=$templates item="template"}
          <option value="{$template.id}"{if $itemspace.params.template_asset_id == $template.id} selected="selected"{/if}>{$template.url}</option>
{/foreach}
        </select>
      </div>
    
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm()" />
        <input type="submit" value="Save" />
      </div>
    
    </div>
  </form>
</div>

<div id="actions-area">
  
</div>