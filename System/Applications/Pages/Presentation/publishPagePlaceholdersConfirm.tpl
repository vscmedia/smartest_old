<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/getPageAssets?page_id={$content.page_id};
}
{/literal}
</script>

<h3>The particular placeholders are not defined</h3>
	{foreach from=$undefinedPlaceholderClasses item=undefinedPlaceholderClasses}
		{$undefinedPlaceholderClasses}<br />
	{/foreach}
<a href="#" onclick="javascript:cancelForm()">[Cancel]</a><a href="{$domain}{$section}/publishPagePlaceholders?page_id={$content.page_id}"  > [OK]  </a>
	