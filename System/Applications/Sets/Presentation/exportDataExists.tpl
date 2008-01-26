<script language="javascript">
{literal}
function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');

	if(editForm){
		
{/literal}		editForm.action="/{$section}/"+pageAction;{literal}
		
		editForm.submit();
	}
}
{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">Sets</a> &gt;{$set.set_name}  &gt; Export Data Already Exists</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="schema_name"  value="{$content.schema_name}" />
  <input type="hidden" name="model_name"  value="{$content.class_name}" />
  <input type="hidden" name="set_name"  value="{$set.set_name}" />
</form>

<table border="0" cellspacing="0" cellpadding="0" style="width:850px">
  <tr>
    <td valign="top" style="width:550px"><font color="Red">
you have already paired a data set from this model with this  schema!!! </font><br>would you like to use the same saved configuration<br>
<a href="{$domain}{$section}/">[Cancel]</a>
<a href="#" onclick="workWithItem('../XmlExport/exportData');">[OK]</a> <br><br>OR <br> <br>create a new configuration?.<br>
<a href="{$domain}{$section}/">[Cancel]</a>
<a href="{$domain}{$section}/chooseSchemaForExport?set_id={$set.set_id}">[OK]</a>			
		</td>
</td>		
		</tr>

</table>

</div>
