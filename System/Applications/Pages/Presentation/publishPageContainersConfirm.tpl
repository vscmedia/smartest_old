<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/getPageAssets?page_id={$content.page_id};
}
{/literal}
</script>

<h3>The particular containers are not defined</h3>
	{foreach from=$undefinedContainerClasses item=undefinedContainerClasses}
		{$undefinedContainerClasses}<br />
	{/foreach}
<a href="#" onclick="javascript:cancelForm()">[Cancel]</a><a href="{$domain}{$section}/publishPageContainers?page_id={$content.page_id}"  > [OK]  </a>
	