<ul class="tabset">
    <li{if $method == "editPage"} class="current"{/if}><a href="{$domain}{$section}/editPage?page_id={$smarty.get.page_id}">Page Properties</a></li>
    <li{if $method == "pageAssets"} class="current"{/if}><a href="{$domain}{$section}/pageAssets?page_id={$smarty.get.page_id}">Element Structure</a></li>
    <li{if $method == "pageTags"} class="current"{/if}><a href="{$domain}{$section}/pageTags?page_id={$smarty.get.page_id}">Tags</a></li>
    <li{if $method == "preview"} class="current"{/if}><a href="{$domain}{$section}/preview?page_id={$smarty.get.page_id}">Preview</a></li>
</ul>

{* }[<a href="{$domain}{$section}/editPage?page_id={$smarty.get.page_id}">Overview</a>]
[<a href="{$domain}{$section}/pageAssets?page_id={$smarty.get.page_id}">Element Structure</a>]
[<a href="{$domain}{$section}/pageTags?page_id={$smarty.get.page_id}">Tags</a>]
{if $smarty.get.item_id}[<a href="{$domain}{$section}/pageTags?page_id={$smarty.get.page_id}">Item Dat</a>]{/if}
[<a href="{$domain}{$section}/preview?page_id={$smarty.get.page_id}">Preview</a>]  *}

{if ($content.pageInfo.page_type == "ITEMCLASS" || $content.page.page_type == "ITEMCLASS") && $smarty.get.item_id}[<a href="{$domain}{$section}/getPageLists?page_id={$smarty.get.page_id}">Item Data</a>]{/if}