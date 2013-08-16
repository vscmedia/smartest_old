{if empty($groups)}
  <span class="form-hint">No groups currently exist that exlusively contain files that accepted by this placeholder type (<a href="{$domain}assets/newAssetGroup?filter_type={$selected_type}">create one</a>)</span>
  <input type="hidden" name="placeholder_filegroup" value="NONE" />
{else}
  <select name="placeholder_filegroup">
    <option value="NONE">Do not limit - Allow all files of the correct types</option>
    {foreach from=$groups item="group"}
    <option value="{$group.id}">{$group.label}</option>
    {/foreach}
  </select>
{/if}