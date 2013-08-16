<div id="work-area">

  <h3 id="page-name">Add New URL</h3>

  <form id="addUrl" name="addUrl" action="{$domain}{$section}/insertPageUrl" method="post" style="margin:0px">

  <input type="hidden" name="page_id" value="{$pageInfo.id}" />
  <input type="hidden" name="page_webid" value="{$pageInfo.webid}" />
  {if $is_valid_item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}

  <div id="edit-form-layout">

    <div class="edit-form-row">
      <div class="form-section-label-full">URL</div>
      http://{$site.domain}{$domain}<input type="text" name="page_url" value="" />
      <input type="checkbox" name="forward_to_default" id="forward_to_default" value="1" onchange="toggleFormAreaVisibilityBasedOnCheckbox('forward_to_default', 'show-redirect-type');" /><label for="forward_to_default">Forward to default URL</label>
    </div>
    
    <div class="edit-form-row" style="display:none" id="show-redirect-type">
      <div class="form-section-label">Redirect type</div>
      <select name="url_redirect_type">
        <option value="301"{if $url.redirect_type == "301"} selected="selected"{/if}>301 Moved Permanently (SEO friendly - recommended)</option>
        <option value="302"{if $url.redirect_type == "302"} selected="selected"{/if}>302 Found (unspecified reason)</option>
        <option value="303"{if $url.redirect_type == "303"} selected="selected"{/if}>303 See other</option>
        <option value="307"{if $url.redirect_type == "307"} selected="selected"{/if}>307 Temporary redirect</option>
      </select>
    </div>
    
{if $is_valid_item}
    <div class="edit-form-row">
      <div class="form-section-label">Applies to</div>
      <select name="page_url_type">
        <option value="SINGLE_ITEM">This {$item._model.name|lower} only</option>
        <option value="ALL_ITEMS">All {$item._model.plural_name|lower}</option>
      </select>
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