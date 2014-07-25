<div id="work-area">
    
  {* load_interface file="placeholder_tabs.tpl" *}
  <h3>Edit container</h3>
  
  <form id="updateContainer" name="updateContainer" action="{$domain}{$section}/updateContainer" method="post" style="margin:0px">
    <input type="hidden" name="container_id" value="{$container.id}">
    
    <div id="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Short Name (used in templates)</div>
        {$container.name}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="container_label" value="{$container.label}" />
      </div>

{if count($possible_groups)}
      <div class="edit-form-row">
        <div class="form-section-label">Restrict definitions to members of a template group?</div>
        <select name="container_filter">
          <option value="NONE">No restriction</option>
{foreach from=$possible_groups item="group"}
          <option value="{$group.id}"{if $group.id == $container.filter_value} selected="selected"{/if}>{$group.label}</option>
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