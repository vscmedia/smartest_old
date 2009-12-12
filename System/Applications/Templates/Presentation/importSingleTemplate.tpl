<div id="work-area">
  <h3>Import a template</h3>
  {if $show_form}
  <div class="special-box">You are importing <strong><code>{$template.file_path}</code></strong> into Smartest's new templates database</div>
  <form action="{$domain}{$section}/addSingleTemplateToDatabase" method="post">
    
    <input type="hidden" name="template_filename" value="{$template.url}" />
    <input type="hidden" name="template_current_storage" value="{$template.storage_location}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Import as</div>
      <select name="template_type" onchange="$('type-field').innerHTML=this.value;">
        {foreach from=$template_types item="typeoption"}
        <option value="{$typeoption.id}"{if $typeoption.id == $template.type} selected="selected"{/if}>{$typeoption.label}{if $typeoption.storage.location != $template.storage_location} (will be moved to {$typeoption.storage.location}){/if}</option>
        {/foreach}
      </select><span class="form-hint">(<span id="type-field">{$template.type}</span>)</span>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Name</div>
      <input type="text" name="template_name" value="{$template.suggested_name}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Multi-site sharing</div>
      <input type="checkbox" name="template_shared" value="{$template.suggested_name}" id="template-shared" />
      <label for="template-shared">Check this box to make this template available to all the websites you host in Smartest</label>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm();">
        <input type="submit" value="Import" />
      </div>
    </div>
    
  </form>
  {/if}
</div>

<div id="actions-area">

</div>