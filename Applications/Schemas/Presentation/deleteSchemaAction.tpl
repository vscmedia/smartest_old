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

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}/"> Templates</a>  &gt; Delete Schema</h3>
<form id="pageViewForm" method="POST"  >
 <input type="hidden" name="schema_id" value="{$content.schema_id}"  >
</form>

<table>	
<tr><td>{if $count eq 0}
		Are you sure you want to delete this Schema?<br />
	<a href="{$domain}{$section}/">[Cancel]</a><a href="#" onclick="workWithItem('removeSchema');">[OK]</a><br /><br />
	{else}
	This Schema cannot be deleted!!!<br />
	This Schema is in used on the  following Models.<br />
	{foreach item="model" from=$models}
	{$model.itemclass_name}<br>
	{/foreach}

	<a href="{$domain}{$section}/" >[OK]</a>
	{/if}
    </td>
  </tr>
</table>

</div>