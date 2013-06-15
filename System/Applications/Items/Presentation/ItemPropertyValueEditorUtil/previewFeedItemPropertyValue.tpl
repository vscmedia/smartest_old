<div id="work-area">
  <h3>Data feed</h3>
  <div class="instruction">For values of feed properties</div>
  <div class="special-box">Feed URL: <code>{$feed.url}</code></div>
  
  {if $feed.has_error}
    <div class="warning">There is an external problem with this feed. The error message was: <strong>{$feed.error_message}</strong></div>
  {else}
  
  <ul>
  {foreach from=$feed.items item="item"}
    <li><a href="{$item.url}" target="_blank">{$item}</li>
  {/foreach}
  </ul>
  
  {/if}
  
</div>