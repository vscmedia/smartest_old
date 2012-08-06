<div id="work-area">
  <h3>Resize image</h3>
  <form action="{$domain}assets/resizeImageAssetAction" method="post">
    
    <input type="hidden" name="asset_id" value="{$asset.id}" />
    
    <div class="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Original file</div>
        <div id="plane" style="background-color:#ccc;{if $asset.image.is_square}width:240px;height:240px{elseif $asset.image.is_portrait}width:180px;height:240px{else}width:240px;height:180px{/if}"></div>
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Original file</div>
        <input type="checkbox" name="create_copy" value="1" id="create-copy" /><label for="create-copy">Create a copy of the original file</label>
      </div>
    
    </div>
    
  </form>
</div>