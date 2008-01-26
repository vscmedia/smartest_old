<script language="javascript">
{literal}

function validate(){			
	if (document.renameSchemaForm.schema_name.value == "") {
    		alert( "Text field cannot be left blank" );
    		document.renameSchemaForm.schema_name.focus();
    		return false ;
		}	
	else return true;
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}/"> Templates</a>  &gt; Rename Schema</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px"></div>

<form name="renameSchemaForm" action="{$domain}{$section}/renameSchemaAction" method="POST" > 
<table>
	<input type="hidden" name="schema_id" value="{$content.id}"  >
	<tr>
		<td>
		Schema Name
		</td>
		<td>
		<input type="text" name="schema_name" value="{$content.name}"  >
		</td>	
	</tr>
	
	<tr>
	<td></td>
	<td>
	<input type="submit" value="Rename" name="submit" onclick="return validate();">
	</td>
	</tr>
</table>

</form>

</div>