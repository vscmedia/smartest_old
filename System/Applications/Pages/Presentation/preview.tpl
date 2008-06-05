<div id="work-area">

{load_interface file="edit_tabs.tpl"}

<h3>Preview of page: {$page.title}{if $item} (as {$item._model.name|lower} &quot;{$item.name}&quot;){/if}</h3>

{if $show_iframe}

<div style="margin-bottom:10px">
  <a href="{dud_link}" onclick="toggleMenuVisibility('preview-actions-menu');" class="js-menu-activator">Actions</a>
</div>

<div id="preview-actions-menu" class="js-menu" style="display:none">
  <ul></ul>
  <ul><li>{if $show_approve_button}<a href="{dud_link}" onclick="window.location='{$domain}{$section}/approvePageChanges?page_id={$page.webid}'">{else}<span>{/if}Approve Changes{if $show_approve_button}</a>{else}</span>{/if}</li><li>{if $show_publish_button}<a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishPageConfirm?page_id={$page.webid}'">{else}<span>{/if}Publish This Page{if $show_publish_button}</a>{else}</span>{/if}</li>{if $show_edit_item_option}{if $show_publish_item_option}<li><a href="{dud_link}" onclick="window.location='{$domain}datamanager/publishItem?item_id={$item.id}'">Publish This {$item._model.name}</a></li>{/if}<li><a href="{dud_link}" onclick="window.location='{$domain}datamanager/editItem?item_id={$item.id}&amp;page_id={$page.webid}&amp;from=pagePreview'">Edit This {$item._model.name}</a></li>{/if}<li><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releasePage?page_id={$page.webid}'">Release This Page</a></li></ul></div>

<div id="preview">
  <iframe src="{$site_domain}website/renderEditableDraftPage?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}"></iframe>
</div>

{elseif $show_item_list}

{load_interface file="choose_item.tpl"}

{/if}

</div>