<div id="work-area">

  <h3>Tagged Items: {$tag.label}</h3>
  
  {if !$items._empty}
  <h4>Items</h4>
  <ul>
    {foreach from=$items item="item"}
    <li style="list-style-image:url('{$item.small_icon}')"> <a href="{$item.action_url}">{$item.name}</a><span> - {$item.model.name}</span></li>
    {/foreach}
  </ul>
  {/if}
  
  {if !$assets._empty}
  <h4>Files</h4>
  <ul>
    {foreach from=$assets item="asset"}
    <li style="list-style-image:url('{$asset.small_icon}')"> <a href="{$asset.action_url}">{$asset.label}</a><span> - {$asset.type_info.label}</span></li>
    {/foreach}
  </ul>
  {/if}
  
  {if !$pages._empty}
  <h4>Pages</h4>
  <ul>
    {foreach from=$pages item="page"}
    <li style="list-style-image:url('{$page.small_icon}')"> <a href="{$page.action_url}">{$page.title}</a></li>
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