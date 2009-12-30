<div id="work-area">
  
  {load_interface file="edit_set_tabs.tpl"}
  
  <h3><a href="{$domain}smartest/models">Items</a> &gt; {if $model.id}<a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; <a href="{$domain}sets/getItemClassSets?class_id={$model.id}">Sets</a>{else}<a href="{$domain}smartest/sets">Sets</a>{/if} &gt; <a href="{$domain}sets/editSet?set_id={$set.id}">{$set.label}</a> &gt; Set Order</h3>
  
  {if count($items)}
  
  <ul class="re-orderable-list">
    {foreach from=$items item="item" key="key"}
    <li>{$item.name}
      <div class="buttons">{if $key > 0}<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=up"><img src="{$domain}Resources/Icons/arrow_up.png" alt="up" /></a>{/if}
      {if $key < count($items)-1}<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=down"><img src="{$domain}Resources/Icons/arrow_down.png" alt="down" /></a>{/if}</div>
    </li>
    {/foreach}
  </ul>
  
  <div class="edit-form-row">
    <div class="buttons-bar"><input type="button" value="Done" onclick="cancelForm();" /></div>
  </div>
  
  {else}
  <div class="warning">There are currently no items in this set. <a href="{$domain}{$section}/editSet?set_id={$set.id}">Click here</a> to add some.</div>
  {/if}
  
</div>

<div id="actions-area">
    
</div>