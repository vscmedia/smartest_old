{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{item_select name=$name id=$property_id value=$value options=$property._options}

{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}

{if $request_parameters.for != 'ipv'}

<ul class="item_property_actions">

{if $item.id}
  <li><a href="#" onclick="return false;" id="new-item-button-{$property.id}"><img src="{$domain}Resources/Icons/add.png" alt="" /></a></li>
{else}
  <li><a href="#" onclick="return false;" id="new-item-button-{$property.id}"><img src="{$domain}Resources/Icons/add.png" alt="" /></a>
    <script type="text/javascript">
    $('new-item-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Define this property with a new item');{literal}}{/literal});
    $('new-item-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
    </script></li>
{/if}

  <li style="padding-top:6px"><span class="form-hint" id="file-property-tooltip-{$property.id}"></span></li>

</ul>

{/if}

{if $asset.type_info.editable=='true'}
<input type="button" onclick="window.location='{$domain}datamanager/editItem?from=child_edit&amp;item_id='+$('item_property_{$property.id}').value" value="Edit 
&gt;&gt;" />
{/if}

{if $request_parameters.for != 'ipv'}

{if $item.id}

<script type="text/javascript">

  $('new-item-button-{$property.id}').observe('click', function(){ldelim}
    if($('item-name').value.charAt(1)){ldelim}
      $('next-action').value = 'createItem';
      $('property-id').value = '{$property.id}';
      $('new-item-form').submit();
    {rdelim}
  {rdelim});
  
</script>

{/if}

{/if}