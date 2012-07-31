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
    <input type="hidden" name="destination_site_id" value="{$item.site.id}" />
    {/if}
    <h4>What to do with attached objects</h4>
{foreach from=$properties item="property"}
      <div class="edit-form-row">
        <div class="form-section-label">{$property.name} ({$property.datatype})</div>
        Current definition: {if $property.datatype == "SM_DATATYPE_ASSET"}{$property.value.url}{else}{$property.value}{/if}<br />
        <select name="copy_decision[{$property.id}]">
          <option value="share">Use current, share if necessary</option>
          <option value="duplicate">Duplicate</option>
          <option value="empty">Leave behind and leave this property empty</option>
        </select>
      </div>
{/foreach}
  <div class="buttons-bar">
    <input type="submit" value="Duplicate" />
  </div>
  </form>
</div>