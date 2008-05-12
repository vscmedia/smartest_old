<script language="javascript">
  
  var toggle = 0;
  
  {literal}
  function toggleCacheResultDetails(){
    
    if(toggle){
      // the details are showing - hide them
      toggle = 0;
      new Effect.BlindUp('cache-details', {duration:0.7});
      $('show-details-link').innerHTML = 'Show Details';
    }else{
      //the details are hidden - show them
      toggle = 1;
      new Effect.BlindDown('cache-details', {duration:0.7});
      $('show-details-link').innerHTML = 'Hide Details';
    }
    
  }
  {/literal}
  
</script>

<div id="work-area">
  
  <h3>Clear Pages Cache</h3>
  
  {if $show_result}
  
  {if count($deleted_files) || count($failed_files) || count($untouched_files)}
  
  <div class="instruction">Result: 0 pages were cleared from the cache. <a href="javascript:toggleCacheResultDetails()" id="show-details-link">Show Details</a></div>
  
  <div id="cache-details" style="display:none">
  
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
  
  </div>
  
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