{if empty($related_pages)}
  <i>No pages are linked to this {$item_model.name|strtolower}.</i><br /><br />
{else}
  <ul>
    {foreach from=$related_pages item="related_page"}
    <li>{$related_page.title}</li>
    {/foreach}
  </ul>
{/if}