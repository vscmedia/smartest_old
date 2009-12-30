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
		formList = document.getElementById('used_items');
	}	
	
}

function executeTransfer(){
	document.transferForm.submit();
}

{/literal}
</script>

<div id="work-area">
  
  {load_interface file="edit_filegroup_tabs.tpl"}
  
  <h3>Files in group "{$group.label}"</h3>
  
  <form action="{$domain}{$section}/transferAssets" method="post" name="transferForm">

    <input type="hidden" id="transferAction" name="transferAction" value="" /> 
    <input type="hidden" name="group_id" value="{$group.id}" />

    <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
      <tr>
        <td align="center">
          <div style="text-align:left">Files that <strong>aren't</strong> in this group</div>

  		    <select name="available_assets[]"  id="available_assets" size="2" multiple style="width:270px; height:300px;"  onclick="setMode('add')"  >

{foreach from=$non_members key="key" item="asset"}
  		      <option value="{$asset.id}" >{$asset.url}</option>
{/foreach}

  		    </select>

  		  </td>
        
        <td valign="middle" style="width:40px">
  		    <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
          <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
        </td>
        
        <td align="center">
          <div style="text-align:left">Files that <strong>are</strong> in this group</div>
   	      
   	      <select name="used_assets[]"  id='used_assets' size="2" multiple style="width:270px; height:300px" onclick="setMode('remove')" >	
{foreach from=$members key="key" item="asset"}
  		      <option value="{$asset.id}" >{$asset.url}</option>
{/foreach}
          </select>
          
  	    </td>
      </tr>
    </table>
  </form>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Group options</b></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/browseAssetGroup?group_id={$group.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_magnify.png" border="0" alt="" style="width:16px;height:16px" /> Browse this group</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Repository options</b></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/assetGroups'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" style="width:16px;height:16px" /> View all file groups</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/assets'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" style="width:16px;height:16px" /> View all files by type</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/newAssetGroup'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_add.png" border="0" alt="" style="width:16px;height:16px" /> Create a new file group</a></li>
  </ul>
  
</div>