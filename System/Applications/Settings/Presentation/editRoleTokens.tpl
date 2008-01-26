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

<h3><a href="{$domain}{$section}">Settings</a> &gt; <a href="{$domain}{$section}/users">Users</a> &gt; Edit Role Tokens</h3>

<div class="instruction">Modifying Role: <b>{$role.label}</b></div>

<form action="{$domain}{$section}/transferTokensToRole" method="post" name="transferForm">
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
<input type="hidden" name="role_id" value="{$role.id}" />

<table border="0" cellspacing="0" cellpadding="0" style="width:550px">
  <tr>
    
    <td valign="top">
      Permissions not granted:<br />
      <select name="tokens[]" size="2" multiple style="width:300px;height:200px;" onclick="setMode('add')">
        {foreach from=$tokens key="key" item="values"}
        <option value="{$values.token_id}" >{$values.token_description}</option>
        {/foreach}
      </select>
    </td>
    
    <td valign="middle">
      <input type="button" value="&gt;&gt;" id="add_button"  disabled="disabled" onclick="executeTransfer();" /><br /><br />
      <input type="button" value="&lt;&lt;" id="remove_button"   disabled="disabled" onclick="executeTransfer();" />
    </td>
    
    <td valign="top">
      Permissions granted:<br />
      <select name="sel_tokens[]"  id='sel_roles' size="4" multiple style="width:300px;height:200px" onclick="setMode('remove')" >	
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
     <li class="permanent-action"><b>Users &amp; Permissions</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addUser'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add User</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addRole'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add Role</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/listUsers'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> List Users</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/listRoles'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> List Roles</a></li>
  </ul>
</div>