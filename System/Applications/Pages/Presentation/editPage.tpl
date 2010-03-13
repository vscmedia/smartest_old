<div id="work-area">

{if $allow_edit}
  
  {load_interface file="edit_tabs.tpl"}
  
  {if $require_item_select}
  <h3>Meta-Page Overview</h3>
  {load_interface file="choose_item.tpl"}
  {else}
  {load_interface file="editPage.form.tpl"}
  {/if}

{else}

<div class="instruction">You can't currently edit this page</div>

{/if}

</div>

{if !$require_item_select}
<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    
    <li class="permanent-action"><a href="{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $pageInfo.type == "ITEMCLASS"}&amp;item_id={$pageInfo.item.id}{/if}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>
    <li class="permanent-action"><a href="{$domain}desktop/editSite?site_id={$pageInfo.site_id}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit Site Parameters</a></li>
    {if $allow_release}<li class="permanent-action"><a href="{$domain}{$section}/releasePage?page_id={$pageInfo.webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" alt=""> Release this page</a></li>{/if}
    {if $allow_edit}<li class="permanent-action"><a href="{$domain}{$section}/closeCurrentPage" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>{/if}
  </ul>
</div>
{/if}