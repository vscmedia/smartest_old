<div id="work-area">

{load_interface file="editPage.tabs.tpl"}

<h3>Preview of page: {$page.title}{if $item} (as {$item._model.name|lower} &quot;{$item.name}&quot;){/if}</h3>

{if $show_iframe}

<div style="margin-bottom:10px">
  {if $item}<input type="button" onclick="window.location='{$domain}datamanager/editItem?item_id={$item.id}&amp;from=pagePreview'" value="Edit {$item._model.name}" />{/if}
  <input type="button" onclick="window.location='{$domain}{$section}/approvePageChanges?page_id={$page.webid}'" value="Approve Changes"{if !$show_approve_button} disabled="disabled"{/if}/>
  <input type="button" onclick="window.location='{$domain}{$section}/publishPage?page_id={$page.webid}'" value="Publish This Page" {if !$show_publish_button} disabled="disabled"{/if}/>
</div>

<div id="preview">
  <iframe src="{$site_domain}website/renderEditableDraftPage?page_id={$page.webid}{if $item}&amp;item_id={$item.webid}{/if}"></iframe>
</div>

{elseif $show_item_list}

<h4>To preview this page, please choose a specific {$model.name}:</h4>

{* <ul class="basic-list">
{foreach from=$items item="page_item"}
  <li><a href="{$domain}{$section}/preview?page_id={$page.webid}&amp;item_id={$page_item.id}">{$model.name}: {$page_item.name}</a></li>
{/foreach}
</ul> *}

<form action="{$domain}{$section}/preview" method="get" id="item_chooser">
  <input type="hidden" name="page_id" value="{$page.webid}" />
  <select name="item_id" style="width:300px" onchange="$('item_chooser').submit();" />
    {foreach from=$items item="page_item"}
    <option value="{$page_item.id}">{$page_item.name}</option>
    {/foreach}
  </select>
  <input type="submit" value="Go" />
</form>

{/if}

</div>