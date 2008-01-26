<ul class="tabset">
    <li{if $method == "editItem"} class="current"{/if}><a href="{$domain}{$section}/editItem?item_id={$smarty.get.item_id}">Item Properties</a></li>
    <li{if $method == "itemTags"} class="current"{/if}><a href="{$domain}{$section}/itemTags?item_id={$smarty.get.item_id}">Tags</a></li>
</ul>

{* [<a href="{$domain}{$section}/editItem?item_id={$smarty.get.item_id}">Overview</a>]
[<a href="{$domain}{$section}/getPageAssets?page_id={$smarty.get.page_id}">Element Structure</a>]
[<a href="{$domain}{$section}/preview?page_id={$smarty.get.page_id}">Preview</a>]
{if ($content.pageInfo.page_type == "ITEMCLASS" || $content.page.page_type == "ITEMCLASS") && $smarty.get.item_id}[<a href="{$domain}{$section}/getPageLists?page_id={$smarty.get.page_id}">Item Data</a>]{/if} *}