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

<h3>Add a new placeholder</h3>

<form action="{$domain}assets/insertPlaceholder" method="post" style="margin:0px">

  <div id="edit-form-layout">

{if $name}
    <div class="edit-form-row">
      <div class="form-section-label">Markup </div>
      <code>&lt;?sm:placeholder name="{$name}":?&gt;</code><input type="hidden" name="placeholder_name" value="{$name}" />
    </div>
{else}
    <div class="edit-form-row">
      <div class="form-section-label">Name </div>
      <input type="text" name="placeholder_name" id="placeholder_name" value="{$name}" />
        <span class="form-hint">If you don't enter a tag name, one will be generated for you.</span>
    </div>
{/if}
    <div class="edit-form-row">
      <div class="form-section-label">Content type:</div>
        <select name="placeholder_type">
          {foreach from=$types item="type"}
          {if $type.hide != 'true'}<option value="{$type.id}">{$type.label}</option>{/if}
          {/foreach}
  </select>
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Label (optional):</div>
    <input type="text" name="placeholder_label" id="placeholder_label" {if !$name}onkeyup="updateAssetClassName();"{/if} />
  </div>
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
  </div>
</form>

</div>