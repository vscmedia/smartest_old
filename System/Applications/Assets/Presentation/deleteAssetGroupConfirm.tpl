<div id="work-area">
  <h3>Delete file group?</h3>
  <p>Are you sure you want to delete this file group? This action cannot be undone.</p>
  <form action="{$domain}{$section}/deleteAssetGroup" method="post">
    <input type="hidden" name="group_id" value="{$group.id}" />
    <div class="buttons-bar">
      <input type="submit" value="Delete" />
      <input type="button" onclick="cancelForm();" value="Cancel" />
    </div>
  </form>
</div>