<div id="work-area">

{load_interface file="edit_user_tabs.tpl"}

<h3 id="user">{if $user.id == $_user.id}Your profile pic{else}Profile pic for {$user.fullname}{/if}</h3>

<form action="{$domain}users/saveUserProfilePic">
  <input type="hidden" name="user_id" value="{$user.id}" />
  <div style="width:200px;padding:10px;float:left">
    {$user.profile_pic.image.width_200}
  </div>
  <div style="float:left">
    <div class="edit-form-row">Choose a picture
      <select name="profile_pic_asset_id">
{foreach from=$assets item="asset"}
        <option value="{$asset.id}"{if $asset.id == $user.profile_pic_asset_id} selected="selected"{/if}>{$asset.label}</option>
{/foreach}
      </select>
    </div>
  </div>
  <div class="breaker"></div>
  <div class="buttons-bar">
    <input type="submit" value="Save" />
  </div>
</form>

</div>