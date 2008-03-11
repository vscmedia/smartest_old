<div id="work-area">
  <h3>New Uploads</h3>
  <form action="{$domain}{$section}/createAssetsFromNewUploads" method="post">
  <ul style="padding:0px;margin:0px;list-style-type:none">
  {foreach from=$files item="file" name="files"}
    <li style="padding:5px;background-color:#{cycle values="fff,ddd"}">
      <b>Title</b>: <input type="text" name="new_files[{$smarty.foreach.files.index}][name]" /><br />
      <b>File Path</b>: {$file.current_directory}{$file.filename}<input type="hidden" name="new_files[{$smarty.foreach.files.index}][filename]" value="{$file.current_directory}{$file.filename}" /><br />
      <b>Type</b>: {$file.type_label}<input type="hidden" name="new_files[{$smarty.foreach.files.index}][type]" value="{$file.type_code}" /><br />
      <b>Size</b>: {$file.size}<br />
      <b>Shared</b>: <input type="checkbox" id="share_{$smarty.foreach.files.index}" name="new_files[{$smarty.foreach.files.index}][shared]" value="1" /><label for="share_{$smarty.foreach.files.index}">Check here to share this file with other sites</label><br />
      {if $file.current_directory != $file.correct_directory}
      <b>This file will be moved to: {$file.correct_directory}{$file.filename}</b><br />
      {/if}
    </li>
  {/foreach}
  </ul>
  <div class="buttons-bar">
    <input type="submit" value="Finish" />
  </div>
  </form>
</div>