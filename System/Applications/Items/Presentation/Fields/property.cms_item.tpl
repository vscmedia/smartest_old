{capture name="name" assign="name"}item[{$property.id}]{/capture}
{* capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture *}

{item_select name=$_input_data.name id=$_input_data.id value=$value options=$property._options property_id=$property.id host_item_id=$item.id}

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}

{if $request_parameters.for != 'ipv'}

{if $item.id}

{* <script type="text/javascript">

  $('new-item-button-{$property.id}').observe('click', function(){ldelim}
    if($('item-name').value.charAt(1)){ldelim}
      $('next-action').value = 'createItem';
      $('property-id').value = '{$property.id}';
      $('new-item-form').submit();
    {rdelim}
  {rdelim});
  
</script> *}

{/if}

{/if}