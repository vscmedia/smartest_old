<script language="javascript" type="text/javascript">

</script>

<div id="work-area">

<h3>OAuth Client Accounts</h3>

{if count($accounts)}
<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="account_id" id="item_id_input" value="" />
</form>

<ul class="options-list" id="options_list">
{foreach from=$accounts key=key item=user}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editAccount?account_id={$user.id}'">
    <a href="#" class="option" id="item_{$user.id}" onclick="setSelectedItem('{$user.id}'); return false;" >
      <img border="0" src="{$domain}Resources/Icons/user.png">
  {$user.display_name}</a></li>
{/foreach}
</ul>
{else}
<div class="special-box">There are no OAuth services added to Smartest yet. <a href="{$domain}smartest/oauth_account/add">Click here</a> to add one.</div>
{/if}

</div>

<div id="actions-area">

  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected OAuth Account</b></li>
    <li class="permanent-action"><a href="#" onclick="workWithItem('editAccount'); return false;" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit Account Details</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to delete this OAuth Account?')){workWithItem('deleteAccount');}{/literal} return false;"><img border="0" src="{$domain}Resources/Icons/delete.png"> Delete User</a></li>
  </ul>

  <ul class="actions-list">
     <li><b>Options</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/oauth_account/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/add.png"> Add a service</a></li>
  </ul>

</div>