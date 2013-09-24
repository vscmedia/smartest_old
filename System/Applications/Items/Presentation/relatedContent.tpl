<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Related Content</h3>
  
  <h4>Other {$model.plural_name}</h4>
  
  <div id="related-items-{$model.id}-list-container">
  {if empty($related_items_this_model)}
    <i>No other {$model.plural_name|strtolower} are linked to this {$model.name|strtolower}.</i><br /><br />
  {else}
  <ul>
    {foreach from=$related_items_this_model item="related_item"}
    <li>{$related_item.name}</li>
    {/foreach}
  </ul>
  {/if}
  </div>
  
  <a href="{$domain}{$section}/editRelatedContent?item_id={$item.id}&amp;model_id={$model.id}" class="arrow-right" id="edit-related-same-model-link">Edit...</a><br /><br />
  
  <script type="text/javascript">

    $('edit-related-same-model-link').observe('click', function(e){ldelim}
        MODALS.load('{$section}/editRelatedContent?item_id={$item.id}&model_id={$model.id}', 'Related {$model.plural_name|strtolower}');
        e.stop();
    {rdelim});

  </script>
  
  <h4>Static Pages</h4>
  
  <div id="related-pages-list-container">
  {if empty($related_pages)}
    <i>No web pages are linked to this {$model.name|strtolower}.</i><br /><br />
  {else}
  <ul>
    {foreach from=$related_pages item="related_page"}
    <li>{$related_page.title}</li>
    {/foreach}
  </ul>
  {/if}
  </div>

  <a href="{$domain}{$section}/editRelatedContent?item_id={$item.id}" class="arrow-right" id="edit-related-pages-link">Edit...</a><br /><br />
  
  <script type="text/javascript">

      $('edit-related-pages-link').observe('click', function(e){ldelim}
          MODALS.load('{$section}/editRelatedContent?item_id={$item.id}', 'Related pages');
          e.stop();
      {rdelim});

    </script>

  {foreach from=$models item="related_model" key="key"}
  
  <h4>{$related_model.plural_name}</h4>
  
  <div id="related-items-{$related_model.id}-list-container">
  {if empty($related_foreign_items[$key])}
    <i>No {$related_model.plural_name|strtolower} are linked to this {$model.name|strtolower}.</i><br /><br />
  {else}
    <ul>
      {foreach from=$related_foreign_items[$key] item="related_item"}
      <li>{$related_item.name}</li>
      {/foreach}
    </ul>
  {/if}
  </div>
  
  <a href="{$domain}{$section}/editRelatedContent?item_id={$item.id}&amp;model_id={$related_model.id}" class="arrow-right" id="edit-related-model-{$related_model.id}-link">Edit...</a><br /><br />
  
  <script type="text/javascript">

        $('edit-related-model-{$related_model.id}-link').observe('click', function(e){ldelim}
            MODALS.load('{$section}/editRelatedContent?item_id={$item.id}&model_id={$related_model.id}', 'Related {$related_model.plural_name|strtolower}');
            e.stop();
        {rdelim});

      </script>
    
  {/foreach}
  
</div>

<div id="actions-area">
  
</div>