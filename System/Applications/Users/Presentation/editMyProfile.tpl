<div id="work-area">
  <h3>Your profile</h3>
  <form action="{$domain}users/updateMyProfile" method="post">
    <div class="edit-form-row">
      <div class="form-section-label">Username</div>
      {if $allow_username_change}<input type="text" name="username" value="{$user.username}" />{else}{$user.username}{/if}
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Password</div>
      <a href="{$domain}smartest/account/password">Click here to change your password</a>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">First name</div>
      <input type="text" name="user_firstname" value="{$user.firstname}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Last name</div>
      <input type="text" name="user_lastname" value="{$user.lastname}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Email address (for notifications)</div>
      <input type="text" name="user_email" value="{$user.email}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Twitter username</div>
      @<input type="text" name="user_twitter" value="{$twitter_handle}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">User interface language (where possible)</div>
      <select name="user_language">
{foreach from=$_languages item="lang" key="langcode"}
        {if $langcode != "zxx" && $langcode != "mul"}<option value="{$langcode}"{if $user.ui_language == $langcode} selected="selected"{/if}>{$lang.label}</option>{/if}
{/foreach}        
      </select>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Website</div>
      <input type="text" name="user_website" value="{$user.website}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Bio</div>
      <textarea name="user_bio" style="width:400px;height:90px">{$user.bio}</textarea>
    </div>
    <div class="buttons-bar">
      <input type="button" onclick="cancelForm();" value="Cancel" />
      <input type="submit" value="Save" />
    </div>
  </form>
  <div class="breaker"></div>
</div>

<div id="actions-area">
  <ul class="actions-list">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}smartest/account/password'; return false;" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/lock.png" /> Change your password</a></li>
  </ul>
</div>