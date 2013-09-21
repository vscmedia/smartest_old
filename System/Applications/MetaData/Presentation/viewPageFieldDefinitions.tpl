<div id="work-area">
  <h3>Definitions of page field: {$field.label} ({$field.name})</h3>
  {if count($definitions)}
  <ul class="basic-list">
    {foreach from=$definitions item="def"}
    <li><img src="{$domain}Resources/Icons/page.png" />&nbsp;Page:&nbsp;<b>{$def.page_title}</b>:&nbsp;Draft Value: <b>{$def.pagepropertyvalue_draft_value}</b>{if $def.pagepropertyvalue_live_value}&nbsp;Live Value: <b>{$def.pagepropertyvalue_live_value}</b>{/if}</li>
    {/foreach}
  </ul>
  {else}
  <div class="special-box">This field hasn't been defined anywhere yet. To define the field, refer to it in a page template using the <code>&lt;?sm:field name="name":?&gt;</code> tag.</div>
  {/if}
  <div class="buttons-bar">
    <input type="button" value="Done" onclick="cancelForm();" />
  </div>
</div>