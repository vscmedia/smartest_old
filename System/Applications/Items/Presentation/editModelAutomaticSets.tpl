<div id="work-area">
  <h3>Sets to which {$model.plural_name|lower} are automatically added</h3>
  <div class="instruction">New {$model.plural_name|lower} will be added automatically to static sets chosen here</div>
  <form action="{$domain}{$section}/updateModelAutomaticSets" method="post">
    <input type="hidden" name="model_id" value="{$model.id}" />
    <ul class="basic-list scroll-list" style="height:350px;border:1px solid #ccc">
    {foreach from=$sets item="set"}
      <li><input type="checkbox" name="sets[{$set.id}]" id="set_{$set.id}" value="1"{if in_array($set.id, $selected_set_ids)} checked="checked"{/if} /> <label for="set_{$set.id}">{$set.label}</label></li>
    {/foreach}
    </ul>
    <div class="buttons-bar">{save_buttons}</div>
  </form>
</div>