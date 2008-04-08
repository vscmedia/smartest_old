<div id="work-area">
  
  <h3>Clear Pages Cache</h3>
  
  {if $show_result}
  
  {if count($deleted_files) || count($failed_files) || count($untouched_files)}
  
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
  
  {else}
  
  <p>The cache is currently empty.</p>
  
  {/if}
  
  {/if}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="OK" onclick="window.location='{$domain}smartest/pages'" />
    </div>
  </div>
  
</div>