<script language="javascript">
{literal}
function submitForm(){
alert("inside submitform");
document.delForm.submit();
}
function cancelForm(){
window.location={$domain}{$section}/get{$content.template_code}Templates;
}
{/literal}
</script>

<h3>Delete Template</h3>
	{if $draftpage_count eq 0 && $livepage_count eq 0}
		Are you sure you want to delete this Template?<br />
	<a href="javascript:cancelForm();">[Cancel]</a><a href="{$domain}{$section}/deleteTemplate?template_code={$content.template_code}&template_name={$content.template_name}" >[OK]</a><br /><br />
	{elseif $draftpage_count neq 0 && $livepage_count eq 0}
	This Template is in use on the draft versions of the following pages.<br />
	{foreach item=drafttemplate from=$draft_templates}
		{$drafttemplate.page_title} ({$drafttemplate.page_name})<br />
	{/foreach}
	
	Are you sure you want to delete it?<br />
	<a href="#" onclick="javascript:cancelForm()">[Cancel]</a><a href="{$domain}{$section}/deleteTemplate?template_type={$content.template_code}&template_name={$content.template_name}"  > [OK]  </a><br />
	{elseif $draftpage_count eq 0 && $livepage_count neq 0}
	This Template is in use on the following live pages so cannot be deleted.<br />
	 {foreach item=livetemplate from=$live_templates}
		{$livetemplate.page_title} ({$livetemplate.page_name})<br />
	{/foreach}
	<a href="#" onclick="javascript:cancelForm()">[OK]  </a><br />
	{elseif $draftpage_count neq 0 && $livepage_count neq 0}
	This Template is in use on the live versions of the following pages.<br />
	{foreach item=livetemplate from=$live_templates}
		{$livetemplate.page_title} ({$livetemplate.page_name})<br />
		{/foreach}<br /><br />
	And also is in use on the draft versions of the following pages <br />
		{foreach item=drafttemplate from=$draft_templates}
		{$drafttemplate.page_title} ({$drafttemplate.page_name})<br />
		{/foreach}<br /><br />
			so cannot be deleted.<br />
	<a href="#" onclick="javascript:cancelForm()">[OK]</a>
	{/if}	