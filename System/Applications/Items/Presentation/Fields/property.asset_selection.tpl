{if empty($value)}
  Save item first, then you will be able to choose files.
{else}
  {$value.summary}
  <a href="{$domain}ipv:{$section}/chooseFiles?item_id={$item.id}&amp;property_id={$property.id}">Choose files</a>
{/if}