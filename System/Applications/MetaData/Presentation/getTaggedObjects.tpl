<div id="work-area">

  <h3>Tagged Items: {$tag_name}</h3>
  
  {if empty($objects)}
  <div class="instruction">No items or pages are tagged with "{$tag_name}" on this site.</div>
  {else}
  <div class="instruction">{$objects|count} objects have ben tagged with "{$tag_name}".</div>
  <ul>
    {foreach from=$objects item="object"}
    <li>{$object.type}: <a href="{$object.url}">{$object.title}</a> (<a href="{if $object.type == 'Page'}{$domain}websitemanager/editPage?page_id={$object.webid}{else}{$domain}datamanager/editItem?item_id={$object.id}{/if}">edit</a>) </li>
    {/foreach}
  </ul>
  {/if}

</div>

<div id="actions-area">
  <ul id="non-specific-actions" class="actions-list">
    <li><strong>Options</strong></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/tags'"><img src="{$domain}Resources/Icons/tick.png" alt="tick">Go back to tags</a></li>
  </ul>
</div>