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

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">Sets</a> &gt;{$set.set_name}  &gt; Export Data Options</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="schema_id"  value="{$content.schema_id}" />
  <input type="hidden" name="set_id"  value="{$set.set_id}" />
</form>

<ol>
<li>create a new configuration?</li>
<a href="#" onclick="workWithItem('exportData');">[OK]</a> <a href="{$domain}{$section}/">[Cancel]</a>
{if $content.count==1 }
<li>continue with existing settings?</li>
<a href="{$domain}{$section}/exportSuccess?set_id={$set.set_id}&schema_name={$content.schema_name}&dataexport_name={$content.name}" >[OK]</a> <a href="{$domain}{$section}/">[Cancel]</a>
<li>edit the existing pairing configration</li>
<a href="#" onclick="workWithItem('editExportData');">[OK]</a> <a href="{$domain}{$section}/">[Cancel]</a>
{elseif $content.count>1}
<li>select another existing pairing</li>
<a href="#" onclick="workWithItem('choosePairingForExport');">[OK]</a> <a href="{$domain}{$section}/">[Cancel]</a>
{/if}
</ol>

</div>



