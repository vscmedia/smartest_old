<script language="javascript">
{literal}

function setMode(mode){

	document.getElementById('transferAction').value=mode;

	if(mode == "add"){
		document.getElementById('add_button').disabled=false;
		document.getElementById('remove_button').disabled=true;
		formList = document.getElementById('available_items');
	}else if(mode == "remove"){
		document.getElementById('add_button').disabled=true;
		document.getElementById('remove_button').disabled=false;
		formList = document.getElementById('used_items');
	}
	
	
	document.getElementById('item_id').value=formList.value;
	// alert(document.getElementById('item_id').value);
	
}

function executeTransfer(){
	document.transferForm.submit();
}

{/literal}
</script>

<h3>Members of Data Set</h3>

<form action="{$domain}sets/transferItem" method="post" name="transferForm">
  
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
  <input type="hidden" id="item_id" name="item_id" value="" />
  <input type="hidden" id="set_id" name="set_id" value="1" />
  
  <table width="560" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
    <tr>
      <td width="230">
      Available Items Not Used

<select name="available_items" size="12" style="width:230px" id="available_forms" onchange="setMode('add');">
<option value="1" onclick="setMode('add');" style="height:15px">Basic Application</option>
</select></td>

      <td valign="middle" style="text-align:center">
        <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer()" /><br /><br />
        <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer()" />
      </td>
      <td width="230">
        Items Belonging to this set:
<select name="used_items" size="12" style="width:230px" id="used_forms" onchange="setMode('remove');">
<option value="2" onclick="setMode('remove');" style="height:15px">Self Assessment</option>
<option value="3" onclick="setMode('remove');" style="height:15px">Employment History</option>
</select></td></tr>

</table>

</form>