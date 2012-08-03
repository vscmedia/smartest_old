<div id="work-area">
  
  <h3>Create a file group</h3>
  
  <form action="{$domain}{$section}/createAssetGroup" method="post" enctype="multipart/form-data">
  
    <div id="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Name this group</div>
        <input type="text" name="asset_group_label" value="Untitled file group" id="asset-group-label" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Type of group</div>
        <select name="asset_group_mode" id="file-group-mode-select">
          <option value="SM_SET_ASSETGROUP">Ordinary file group</option>
          <option value="SM_SET_ASSETGALLERY"{if $gallery_checked} selected="selected"{/if}>Gallery</option>
        </select><span class="form-hint" id="filegroup-type-hint">{if $gallery_checked}A file gallery allows you to order and caption certain files so they can be displayed together{else}An ordinary file group simply allows you to group a subset of your files together{/if}</span>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Which files can go in this group?</div>
        <div id="file-group-select-holder"{if $gallery_checked} style="display:none"{/if}>
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
        <div id="file-gallery-select-holder"{if !$gallery_checked} style="display:none"{/if}>
          <select name="asset_gallery_type">
            
            <option value="ALL">Any gallery-compatible file</option>
            
            <optgroup label="Placeholder types">
{foreach from=$gallery_placeholder_types item="type"}
              <option value="P:{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
            </optgroup>

            <optgroup label="Specific file types">
{foreach from=$gallery_asset_types item="type"}
              <option value="A:{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
            </optgroup>
            
            <optgroup label="Existing file groups">
{foreach from=$gallery_groups item="group"}
              <option value="G:{$type.id}">Files from '{$group.label}'</option>
{foreachelse}
              <option value="" disabled="disabled">No matching file groups</option>
{/foreach}
            </optgroup>
            
          </select>
        </div>
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

<script type="text/javascript">
{literal}
  $('file-group-mode-select').observe('change', function(e){
    if(this.value == "SM_SET_ASSETGALLERY"){
      $('file-gallery-select-holder').show();
      $('file-group-select-holder').hide();
      if($F('asset-group-label') == 'Untitled file group'){
        $('asset-group-label').value = 'Untitled gallery';
        $('filegroup-type-hint').update('A gallery allows you to arrange certain files in order to display them together');
      }
    }else{
      $('file-gallery-select-holder').hide();
      $('file-group-select-holder').show();
      if($F('asset-group-label') == 'Untitled gallery'){
        $('asset-group-label').value = 'Untitled file group';
        $('filegroup-type-hint').update('An ordinary file group simply allows you to group a subset of your files together');
      }
    }
  });
{/literal}
</script>

<div id="actions-area">

</div>