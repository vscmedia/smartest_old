{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="input_id" assign="input_id"}item_property_{$property.id}{/capture}

{asset_select id=$input_id name=$name value=$value options=$property._options required=$property.required}

{if $value.id}
  <ul class="item_property_actions">
    
    {if $item}
      <li><a href="{$domain}assets/startNewFileCreationForItemPropertyValue?property_id={$property.id}&amp;item_id={$item.id}" title="Use a new file instead" id="new-asset-button-{$property.id}"><img src="{$domain}Resources/Icons/page_add.png" alt="" /></a>
        <script type="text/javascript">
        $('new-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Define this property with a new file');{literal}}{/literal});
        $('new-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
        </script></li>
    {/if}
    
    {if $value.id && is_array($value.type_info)}
      <!--<input type="button" onclick="window.location='{$domain}assets/editAsset?from=item_edit&amp;asset_id='+$('item_property_').value" value="Edit &gt;&gt;" />-->
      <li><a href="javascript:;" id="edit-asset-button-{$property.id}" title="Edit this file"><img src="{$domain}Resources/Icons/pencil.png" alt="" /></a>
      <script type="text/javascript">
      $('edit-asset-button-{$property.id}').observe('click', function(){literal}{{/literal}window.location='{$domain}assets/editAsset?from=item_edit&asset_id='+$('{$input_id}').value{literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit the selected file');{literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
      </script></li>
    {/if}
    
    {if $value.id && is_array($value.type_info) && count($value.type_info.param)}
      <li><a href="{$domain}ipv:{$section}/editAssetData?item_id={$item.id}&amp;property_id={$property.id}" id="edit-params-button-{$property.id}" title="Edit display parameters for this instance of this file"><img src="{$domain}Resources/Icons/page_edit.png" alt="" /></a>
        <script type="text/javascript">
        $('edit-params-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit display parameters for this instance');{literal}}{/literal});
        $('edit-params-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
        </script></li>
    {/if}
    
    <li style="padding-top:6px"><span class="form-hint" id="file-property-tooltip-{$property.id}"></span></li>
    
  </ul>
{/if}