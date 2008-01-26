<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/getPageLists?page_id={$content.page_id};
}
{/literal}
</script>
{if $content.count eq 0}
Are you sure you want to publish?<br />
{elseif $content.count eq 1}
<h3>The particular list are not defined</h3>
	{foreach from=$undefinedLists item="undefinedLists"}
		{$undefinedLists}<br />
	{/foreach}
{elseif $content.count gt 1}
<h3>The particular lists are not defined</h3>
{foreach from=$undefinedLists item="undefinedLists"}
		{$undefinedLists}<br />
	{/foreach}
{/if}
<a href="#" onclick="javascript:cancelForm()">[Cancel]</a><a href="{$domain}{$section}/publishPageLists?page_id={$content.page_id}"  > [OK]  </a>
	
