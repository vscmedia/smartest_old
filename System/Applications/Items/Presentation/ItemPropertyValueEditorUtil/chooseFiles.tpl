<div id="work-area">
    <h3>Define property {$property.name} for item {$item.name}</h3>
    <form action="{$domain}ipv:{$section}/updateFilesSelection" method="post">
      
      <input type="hidden" name="item_id" value="{$item.id}" />
      <input type="hidden" name="property_id" value="{$property.id}" />
      
      <div class="instruction">check the box next to the items you'd like to choose</div>
      
      <ul class="basic-list scroll-list" style="height:350px;border:1px solid #ccc">
        {foreach from=$options item="option"}
        <li><input type="checkbox" name="items[{$option.id}]" id="item_{$option.id}"{if in_array($option.id, $selected_ids)} checked="checked"{/if} /><label for="item_{$option.id}">{$option.label}</label></li>
        {/foreach}
      </ul>
      
      <div id="edit-form-layout">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" name="action" value="Save" />
        </div>
      </div>
      
    </form>
</div>

<div id="actions-area">
    
</div>