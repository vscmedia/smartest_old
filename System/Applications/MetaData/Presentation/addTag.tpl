<div id="work-area">
<h3>Add Tag</h3>

<div class="instruction">Enter one or more tags. Separate multiple tags with commas.</div>

<form action="{$domain}{$section}/insertTag" method="post">
  <div id="edit-form-layout">
    <div class="edit-form-row">
      <div class="form-section-label">Tag Name(s): </div>
      <input type="text" name="tag_label" />
    </div>
    {if $item.id}
    <div class="edit-form-row">
      <input type="checkbox" name="tag_item" value="1" checked="checked" id="tag_item_checkbox" />
      <label for="tag_item_checkbox">Tag '{$item.name}' with new tags I create here</label>
      <input type="hidden" name="item_id" value="{$item.id}" />
    </div>
    {/if}
    {if $page.id}
    <div class="edit-form-row">
      <input type="checkbox" name="tag_page" value="1" checked="checked" id="tag_page_checkbox" />
      <label for="tag_page_checkbox">Tag '{$page.name}' with new tags I create here</label>
      <input type="hidden" name="page_id" value="{$page.id}" />
    </div>
    {/if}
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm();" />
        <input type="submit" name="action" value="Save" />
      </div>
    </div>
  </div>
</form>

</div>

<div id="actions-area">
  
</div>