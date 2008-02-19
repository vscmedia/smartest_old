<div id="work-area">
  
  <h3>Detect New File Uploads</h3>
  
  {foreach from=$new_files key="path" item="files_of_this_type"}
  <h4 style="margin-bottom:0px">Files found in: {$path}</h4>
  <p style="margin-top:0px">({foreach from=$types_info[$path] item="type" name="types"}{if $smarty.foreach.types.index > 0}, {/if}{$type.label}{/foreach})</p>
  <ul>
    {foreach from=$files_of_this_type item="file"}
      <li>{$path}{$file}</li>
      {foreachelse}
      <li style="list-style-type:none"><i>No New Files in this Location</i></li>
    {/foreach}
  </ul>
  {/foreach}
  
</div>