<div id="work-area">
  <h3>Add an OAuth Client Account</h3>
  <form action="{$domain}oauth/insertAccount" method="post">
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <input type="text" name="oauth_service_label" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Service</div>
      <select name="oauth_service">
{foreach from=$services item="service"}
        <option value="{$service.id}">{$service.label} (OAuth {$service.oauth_version})</option>
{/foreach}
      </select>
    </div>
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();">
      <input type="submit" value="Save" />
      <input type="submit" value="Save &amp; Request Token" />
    </div>
  </form>
</div>

<div id="actions-area">
  
</div>