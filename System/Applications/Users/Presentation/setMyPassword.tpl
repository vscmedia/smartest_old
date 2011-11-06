<div id="work-area">
  <h3>Change your password</h3>
  {if $request_parameters.password_change_required}
  <div class="warning">The system administrator has required that you change your password immediately. Please make the new password at least eight characters long.</div>
  {else}
  <div class="special-box">Use this form to change your password. Passwords must be at least eight characters long.</div>
  {/if}
  <form action="{$domain}users/updateMyPassword" method="post">
    <div class="edit-form-row">
      <div class="form-section-label">Enter it once</div>
      <input type="password" name="password_1" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Enter it again</div>
      <input type="password" name="password_2" />
    </div>
    <div class="buttons-bar">
      {if !$request_parameters.password_change_required}<input type="button" onclick="cancelForm();" value="Cancel" />{/if}
      <input type="submit" value="Update password" />
    </div>
  </form>
</div>