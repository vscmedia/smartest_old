<div class="instruction">This is your tag page. It shows how tags will look when clicked.</div>
<div class="instruction">{$chooser_message}</div>

<form action="{$domain}{$continue_action}" method="get" id="tag_chooser">
  <input type="hidden" name="page_id" value="{$page.webid}" />
  <select name="tag" style="width:300px" onchange="$('tag_chooser').submit()">
    {foreach from=$tags item="tag"}
    <option value="{$tag.name}">{$tag.label}</option>
    {/foreach}
  </select>
  <input type="submit" value="Continue" />
</form>