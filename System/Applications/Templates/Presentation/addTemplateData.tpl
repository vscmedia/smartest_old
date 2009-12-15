<div id="work-area">
  <h3>New Templates</h3>
  <form action="{$domain}{$section}/createTemplateAssetsFromFiles" method="post">
  <ul style="padding:0px;margin:0px;list-style-type:none">
  {foreach from=$files item="file" name="files"}
    <li style="padding:10px;background-color:#{cycle values="fff,ddd"}">
      
      <b>Title</b>: <input type="text" name="new_files[{$smarty.foreach.files.index}][name]" value="{$file.suggested_name}" /><br />
      <b>File Path</b>: <code>{$file.current_directory}{$file.filename}</code><input type="hidden" name="new_files[{$smarty.foreach.files.index}][filename]" value="{$file.current_directory}{$file.filename}" /><br />
      <b>Import as</b>:
      
      {if count($file.possible_types) > 1}
        {if $file.suffix_recognized}
        <select name="new_files[{$smarty.foreach.files.index}][type]">{foreach from=$file.possible_types item="ptype"}<option value="{$ptype.type.id}"{if $file.current_directory == $ptype.storage_location} selected="selected"{/if}>{$ptype.type.label}{if $file.current_directory != $ptype.storage_location} (will be moved to /{$ptype.storage_location}{$file.filename}){/if}</option>{/foreach}</select>
        {else}
        <select name="new_files[{$smarty.foreach.files.index}][type]">{foreach from=$file.possible_types item="ptype"}<option value="{$ptype.type.id}">{$ptype.type.label} (will be renamed {$ptype.filename})</option>{/foreach}</select>
        {/if}
      {else}
        {$file.possible_types[0].type.label}{if $file.current_directory != $file.possible_types[0].storage_location} (file will be moved to <code>{$file.possible_types[0].storage_location}{$file.filename}</code>){/if}<input type="hidden" name="new_files[{$smarty.foreach.files.index}][type]" value="{$file.possible_types[0].type.id}" />
      {/if}<br />
      
      <b>Size</b>: {$file.size}<br />
      <b>Shared</b>: <input type="checkbox" id="share_{$smarty.foreach.files.index}" name="new_files[{$smarty.foreach.files.index}][shared]" value="1" /><label for="share_{$smarty.foreach.files.index}">Check here to share this file with other sites</label><br />
      {if !$file.suffix_recognized}<div class="warning">The suffix of this file (.{$file.actual_suffix}) has not been recognized.</div>{/if}
      
{*      {if count($file.possible_groups)}<b>Add to group</b>:
      <select name="new_files[{$smarty.foreach.files.index}][group]">
        <option value="">None</option>
{foreach from=$file.possible_groups item="group"}
        <option value="{$group.id}">{$group.label}</option>
{/foreach}
      </select><br />{/if} *}
      
    </li>
  {/foreach}
  </ul>
  <div class="buttons-bar">
    <input type="button" value="&lt;&lt; Back" onclick="window.location='{$domain}{$section}/import'" />
    <input type="submit" value="Finish" />
  </div>
  </form>
</div>