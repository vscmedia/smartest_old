<div id="work-area">
  <h3>{$item._model.name} preview: {$item.name}</h3>
  <div class="instruction">There are multiple metapages available for viewing {$item._model.plural_name|strtolower}. Please choose one to continue.</div>
  <form action="{$domain}websitemanager/preview">
    <div class="edit-form-row">
      <select name="page_id">
        {foreach from=$metapages item="metapage"}
        <option value="{$metapage.webid}"{if $metapage.id == $default_metapage.id} selected="selected"{/if}>{$metapage.title}</option>
        {/foreach}
      </select>
    </div>
    <input type="hidden" name="item_id" value="{$item.id}" />
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm()" />
      <input type="submit" value="Continue" />
    </div>
  </form>
</div>

<div id="actions-area">
  
</div>