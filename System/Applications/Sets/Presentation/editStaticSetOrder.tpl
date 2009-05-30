<div id="work-area">
  
  <h3><a href="{$domain}smartest/data">Items</a> &gt; <a href="{$domain}smartest/sets">Sets</a> &gt; <a href="{$domain}sets/editSet?set_id={$set.id}">{$set.label}</a> &gt; Set Order</h3>
  
  <ul>
    {foreach from=$items item="item" key="key"}
    <li>{$item.name} (<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=up">up</a>) (<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=down">down</a>)</li>
    {/foreach}
  </ul>
  
</div>

<div id="actions-area">
    
</div>