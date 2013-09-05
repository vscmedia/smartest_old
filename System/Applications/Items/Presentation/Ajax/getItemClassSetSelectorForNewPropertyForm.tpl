{if $sets._count}
<select name="foreign_key_option_set_id">
  <option value="">Don't restrict</option>
{foreach from=$sets item="set"}
  <option value="SET:{$set.id}">{$set.label} ({$set.id})</option>
{/foreach}
</select>
{else}
<span class="form-hint">There are no available sets from the model '{$model.name}'. All {$model.plural_name|lower} will be available to choose.</span>
{/if}