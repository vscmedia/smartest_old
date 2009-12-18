<div id="work-area">
  <h3>Convert template type</h3>
  {if $is_convertable}
  <form action="{$domain}{$section}/updateTemplateType" method="post">
    <input type="hidden" name="template_id" value="{$template.id}" />
    <div class="edit-form-row">
      <div class="form-section-label">Template file</div>
      <code>{$current_type.storage.location}{$template.url}</code>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Current type</div>
      {$current_type.label}; {$current_type.description} <cpan class="form-hint">({$current_type.id})</span>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Convert to</div>
      <select name="new_type" onchange="$('type-field').innerHTML=this.value;">
        {foreach from=$possible_types item="newtype"}
        <option value="{$newtype.id}">{$newtype.label}{if $newtype.storage.location != $current_type.storage.location} (will be moved to {$newtype.storage.location}{$template.url}){/if}</option>
        {/foreach}
      </select><span class="form-hint">(<span id="type-field">{$possible_types[0].id}</span>)</span>
    </div>
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm()" />
        <input type="submit" value="Convert &amp; save" />
      </div>
    </div>
  </form>
  {else}
    <div class="warning">This template isn't convertable because it is either the wrong type or already in use as its current type</div>
  {/if}
</div>

<div id="actions-area">
  
</div>