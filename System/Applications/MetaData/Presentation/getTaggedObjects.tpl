<div id="work-area">

  <h3>Tagged Items: {$tag_name}</h3>
  
  {if empty($objects)}
  <div class="instruction">No items or pages are tagged with "{$tag_name}" on this site.</div>
  {else}
  <div class="instruction">{$objects|count} objects have ben tagged with "{$tag_name}".</div>
  <ul>
    {foreach from=$objects item="object"}
    <li style="list-style-image:url('{$object.small_icon}')"> <a href="{$object.action_url}">{$object.title}</a></li>
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