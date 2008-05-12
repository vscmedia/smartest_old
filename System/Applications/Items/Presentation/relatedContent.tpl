<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Related Content</h3>
  
  <h4>Other {$model.plural_name}</h4>
  
  {if empty($related_items_this_model)}
    <i>No other {$model.plural_name|strtolower} are linked to this page.</i><br /><br />
  {else}
  <ul>
    {foreach from=$related_items_this_model item="related_item"}
    <li>{$related_item.name}</li>
    {/foreach}
  </ul>
  {/if}
  
  <a href="{$domain}{$section}/editRelatedContent?item_id={$item.id}&amp;model_id={$model.id}" class="arrow-right">Edit...</a><br /><br />
  
  <h4>Static Pages</h4>
  
  {if empty($related_pages)}
    <i>No web pages are linked to this page.</i><br /><br />
  {else}
  <ul>
    {foreach from=$related_pages item="related_page"}
    <li>{$related_page.title}</li>
    {/foreach}
  </ul>
  {/if}

  <a href="{$domain}{$section}/editRelatedContent?item_id={$item.id}" class="arrow-right">Edit...</a><br /><br />

  {foreach from=$models item="related_model"}
  
  <h4>{$related_model.plural_name}</h4>
  
  {if empty($related_model.related_items)}
    <i>No {$related_model.plural_name|strtolower} are linked to this page.</i><br /><br />
  {else}
    <ul>
      {foreach from=$related_model.related_items item="related_item"}
      <li>{$related_item.name}</li>
      {/foreach}
    </ul>
  {/if}
  
  <a href="{$domain}{$section}/editRelatedContent?item_id={$item.id}&amp;model_id={$related_model.id}" class="arrow-right">Edit...</a><br /><br />
    
  {/foreach}
  
</div>

<div id="actions-area">
  
</div>