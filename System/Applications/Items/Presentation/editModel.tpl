<div id="work-area">
  
  {load_interface file="edit_model_tabs.tpl"}
  
  <h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Model info</h3>
  
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
        <div class="form-section-label">Model class</div>
        <code>{$class_file}</code>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Model plural name</div>
        {if $allow_plural_name_edit}<input type="text" name="itemclass_plural_name" value="{$model.plural_name}" />{else}{$model.plural_name}{/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Shared on all sites</div>
        <input type="checkbox" name="itemclass_shared" id="itemclass-shared" value="1"{if $shared} checked="checked"{/if}{if !$allow_sharing_toggle} disabled="disabled"{/if} />
        
            {if $shared}
              {if $allow_sharing_toggle}
                <label for="itemclass-shared">Uncheck the box to make only this site able to store and display {$model.plural_name|lower}</label>
              {else}
                <span class="form-hint">
                {if $is_movable}
                  This model must be shared because 
                  {if $model.site_id == '0'}
                    it isn't currently attached to one of your sites
                  {else}
                    it is already in use on sites other than this one
                  {/if}
                {else}
                  This model cannot be unshared because file permissions do not allow the model's class file to be moved
                {/if}
                </span>
              {/if}
            {else}
              {if $allow_sharing_toggle}
                <label for="itemclass-shared">Check the box to make all sites able to store and display {$model.plural_name|lower}</label>
              {else}
                <span class="form-hint">
                  {if $is_movable}
                  This model cannot be shared because other models with conflicting or identical names exist on other sites
                  {else}
                  This model cannot be shared because file permissions do not allow the model's class file to be moved
                  {/if}
                </span>
              {/if}
            {/if}
            
            {if !$is_movable}
              <div class="warning">
                The following files must be writable by the web server before you can {if $shared}unshare this model{else}share this model with other sites{/if}:<br />
                {foreach from=$unwritable_files item="unwritable_file"}
                <div><code>{$unwritable_file}</code></div>
                {/foreach}
              </div>
            {/if}
            
      </div>
      
      {if $allow_main_site_switch}
      <div class="edit-form-row" id="">
        <div class="form-section-label">Main site</div>
        <select name="itemclass_site_id">
          {foreach from=$sites item="s"}
          <option value="{$s.id}"{if $current_site_id_id==$s.id} selected="selected"{/if}>{$s.name}</option>
          {/foreach}
        </select><span class="form-hint">The model's main site is the one that can use it if the model is not shared.</span>
      </div>
      {/if}
      
      {if count($metapages)}
      <div class="edit-form-row">
        <div class="form-section-label">Default Meta-Page</div>
        <select name="itemclass_default_metapage_id">
          <option value="NONE">No default</option>
          {foreach from=$metapages item="page"}
          <option value="{$page.id}"{if $model.default_metapage_id==$page.id} selected="selected"{/if}>{$page.title}</option>
          {/foreach}
        </select>
      </div>
      {/if}
      
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