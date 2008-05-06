<div id="work-area">

{if $allow_edit}

{load_interface file="edit_tabs.tpl"}

{load_interface file="editPage.form.tpl"}

{else}

<h3>Edit Page</h3>

<div class="instruction">You can't currently edit this page</div>

{/if}

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="{$domain}desktop/editSite?site_id={$pageInfo.site_id}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit Site Parameters</a></li>
    <li class="permanent-action"><a href="{$domain}websitemanager/preview?page_id={$pageInfo.webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_red.png" border="0" alt=""> Preview this page</a></li>
    {if $allow_release}<li class="permanent-action"><a href="{$domain}{$section}/releasePage?page_id={$pageInfo.webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" alt=""> Release this page</a></li>{/if}
    {if $allow_edit}<li class="permanent-action"><a href="{$domain}{$section}/closeCurrentPage" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>{/if}
  </ul>
</div>
