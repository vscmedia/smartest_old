<script language="javascript">
{literal}

function setMode(mode){

	document.getElementById('transferAction').value=mode;

	if(mode == "add"){
		document.getElementById('add_button').disabled=false;
		document.getElementById('remove_button').disabled=true;
		
	}else if(mode == "remove"){
		document.getElementById('add_button').disabled=true;
		document.getElementById('remove_button').disabled=false;		
	}	
	
}

function executeTransfer(){
	document.transferForm.submit();
}

{/literal}
</script>

<div id="work-area">
    
{load_interface file="edit_user_tabs.tpl"}

<h3>Permission Tokens</h3>

<form action="{$domain}{$section}/transferTokens" method="post" name="transferForm">
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
<input type="hidden" name="user_id" value="{$user.id}" />

<table border="0" cellspacing="0" cellpadding="0" style="width:550px">
  
  <tr>
    <td colspan="3">
      <div class="special-box">
        Modifying permission tokens of user <strong>{$user.full_name} ({$user.username})</strong>
        <br /> for website:&nbsp;
        <select name="site_id" onchange="window.location='{$domain}{$section}/editUserTokens?user_id={$user.id}&amp;site_id='+this.value;">
          {if $allow_global}<option value="GLOBAL">All sites (global)</option>{/if}
          {foreach from=$sites item="site"}
          <option value="{$site.id}"{if $site.id==$site_id} selected="selected"{/if}>{$site.internal_label}</option>
          {/foreach}
        </select>
      </div>
      {if $site_id== 'GLOBAL' && $allow_global}<div class="warning">Note: Granting a token globally will remove any instances of that token being granted on individual sites.</div>{/if}
    </td>
  </tr>
  
  <tr>
    
    <td valign="top">
      Tokens not granted:<br />
      <select name="tokens[]" size="2" multiple style="width:350px;height:200px;" onclick="setMode('add')">
        {foreach from=$tokens key="key" item="values"}
        <option value="{$values.token_id}" >{$values.token_description}</option>
        {/foreach}
      </select>
    </td>
    
    <td valign="middle">
      <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
      <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
    </td>
    
    <td valign="top">
      Tokens granted:<br />
      <select name="sel_tokens[]"  id='sel_roles' size="4" multiple style="width:350px;height:200px" onclick="setMode('remove')" >	
        {foreach from=$utokens key="key" item="value"}
        <option value="{$value.token_id}"  >{$value.token_description}</option>
        {/foreach}
      </select>
    </td>
    
  </tr>
</table>

</form>

</div>

<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add User</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/user_roles/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add Role</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> List Users</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/user_roles'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> List Roles</a></li>
  </ul>
</div>