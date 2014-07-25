{if count($deleted_files) || count($failed_files) || count($untouched_files)}

<div class="instruction">Result: {$num_deleted_files} page{if $num_deleted_files == 1} was{else}s were{/if} cleared from the cache.</div>

<ul class="basic-list">
  {foreach from=$deleted_files item="file"}
    <li>Deleted: {$cache_path}{$file}</li>
  {/foreach}
  {foreach from=$failed_files item="file"}
    <li>Failed to Delete: {$cache_path}{$file}</li>
  {/foreach}
  {foreach from=$untouched_files item="file"}
    <li>Left Alone: {$cache_path}{$file}</li>
  {/foreach}
</ul>

{else}
  
<p>The cache is currently empty.</p>
  
{/if}