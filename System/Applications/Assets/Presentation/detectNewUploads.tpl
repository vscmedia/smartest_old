<div id="work-area">
  
  <h3>Detect New File Uploads</h3>
  <div class="instruction">Check the box next to any files that you wish to add to the repository.</div>
  <form action="{$domain}{$section}/enterNewFileData" method="post">
  {foreach from=$new_files key="path" item="files_of_this_type"}
  <h4 style="margin-bottom:0px">Files found in: {$path}</h4>
  <p style="margin-top:0px">({foreach from=$types_info[$path] item="type" name="types"}{if $smarty.foreach.types.index > 0}, {/if}{$type.label}{/foreach})</p>
  <ul style="list-style-type:none">
    {foreach from=$files_of_this_type item="file"}
      {capture name="full_path" assign="full_path"}{$path}{$file}{/capture}
      <li><input type="checkbox" name="new_files[]" value="{$full_path}" id="file_{$full_path|varname}" />&nbsp;<label for="file_{$full_path|varname}">{$full_path}</label></li>
      {foreachelse}
      <li style="list-style-type:none"><i>No New Files in this Location</i></li>
    {/foreach}
  </ul>
  {/foreach}
  <div class="buttons-bar"><input type="submit" value="Continue &gt;&gt;" /></div>
  </form>
</div>