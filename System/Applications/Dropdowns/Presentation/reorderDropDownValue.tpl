<script language="javascript" type="text/javascript">
{literal}

	function fn_deleteFromDestList(destListName) {
		var destList  = document.pageViewForm.elements[destListName];
		var len = destList.options.length;
		for(var i = (len); i >= 0; i--) {
		if ((destList.options[i] != null) && (destList.options[i].selected == true)) {
		destList.options[i] = null;
			}
		}
	}	
	
	function fn_addSrcToDestList(srcbox,destbox){

		destList = document.getElementById(destbox);
		srcList = document.pageViewForm.elements[srcbox];
		var len = destList.length;
		var srcLen = srcList.length;
		
		
		    for(var i = 0; i < srcLen; i++) 
		    {
			    if ((srcList.options[i] != null) && (srcList.options[i].selected)) 
			    {
				    var found = false;
				    for(var count = 0; count < len; count++) 
				    {
					    if (destList.options[count] != null) 
					    {
						    if (srcList.options[i].value == destList.options[count].value) 
						    {
							    found = true;
							    break;
						    }
					    }
				    }
				    if (found != true) 
				    {
					    destList.options[len] = new Option(srcList.options[i].text); 
					    destList.options[len].name = srcList.options[i].text;
					    destList.options[len].value = srcList.options[i].value;
					    destList.options[len].selected = true;
					    len++;
				    }
			    }
		    }
    		
		
		
	}
	
	function check(){
		
		var editForm = document.getElementById('pageViewForm');	
		var len = editForm.cmbRole1.options.length;
		var len2 = editForm.cmbRole.options.length;
		
		if(len==len2){
			return true;
		}else{
			alert('please specify the order for all options');
			return false;
		}
	}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">DropDowns</a> &gt; Re-Order Drop Down Values</h3>
<a name="top"></a>
<form id="pageViewForm" name="pageViewForm" method="post" action="{$domain}{$section}/updateDropDownOrder" onsubmit="return check();">
<input type="hidden" name="drop_down_id" value="{$dropdown_details.dropdown_id}" />

<table border="0" cellspacing="0" cellpadding="0" style="width:550px">
  <tr>
    <td valign="top" >
		<select name="cmbRole1" size="2" multiple style="width: 170px;height : 100px">
                {foreach from=$dropdown_values key=key item=values}
		<option value="{$values.dropdownvalue_id}" >{$values.dropdownvalue_label}</option>
		{/foreach}
		</select>
    </td>
    <td valign="top" >
		<input type="button" value="&gt;&gt;" id="add_button"  onclick="fn_addSrcToDestList('cmbRole1','cmbRole');" /><br /><br />
        	<input type="button" value="&lt;&lt;" id="remove_button"  onclick="fn_deleteFromDestList('cmbRole');" />
    </td>	
    <td valign="top">
               <select name="cmbRole[]"  id='cmbRole' size="4" multiple style="width: 170px;height : 100px">	
	       
               </select>
     </td>
  </tr>
</table>

<div class="buttons-bar"><input type="button" onclick="cancelForm();" value="Cancel"><input type="submit" value="Save"></div>

</form>

</div>



