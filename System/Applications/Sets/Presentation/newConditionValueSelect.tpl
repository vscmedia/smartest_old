{if $aspect == "_SMARTEST_ITEM_PIECE_ITSELF"}
{if $operator == 8 || $operator == 9}
<select name="new_condition_value" id="new-condition-value">
{foreach from=$tags item="t"}
  <option value="{$t.value}">{$t.label}</option>
{/foreach}
</select>
{/if}
{elseif $aspect == "_SMARTEST_ITEM_NAME"}
<input type="text" name="new_condition_value" id="new-condition-value" value="" />
<script type="text/javascript">$('new-condition-value').focus();</script>
{elseif $aspect == "_SMARTEST_ITEM_ID"}
<input type="text" name="new_condition_value" id="new-condition-value" value="" style="width:50px" />
<script type="text/javascript">$('new-condition-value').focus();</script>
{elseif $aspect == "_SMARTEST_ITEM_NUM_HITS"}
<input type="text" name="new_condition_value" id="new-condition-value" value="" style="width:50px" />
<script type="text/javascript">$('new-condition-value').focus();</script>
{elseif $aspect == "_SMARTEST_ITEM_NUM_COMMENTS"}
<input type="text" name="new_condition_value" id="new-condition-value" value="" style="width:50px" />
<script type="text/javascript">$('new-condition-value').focus();</script>
{else}
{$property_input_html}
{* /ajax:sets/newConditionValueSelect?aspect={$aspect}&amp;operator={$operator}&amp;v={$selectedValue} *}
{/if}