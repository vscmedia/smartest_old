<div id="work-area">
  <h3>Edit gallery membership</h3>
  <form action="{$domain}assets/updateAssetGalleryMembership" method="post">
    <input type="hidden" name="membership_id" value="{$membership.id}" />
    <input type="hidden" name="group_id" value="{$gallery.id}" />
    <div id="edit-form-layout">
      <div class="edit-form-row">
        <div class="form-section-label">Gallery</div>
        {$gallery.label}
      </div>
      <div class="edit-form-row">
        <div class="form-section-label">File</div>
        {$membership.asset.label}
      </div>
      <div class="edit-form-row">
        <div class="form-section-label">Caption</div>
        <textarea name="membership_caption" style="width:400px;height:50px">{$membership.caption}</textarea>
      </div>
      <div class="buttons-bar">
        <input type="submit" value="Save" />
      </div>
    </div>
  </form>
</div>