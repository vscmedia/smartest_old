<div id="work-area">
    
  {load_interface file="placeholder_tabs.tpl"}
  <h3>Edit placeholder</h3>
  
  <form id="updatePlaceholder" name="updatePlaceholder" action="{$domain}{$section}/updatePlaceholder" method="post" style="margin:0px">
    <input type="hidden" name="placeholder_id" value="{$placeholder.id}">
    
    <div id="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Short Name (used in templates)</div>
        {$placeholder.name}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="placeholder_label" value="{$placeholder.label}" />
      </div>

{if count($possible_groups)}
      <div class="edit-form-row">
        <div class="form-section-label">Restrict definitions to members of a file group?</div>
        <select name="placeholder_filter">
          <option value="NONE">No restriction</option>
{foreach from=$possible_groups item="group"}
          <option value="{$group.id}"{if $group.id == $placeholder.filter_value} selected="selected"{/if}>{$group.label}</option>
{/foreach}
        </select>
      </div>
{/if}

      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" name="action" value="Save Changes" />
        </div>
      </div>
    
    </div>
    
  </form>
  
</div>

<div id="actions-area">

</div>