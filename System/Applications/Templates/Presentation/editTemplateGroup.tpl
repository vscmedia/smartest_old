<div id="work-area">
  
  {load_interface file="template_group_edit_tabs.tpl"}  
  
  <h3>Edit file group "{$group.label}"</h3>
  
  <form action="{$domain}{$section}/updateTemplateGroup" method="post">
    
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
      
  {if $allow_type_change}
    <div class="edit-form-row">
      <div class="form-section-label">Which type of templates can go in this group?</div>
      <select name="template_group_type">

{foreach from=$template_types item="type"}
          <option value="{$type.id}"{if $group.filter_value == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}

      </select>
    </div>
  {/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Restrict templates to a specific data model?</div>
      <select name="template_group_model_id">
          
          <option value="0"{if $group.itemclass_id == 0} selected="selected"{/if}>None</option>
{foreach from=$models item="model"}
          <option value="{$model.id}"{if $group.itemclass_id == $model.id} selected="selected"{/if}>{$model.plural_name}</option>
{/foreach}

      </select>
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