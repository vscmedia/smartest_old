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

<form style="margin:0px" action="{$_here}" method="get" id="type-form">

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
        <select name="placeholder_type" id="type-select">
          {foreach from=$types item="type"}
          <option value="{$type.id}"{if $type.id == $selected_type} selected="selected"{/if}>{$type.label}</option>
          {/foreach}
        </select>
        <script type="text/javascript">
          {literal}$('type-select').onchange = function(){$('type-form').submit()};{/literal}
        </script>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Label (optional):</div>
      <input type="text" name="placeholder_label" id="placeholder_label" value="{$label}" {if !$name}onkeyup="updateAssetClassName();"{/if} />
    </div>
    
</form>

<form action="{$domain}{$section}/insertPlaceholder" method="post" style="margin:0px">
    
  <input type="hidden" name="placeholder_name" value="{$name}" />
  <input type="hidden" name="placeholder_type" value="{$selected_type}" />
  <input type="hidden" name="placeholder_label" value="{$label}" />
  
  <div class="edit-form-row">
    <div class="form-section-label">Limit to a file group?</div>
    {if empty($groups)}
      <span class="form-hint">No groups currently exist that exlusively contain files that accepted by this placeholder type (<a href="{$domain}assets/newAssetGroup?filter_type={$selected_type}">create one</a>)</span>
      <input type="hidden" name="placeholder_filegroup" value="NONE" />
    {else}
      <select name="placeholder_filegroup">
        <option value="NONE">Do not limit - Allow all files of the correct types</option>
        {foreach from=$groups item="group"}
        <option value="{$group.id}">{$group.label}</option>
        {/foreach}
      </select>
    {/if}
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
  </div>
</form>

</div>