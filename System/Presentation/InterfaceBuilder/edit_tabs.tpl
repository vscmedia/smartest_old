<ul class="tabset">
    {if $smarty.get.page_id}<li{if $method == "editPage"} class="current"{/if}><a href="{$domain}websitemanager/editPage?page_id={$smarty.get.page_id}{if $smarty.get.item_id}&amp;item_id={$smarty.get.item_id}{/if}">Page Overview</a></li>{/if}
    {if $smarty.get.item_id}<li{if $method == "editItem"} class="current"{/if}><a href="{$domain}datamanager/editItem?item_id={$smarty.get.item_id}{if $smarty.get.page_id}&amp;page_id={$smarty.get.page_id}{/if}">Item Properties</a></li>{/if}
    {if $smarty.get.page_id}<li{if $method == "pageAssets"} class="current"{/if}><a href="{$domain}websitemanager/pageAssets?page_id={$smarty.get.page_id}{if $smarty.get.item_id}&amp;item_id={$smarty.get.item_id}{/if}">Page Elements Tree</a></li>{/if}
    {if $smarty.get.page_id}<li{if $method == "preview"} class="current"{/if}><a href="{$domain}websitemanager/preview?page_id={$smarty.get.page_id}{if $smarty.get.item_id}&amp;item_id={$smarty.get.item_id}{/if}">Preview</a></li>{/if}
    <li{if $method == "pageTags" || $method == "itemTags"} class="current"{/if}><a href="{if $smarty.get.item_id}{$domain}datamanager/itemTags?item_id={$smarty.get.item_id}{if $smarty.get.page_id}&amp;page_id={$smarty.get.page_id}{/if}{else}{$domain}websitemanager/pageTags?page_id={$smarty.get.page_id}{/if}">Tags</a></li>
    {* <li{if $method == "relatedContent"} class="current"{/if}>{if $smarty.get.item_id}<a href="{$domain}datamanager/relatedContent?&amp;item_id={$smarty.get.item_id}{if $smarty.get.page_id}&amp;page_id={$smarty.get.page_id}{/if}">{else}<a href="{$domain}websitemanager/relatedContent?page_id={$smarty.get.page_id}">{/if}Related Content</a></li> *}
</ul>

<br clear="all" />

{* }[<a href="{$domain}{$section}/editPage?page_id={$smarty.get.page_id}">Overview</a>]
[<a href="{$domain}{$section}/pageAssets?page_id={$smarty.get.page_id}">Element Structure</a>]
[<a href="{$domain}{$section}/pageTags?page_id={$smarty.get.page_id}">Tags</a>]
{if $smarty.get.item_id}[<a href="{$domain}{$section}/pageTags?page_id={$smarty.get.page_id}">Item Dat</a>]{/if}
[<a href="{$domain}{$section}/preview?page_id={$smarty.get.page_id}">Preview</a>]  *}

{if ($content.pageInfo.page_type == "ITEMCLASS" || $content.page.page_type == "ITEMCLASS") && $smarty.get.item_id}[<a href="{$domain}{$section}/getPageLists?page_id={$smarty.get.page_id}">Item Data</a>]{/if}