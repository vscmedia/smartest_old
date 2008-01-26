<script language="javascript">

var customAssetClassName = false;

{literal}

function updateAssetClassName(){
	if(!customAssetClassName){
		// alert('test');
		// $("assetclass_name").value = $("assetclass_label").value.toSlug();
	}
}

{/literal}
</script>

<div id="work-area">

<h3>Website Manager &gt; Assets &gt; Add a New Placeholder</h3>

<form action="{$domain}assets/insertPlaceholder" method="post" style="margin:0px">
  
{if $name}
  <input type="hidden" name="placeholder_name" value="{$name}" />
{/if}

  <div id="edit-form-layout">

    <div class="edit-form-row">
      <div class="form-section-label">Label:</div>
      <input type="text" name="placeholder_label" id="placeholder_label" {if !$name}onkeyup="updateAssetClassName();"{/if} />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Markup/tag name: </div>
      {if $name}{ldelim}placeholder name="{$name}"{rdelim}{else}<input type="text" name="placeholder_name" id="placeholder_name" value="{$name}" /><br />
        <span>If you don't enter a tag name, one will be generated for you.</span>{/if}
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Content type:</div>
        <select name="placeholder_type">
          {foreach from=$types item="type"}
          {if $type.id != 'SM_ASSETCLASS_CONTAINER'}<option value="{$type.id}">{$type.label}</option>{/if}
          {/foreach}
{* foreach from=$types item="typecat" 
    <optgroup label="Text">
      {foreach from=$types.user_text item="type"}
      <option value="{$type.id}">{$type.label}</option>
      {/foreach}
    </optgroup>
    <optgroup label="Image">
      {foreach from=$types.image item="type"}
      <option value="{$type.id}">{$type.label}</option>
      {/foreach}
    </optgroup>
    <optgroup label="Browser Instructions">
      {foreach from=$types.browser_instructions item="type"}
      <option value="{$type.id}">{$type.label}</option>
      {/foreach}
    </optgroup>
    <optgroup label="Embedded Elements">
      {foreach from=$types.embedded item="type"}
      <option value="{$type.id}">{$type.label}</option>
      {/foreach}
    </optgroup>
 /foreach *}
    
  </select>
  </div>
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
  </div>
</form>

</div>