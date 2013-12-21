<div id="work-area">
  <h3>Edit OAuth Client Account</h3>
  <form action="{$domain}oauth/updateAccount" method="post">
    
    <input type="hidden" name="oauth_account_id" value="{$account.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <input type="text" value="{$account.label}" name="oauth_service_label" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Service</div>
      {$account.service.label} (OAuth {$account.service.oauth_version})
    </div>
    
    {if empty($account.oauth_consumer_token) || empty($account.oauth_consumer_secret)}
    <div class="special-box">To connect Smartest to {$account.service.label}, you will need to <a href="{$account.service.client_register_url}" target="_blank">register this site</a> as an "App" or "Client" if you have not already done so.</div>
    {/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">{if $account.service.oauth_version == "1.0"}Consumer Token{else}Client ID{/if}</div>
      <input type="text" name="oauth_consumer_token" value="{$account.oauth_consumer_token}"/>
    </div>

    <div class="edit-form-row">
      <div class="form-section-label">{if $account.service.oauth_version == "1.0"}Consumer Secret{else}Client Secret{/if}</div>
      <input type="password" name="oauth_consumer_secret" value="{$account.oauth_consumer_secret}" />
    </div>

    {if $account.oauth_consumer_token && $account.oauth_consumer_secret && empty($account.oauth_access_token) && empty($account.oauth_access_token_secret)}<div class="special-box">To obtain valid access tokens for this account, <a href="{$domain}oauth/prepareAccessTokenRequestProcess?account_id={$account.id}">click here</a>.</div>{/if}

    <div class="edit-form-row">
      <div class="form-section-label">Access Token</div>
      <input type="text" name="oauth_access_token" value="{$account.oauth_access_token}"/>
    </div>

    <div class="edit-form-row">
      <div class="form-section-label">Access Token Secret</div>
      <input type="password" name="oauth_access_token_secret" value="{$account.oauth_access_token_secret}" />
    </div>
    
    <div class="buttons-bar">
      <input type="submit" value="Save changes" />
    </div>
    
  </form>
</div>

<div id="actions-area">
  
</div>