{if empty($related_items)}
  <i>No{if $item_model.id == $model.id} other{/if} {$model.plural_name|strtolower} are linked to this {$item_model.name|strtolower}.</i><br /><br />
{else}
  <ul>
    {foreach from=$related_items item="related_item"}
    <li>{$related_item.name}</li>
    {/foreach}
  </ul>
{/if}