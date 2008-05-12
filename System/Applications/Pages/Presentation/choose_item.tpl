<div class="instruction">This is an object meta-page. it is only used for editing info about {$model.plural_name}.</div>
<div class="instruction">{$chooser_message}</div>

<form action="{$domain}{$continue_action}" method="get" id="item_chooser">
  <input type="hidden" name="page_id" value="{$page.webid}" />
  <select name="item_id" style="width:300px" onchange="$('item_choooser').submit()">
    {foreach from=$items item="item"}
    <option value="{$item.id}">{$item.name}</option>
    {/foreach}
  </select>
  <input type="submit" value="Go" />
</form>