{$value.php_class}
{if empty($value)}
  Save item first, then you will be able to choose items.
{else}
  {$value.summary}
  <a href="{$domain}ipv:{$section}/chooseItems?item_id={$item.id}&amp;property_id={$property.id}">Choose items</a>
  {if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}
{/if}