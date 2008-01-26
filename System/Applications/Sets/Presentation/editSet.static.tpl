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

<h3>{$model.plural_name} in this Data Set</h3>

<div class="instruction">Use the arrow buttons below to move {$model.plural_name|lower} in and out of this set.</div>

<form action="{$domain}sets/transferItem" method="post" name="transferForm">
  
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
  <input type="hidden" name="set_id" value="{$set.set_id}" />
  
  <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
    <tr>
      <td align="center">
      <div style="text-align:left">Available Items Not Used</div>

		<select name="available_items[]"  id="available_items" size="2" multiple style="width:270px; height:300px;"  onclick="setMode('add')"  >
    
    {foreach from=$items key="key" item="value"}
		<option value="{$values.item_id}" >{$value.item_name}</option>
		{/foreach}
		
		</select>
		
		</td>
     <td valign="middle" style="width:40px">
		<input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
    <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
     </td>
     <td align="center">
        <div style="text-align:left">Items Belonging to this set:</div>
 	<select name="used_items[]"  id='used_items' size="2" multiple style="width:270px; height:300px" onclick="setMode('remove')" >	
	{foreach from=$set_items key=key item=value}
	<option value="{$value.item_id}"  >{$value.item_name}</option>
	{/foreach}
        </select>
	</td>
   </tr>
</table>
</form>

</div>