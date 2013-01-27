<div id="work-area">
  
  {load_interface file="edit_model_tabs.tpl"}
  
  <h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Edit model</h3>
  
  {if $can_edit_model}<form action="{$domain}{$section}/updateModel" method="post">{/if}
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    
    <div class="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Model plural name</div>
        {if $allow_plural_name_edit}<input type="text" name="itemclass_plural_name" value="{$model.plural_name}" />{else}{$model.plural_name}{/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Build-in 'name' field title</div>
        {if $allow_infn_edit}<input type="text" name="itemclass_item_name_field_name" value="{$model.item_name_field_name}" /><div class="form-hint">Usually something like 'name', 'title', 'headline', 'label'</div>{else}{$model.item_name_field_name}{/if}
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
                  This model must be shared because it is already in use in more than one website.
                {else}
                  This model cannot be unshared because file permissions do not allow the model's class file to be moved.
                {/if}
                </span>
              {/if}
            {else}
              {if $allow_sharing_toggle}
                <label for="itemclass-shared">Check the box to make all sites able to store and display {$model.plural_name|lower}</label>
              {else}
                <span class="form-hint">
                  {if $is_movable}
                  This model cannot be shared because other models with conflicting or identical names exist on other sites.
                  {else}
                  This model cannot be shared because file permissions do not allow the model's class file to be moved.
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
        {if $can_edit_model}
        <select name="itemclass_site_id">
          {foreach from=$sites item="s"}
          <option value="{$s.id}"{if $current_site_id_id==$s.id} selected="selected"{/if}>{$s.name}</option>
          {/foreach}
        </select><span class="form-hint">The model's main site is the one that can use it if the model is not shared.</span>
        {else}
          {foreach from=$sites item="s"}
            {if $current_site_id_id==$s.id}{$s.name}{/if}
          {/foreach}
        {/if}
      </div>
      {/if}
      
      {if count($metapages)}
      <div class="edit-form-row">
        <div class="form-section-label">Default meta-page</div>
        {if $can_edit_model}
        <select name="itemclass_default_metapage_id">
          <option value="NONE">No default</option>
          {foreach from=$metapages item="page"}
          <option value="{$page.id}"{if $model.default_metapage_id == $page.id} selected="selected"{/if}>{$page.title}</option>
          {/foreach}
        </select>
        {else}
        {foreach from=$metapages item="page"}
          {if $model.default_metapage_id == $page.id}{$page.title}{/if}
          {/foreach}
        {/if}
      </div>
      {/if}
      
      {if count($available_primary_properties)}
      <div class="edit-form-row">
          <div class="form-section-label">Primary property</div>
          {if $can_edit_model}
          <select name="itemclass_primary_property_id">
            <option value="NONE">None</option>
            {foreach from=$available_primary_properties item="property"}
            <option value="{$property.id}"{if $model.primary_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
            {/foreach}
          </select>
          {else}
          {if $model.primary_property_id}
          {foreach from=$available_primary_properties item="property"}
            {if $model.primary_property_id == $property.id}{$property.name}{/if}
          {/foreach}
          {else}
          None
          {/if}
          {/if}
        </div>
      {/if}
      
      <div class="edit-form-row">
        <div class="form-section-label">Default description property</div>
        {if $can_edit_model}
        <select name="itemclass_default_description_property_id">
          {if !$model.default_description_property_id}<option value="0"></option>{/if}
          {foreach from=$description_properties item="property"}
          <option value="{$property.id}"{if $model.default_description_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
          {/foreach}
        </select>
        {else}
        {if $model.default_description_property_id}
          {foreach from=$description_properties item="property"}
            {if $model.default_description_property_id == $property.id}{$property.name}{/if}
          {/foreach}
        {else}
          None
        {/if}
        {/if}
      </div>
      
      <div class="edit-form-row">
          <div class="form-section-label">Default sort property</div>
          {if $can_edit_model}
          <select name="itemclass_default_sort_property_id">
            <option value="0">{$model.name} {$model.item_name_field_name}</option>
            {foreach from=$sort_properties item="property"}
            <option value="{$property.id}"{if $model.default_sort_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
            {/foreach}
          </select>
          {else}
          {if $model.default_sort_property_id}
            {foreach from=$sort_properties item="property"}
              {if $model.default_sort_property_id == $property.id}{$property.name}{/if}
            {/foreach}
          {else}
            None
          {/if}
          {/if}
        </div>
        
        <div class="edit-form-row">
              <div class="form-section-label">Default thumbnail property</div>
              {if $can_edit_model}
              <select name="itemclass_default_thumbnail_property_id">
                <option value="0">None</option>
                {foreach from=$thumbnail_properties item="property"}
                <option value="{$property.id}"{if $model.default_thumbnail_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
                {/foreach}
              </select>
              {else}
              {if $model.default_thumbnail_property_id}
                {foreach from=$thumbnail_properties item="property"}
                  {if $model.default_thumbnail_property_id == $property.id}{$property.name}{/if}
                {/foreach}
              {else}
                None
              {/if}
              {/if}
            </div>
      
      <div class="edit-form-row">
          <div class="form-section-label">Color</div>
          {if $can_edit_model}
          <input type="text" class="color" name="itemclass_color" value="{$model.color}" />
          {else}
          <span style="color:#{$model.color.hex};font-weight:bold">#{$model.color.hex}</span>
          {/if}
        </div>
      
      {if $can_edit_model}<div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm()" />
          <input type="submit" value="Save Changes" />
        </div>
      </div>{/if}
      
    </div>
    
  {if $can_edit_model}</form>{/if}
  
</div>

<div id="actions-area">
    
    <ul class="actions-list" id="non-specific-actions">
      <li><b>Model Options</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/modelInfo?class_id={$model.id}', 'Model info');"><img border="0" src="{$domain}Resources/Icons/information.png" /> Model info</a></li>
      {if $allow_create_new}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/add.png" /> Add a new {$model.name}</a></li>{/if}
      {if $can_edit_properties}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/tag_blue_edit.png" /> Edit model properties</a></li>{/if}
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseUserHeldItems?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/lock_open.png" /> Release all {$model.plural_name}</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png" /> Create a new set from this model</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/getItemClassSets?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/folder_old.png" /> View data sets for this model</a></li>
    {* <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/importData?class_id={$itemBaseValues.itemclass_id}';"><img border="0" src="{$domain}Resources/Icons/page_code.png" /> Import data from CSV</a></li> *}
    </ul>
    
    <ul class="actions-list" id="non-specific-actions">
      <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
      {foreach from=$recent_items item="recent_item"}
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label|summary:"28"}</a></li>
      {/foreach}
    </ul>
    
</div>