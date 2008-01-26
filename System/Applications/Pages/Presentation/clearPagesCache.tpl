<div id="work-area">
  
  <h3>Clear Pages Cache</h3>
  
  {if $show_result}
  
  <div class="instruction">Result:</div>
  
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
  
  {/if}
  
</div>