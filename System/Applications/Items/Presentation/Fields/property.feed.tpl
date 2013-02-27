{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{url_input name=$name id=$property_id value=$value} {if $item.id}<a href="{$domain}ipv:datamanager/previewFeedItemPropertyValue?item_id={$item.id}&amp;property_id={$property.id}">Preview</a>{/if}

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}
{* if strlen($value)}<img src="{$value.qr_code_url}" style="width:100px;float:right" alt="{$value}" class="ipv-qr-code" /><div style="clear:both"></div>{/if *}
{* $value.qr_code_image.width_100 *}