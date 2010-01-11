<div id="work-area">
  
  <h3>Create a file group</h3>
  
  <form action="{$domain}{$section}/createAssetGroup" method="post" enctype="multipart/form-data">
  
    <div id="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Name this group</div>
        <input type="text" name="asset_group_label" value="Untitled file group" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Which files can go in this group?</div>
        <select name="asset_group_type">
          <option value="ALL">Any type of file</option>

          <optgroup label="Placeholder types">
{foreach from=$placeholder_types item="type"}
            <option value="P:{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
          </optgroup>

          <optgroup label="Specific file types">
{foreach from=$asset_types item="type"}
            <option value="A:{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
          </optgroup>

        </select>
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