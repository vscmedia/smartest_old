{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{twitter_acct_input name=$name value=$value id=$property_id}{if $item.id && strlen($value)} <a href="{$domain}ipv:datamanager/previewTwitterAccountItemPropertyValue?item_id={$item.id}&amp;property_id={$property.id}">Preview</a>{/if}
<span class="form-hint">{if strlen($property.hint)}{$property.hint} {/if}Max 20 characters</span>