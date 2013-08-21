<div id="work-area">
  <h3>Add a new Itemspace</h3>
  
  {if $allow_continue}
  
  <div class="instruction"></div>
  
  <form action="{$domain}{$section}/insertItemSpace" method="post">
    
    <div class="edit-form-row">
      <div class="form-section-label">Name</div>
      {$name}<input type="hidden" name="itemspace_name" value="{$name}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Data set</div>
      <select name="itemspace_dataset_id">
        {foreach from=$sets item="dataset"}
        <option value="{$dataset.id}">{$dataset.label}</option>
        {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Use specific template?</div>
      {if empty($templates)}
      <span class="form-hint">No itemspace templates are currently available</span>
      {else}
      <input type="checkbox" name="itemspace_use_template" value="1" />
      <select name="itemspace_template_id">
        {foreach from=$templates item="template"}
        <option value="{$template.id}">{$template.url}</option>
        {/foreach}
      </select>
      {/if}
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Save" />
        <input type="button" value="Cancel" onclick="cancelForm()" />
      </div>
    </div>
    
  </form>
  
  {else}
  
  <div class="instruction"></div>
  
  {/if}
  
</div>

<div id="actions-area">
  
</div>