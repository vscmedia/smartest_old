<div id="work-area">
    {foreach from=$items item="rss_item"}
    <li><a href="{$rss_item.url}">{$rss_item.title.titlecase_strict}</a></li>
    {/foreach}
</div>