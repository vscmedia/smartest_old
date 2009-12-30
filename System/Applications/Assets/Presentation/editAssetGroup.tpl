<div id="work-area">
  
  {load_interface file="edit_filegroup_tabs.tpl"}  
  
  <h3>Edit file group "{$group.label}"</h3>
  
  <form action="{$domain}{$section}/updateAssetGroup" method="post">
    
    <input type="hidden" name="group_id" value="{$group.id}" />
    
    <div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="group_label" value="{$group.label}" />
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Short name</div>
        {if $allow_name_edit}<input type="text" name="group_name" value="{$group.name}" /><span class="form-hint">letters, numbers and underscored only, please.</span>{else}{$group.name}{/if}
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Shared</div>
        <input type="checkbox" name="group_shared" id="shared" value="1"{if $group.shared == "1"} checked="checked"{/if}{if !$allow_shared_toggle} disabled="disabled"{/if} />
        {if $allow_shared_toggle}
          <label for="shared">{if $shared}Uncheck this box to make this group available only to this site. {else}Check this box to make this group available to all sites. {/if}</label>
        {else}
          <span class="form-hint">This group is in use to define one or more placeholders, which are not site specific, so it must be shared.</span>
        {/if}
      </div>
    
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="submit" value="Save changes" />
        </div>
      </div>
    
    </div>
  
  </form>
  
</div>

<div id="actions-area">
  
</div>