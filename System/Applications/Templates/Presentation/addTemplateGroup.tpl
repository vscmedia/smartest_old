<div id="work-area">
  
  <h3>Create a template group</h3>
  
  <form action="{$domain}{$section}/insertTemplateGroup" method="post">
  
    <div id="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Name this group</div>
        <input type="text" name="template_group_label" value="Untitled template group" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Which templates can go in this group?</div>
        <select name="template_group_type">
            <option value=""></option>
          {* <option value="ALL">Any type of template</option> *}

{foreach from=$template_types item="type"}
            <option value="{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}

        </select>
      </div>
      
      <div class="edit-form-row">
          <div class="form-section-label">Cross-site usage</div>
          <input type="checkbox" name="template_group_shared" value="1" id="template_group_shared" />
          <label for="template_group_shared">Share this group</label>
          <span class="form-hint">Makes this template group available to other sites you create in this Smartest install</span>
        </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="submit" value="Save" />
          <input type="button" onclick="cancelForm();" value="Cancel" />
        </div>
      </div>

    </div>
    
  </form>
  
</div>

<div id="actions-area">

</div>