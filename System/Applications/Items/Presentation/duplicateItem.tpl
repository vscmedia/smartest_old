<div id="work-area">
  <h3>Copy {$item._model.name|strtolower}: “{$item.name}”</h3>
  <form action="{$domain}{$section}/createItemCopy" enctype="multipart/form-data" method="post">
    <input type="hidden" name="item_id" value="{$item.id}" />
    {if $sites._count > 1 && $item._model.shared == "1"}
    <div class="edit-form-row">
      <div class="form-section-label">Copy to which site?</div>
      <select name="destination_site_id">
{foreach from=$sites item="site"}
        <option value="{$site.id}"{if $site.id == $_site.id} selected="selected"{/if}>{if $site.id == $_site.id}This one ({$site.internal_label}){else}{$site.internal_label}{/if}</option>
{/foreach}
      </select>
    </div>
    {else}
    <input type="hidden" name="destination_site_id" value="{$item._site.id}" style="width:500px" />
    {/if}
    <div class="edit-form-row">
      <div class="form-section-label">Duplicate {$item._model.name|strtolower} name</div>
      <input type="text" name="duplicate_name" value="{$item.name} copy" id="duplicate-name" />
    </div>
    <h4>What to do with attached files:</h4>
{foreach from=$properties item="property"}
      <div class="edit-form-row">
        <div class="form-section-label">{$property.name}{* ({$property.datatype}) *}</div>
        {if $property.value.empty}
        No file selected<br />
        <select name="copy_decision[{$property.id}]" id="copy-decision-{$property.id}" class="copy-decision-select" disabled="disabled">
          <option value="empty" selected="selected">Leave empty</option>
        </select>
        {else}
        
        Current definition: {if $property.datatype == "SM_DATATYPE_ASSET"}{$property.value.url}{else}{$property.value}{/if}<br />
        
        <select name="itemproperty[{$property.id}][copy_decision]" id="copy-decision-{$property.id}" class="copy-decision-select">
          <option value="share">Use current, share if necessary</option>
          <option value="duplicate">Duplicate{if $property.value.type_info.storage.type == "file"} (requires an additional {$property.value.size}){/if}</option>
          <option value="empty">Leave behind and leave this property empty</option>
        </select>
        
        <div id="duplicate-asset-name-{$property.id}-holder" style="display:none;padding-top:5px">
          Name the duplicate file:<br />
          <input type="text" name="itemproperty[{$property.id}][duplicate_asset_name]" id="duplicate-asset-name-{$property.id}" value="{$property.value.label} copy" style="width:500px" />
        </div>
        
        <script type="text/javascript">
        $('copy-decision-{$property.id}').observe('change', function(e){ldelim}
          
          var element = Event.element(e);
          
          if(element.value == 'duplicate'){ldelim}
            $('duplicate-asset-name-{$property.id}-holder').show();
            $('duplicate-asset-name-{$property.id}').activate();
          {rdelim}else{ldelim}
            $('duplicate-asset-name-{$property.id}-holder').hide();
          {rdelim}
          
        {rdelim});
        </script>
        {/if}
      </div>
{/foreach}
  <div class="buttons-bar">
    <input type="submit" value="Duplicate" />
  </div>
  </form>
</div>