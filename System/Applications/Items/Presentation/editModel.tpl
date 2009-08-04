<div id="work-area">
  
  <h3>Edit Model</h3>
  
  <form action="{$domain}{$section}/updateModel" method="post">
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    
    <div class="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Model Name</div>
        {$model.name}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Number of items</div>
        This site: <strong>{$num_items_on_site}</strong>; All sites: <strong>{$num_items_all_sites}</strong>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Model Class</div>
        Library/ObjectModel/{$model.name|camelcase}.class.php
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Model Plural Name</div>
        <input type="text" name="itemclass_plural_name" value="{$model.plural_name}" style="width:250px" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Default Meta-Page</div>
        <select name="itemclass_default_metapage_id">
          <option value="NONE">No default</option>
          {foreach from=$metapages item="page"}
          <option value="{$page.id}"{if $model.default_metapage_id==$page.id} selected="selected"{/if}>{$page.title}</option>
          {/foreach}
        </select>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Description Property</div>
        <select name="itemclass_default_description_property_id">
          {if !$model.default_description_property_id}<option value="0"></option>{/if}
          {foreach from=$description_properties item="property"}
          <option value="{$property.id}"{if $model.default_description_property_id==$property.id} selected="selected"{/if}>{$property.name}</option>
          {/foreach}
        </select>
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm()" />
          <input type="submit" value="Save Changes" />
        </div>
      </div>
      
    </div>
    
  </form>
  
</div>

<div id="actions-area">
  
</div>