<div id="work-area">
  
  <h3>Detect New File Uploads</h3>
  
  <div class="instruction">Check the box next to any files that you wish to add to the repository.</div>
  
  <form action="{$domain}{$section}/enterNewFileData" method="post">
    {foreach from=$new_files key="path" item="files_of_this_type"}
    <div class="special-box" style="margin-bottom:5px">Files found in: <strong><code>{$path}</code></strong></div>
    <span class="form-hint" style="padding:0px">{foreach from=$types_info[$path] item="type" name="types"}{if $smarty.foreach.types.index > 0}, {/if}{$type.label}{/foreach}</span>
    {if count($files_of_this_type)}
      <p style="margin-bottom:0px">Select <a href="javascript:new Smartest.UI.CheckBoxGroup('checkbox-{$path|varname}').selectAll();">all</a> | <a href="javascript:new Smartest.UI.CheckBoxGroup('checkbox-{$path|varname}').selectNone();">none</a></p>
    {/if}
    <ul style="list-style-type:none;margin-top:8px">
      {foreach from=$files_of_this_type item="file"}
        {capture name="full_path" assign="full_path"}{$path}{$file}{/capture}
        <li><input type="checkbox" name="new_files[]" value="{$full_path}" id="file_{$full_path|varname}" class="checkbox-{$path|varname}" />&nbsp;<label for="file_{$full_path|varname}"><code>{$full_path}</code></label></li>
        {foreachelse}
        <li style="list-style-type:none;color:#999"><i>No new files in this location.</i></li>
      {/foreach}
    </ul>
    {/foreach}
    <div class="buttons-bar"><input type="submit" value="Continue &gt;&gt;" /></div>
  </form>
  
</div>