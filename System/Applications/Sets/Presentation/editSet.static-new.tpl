<script language="javascript" src="{$domain}Resources/Javascript/System/yui/build/yahoo/yahoo.js"></script>
<script language="javascript" src="{$domain}Resources/Javascript/System/yui/build/dom/dom.js"></script>
<script language="javascript" src="{$domain}Resources/Javascript/System/yui/build/event/event.js"></script>
<script language="javascript" src="{$domain}Resources/Javascript/System/yui/build/dragdrop/dragdrop.js"></script>

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

function sm_startDragDrop(item_id){
	var DD = new YAHOO.util.DD(item_id);
}

{/literal}
</script>

<h3>Members of Data Set</h3>

<form action="{$domain}sets/transferItem" method="post" name="transferForm">
  
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
  <input type="hidden" id="item_id" name="item_id" value="" />
  <input type="hidden" id="set_id" name="set_id" value="1" />
  
  <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #ccc">
    <tr>
      <td width="50%">
      Available Items Not Used

		<ul id="available_items" style="width:90%;height:250px;border:1px solid #ccc;overflow:scroll">
        <li style="cursor:move" onmousedown="sm_startDragDrop('item_1')" id="item_1">item 1</li>
        <li style="cursor:move" onmousedown="sm_startDragDrop('item_2')" id="item_2">item 2</li>
        </ul></td>

      {*<td valign="middle" style="text-align:center">
        <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer()" /><br /><br />
        <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer()" />
      </td>*}
      <td width="50%">
        Items Belonging to this set:
        <ul id="used_items" style="width:90%;height:250px;border:1px solid #ccc;overflow:scroll">
        <li style="cursor:move" onmousedown="sm_startDragDrop('item_3')" id="item_3">item 3</li>
        <li style="cursor:move" onmousedown="sm_startDragDrop('item_4')" id="item_4">item 4</li>
        </ul>
</td></tr>

</table>

</form>