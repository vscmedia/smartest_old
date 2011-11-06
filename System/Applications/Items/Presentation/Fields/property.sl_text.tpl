{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{text_input name=$name value=$value id=$property_id}<span class="form-hint">{if strlen($property.hint)}{$property.hint}{/if} Max 255 characters</span>

<div id="autocomplete_choices_{$property.id}" class="autocomplete" style="display:none"></div>

<script type="text/javascript">
    
new Ajax.Autocompleter("{$property_id}", "autocomplete_choices_{$property.id}", "/ajax:datamanager/getTextIpvAutoSuggestValues", {literal}{
    paramName: "str", 
    minChars: 2,
    delay: 50,
    width: 300,
    {/literal}parameters: 'property_id={$property.id}',{literal}
});

{/literal}

</script>