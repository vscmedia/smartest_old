<div id="work-area">
  <h3>Twitter settings</h3>
  
  {if empty($twitter_consumer_key) || empty($twitter_consumer_secret)}<div class="special-box">Enter OAuth settings for access to the Twitter API v1.1. To do this you will need to <a href="http://dev.twitter.com/apps/new" target="_blank">register this site as an App with Twitter</a> if you have not already done so.</div>{/if}
  
  <form action="{$domain}desktop/updateTwitterSettings" method="post">
  <div class="edit-form-layout">

  <div class="edit-form-row">
    <div class="form-section-label">Consumer Key</div>
    <input type="text" name="twitter_consumer_key" value="{$twitter_consumer_key}"/>
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">Consumer Secret</div>
    <input type="password" name="twitter_consumer_secret" value="{$twitter_consumer_secret}" />
  </div>
  
  {if $twitter_consumer_key && $twitter_consumer_secret && empty($twitter_access_token) && empty($twitter_access_token_secret)}<div class="special-box">To obtain valid access tokens for this app, <a href="{$token_request_url}">click here</a>.</div>{/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Access Token</div>
    <input type="text" name="twitter_access_token" value="{$twitter_access_token}"/>
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">Access Token Secret</div>
    <input type="password" name="twitter_access_token_secret" value="{$twitter_access_token_secret}" />
  </div>
  
  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();'" />
    <input type="submit" name="action" value="Save" />
  </div>
  
  </div>
  </form>
</div>

<div id="actions-area">
  
</div>