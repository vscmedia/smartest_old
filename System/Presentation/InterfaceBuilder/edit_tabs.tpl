<ul class="tabset">
    {if $smarty.get.item_id}
    {load_interface file="item_edit_tabs.tpl"}
    {else}
    {load_interface file="page_edit_tabs.tpl"}
    {/if}
</ul>