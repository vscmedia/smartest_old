<script language="javascript" type="text/javascript">

{literal}
  function check(){
    var editForm = document.getElementById('pageViewForm');
    if(editForm.drop_down.value==''){
      alert ('please enter the dropdown Label');
      editForm.drop_down.focus();
      return false;
    }else{
      return true;
    }
  }
  
{/literal}

</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">DropDowns</a> &gt; Add Drop Down</h3>
<a name="top"></a>

<div class="instruction">Your data is collected into functionally distinct types called Drop Downs. Please choose one to continue.</div>

<table border="0" cellspacing="0" cellpadding="0" style="width:850px">
  <tr>
    <td valign="top" style="width:550px">
			<table  border="0" style="width:550px">
<form id="pageViewForm" method="post" action="{$domain}{$section}/insertDropDown"  onsubmit="return check();">
<!--<input type="hidden" name="set_id" value="{$content.newset}" />-->
				<tr>
					<td width="250">Dropdown Label: <input type="text" name="dropdown_label" id="drop_down" value=""></td>				
				
					<td width="250" ><input type="submit" value="Add"></td>	
									
				</tr>		
				
</form>
			</table>

		</td>
		<td valign="top" style="width:250px">		
		</td>		
		</tr>
</table>

</div>