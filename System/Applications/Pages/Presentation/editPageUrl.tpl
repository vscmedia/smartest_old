<div id="work-area">

<form id="editUrl" name="editUrl" action="{$domain}{$section}/updatePageUrl" method="POST" style="margin:0px">

  <input type="hidden" name="page_id" value="{$pageInfo.id}" />
  <input type="hidden" name="page_webid" value="{$pageInfo.webid}" />
  <input type="hidden" name="url_id" value="{$url.id}" />

  <div id="edit-form-layout">
    {if $pageInfo.type == "ITEMCLASS" && ($url.type == "SM_PAGEURL_INTERNAL_FORWARD" || $url.type == "SM_PAGEURL_NORMAL")}<div class="warning">Editing this URL will affect all {$model.plural_name|lower}.</div>{/if}
  
    <div class="edit-form-row">
      <div class="form-section-label-full">URL:</div>
        http://{$site.domain}{$domain}<input type="text" name="page_url" value="{$url.url}" style="width:250px" />
        <br />{if !$url.is_default}<input type="checkbox" name="forward_to_default" id="forward_to_default" value="1"{if $url.type == "SM_PAGEURL_INTERNAL_FORWARD" || $url.type == 'SM_PAGEURL_ITEM_FORWARD'} checked="checked"{/if} onchange="toggleFormAreaVisibilityBasedOnCheckbox('forward_to_default', 'show-redirect-type');" /><label for="forward_to_default">Forward to default URL</label>{/if}
    </div>
  
    <div class="edit-form-row" style="display:{if $url.type == "SM_PAGEURL_INTERNAL_FORWARD" || $url.type == "SM_PAGEURL_ITEM_FORWARD"}block{else}none{/if}" id="show-redirect-type">
      <div class="form-section-label">Redirect type</div>
      <select name="url_redirect_type">
        <option value="301"{if $url.redirect_type == "301"} selected="selected"{/if}>301 Moved Permanently (SEO friendly - recommended)</option>
        <option value="302"{if $url.redirect_type == "302"} selected="selected"{/if}>302 Found (unspecified reason)</option>
        <option value="303"{if $url.redirect_type == "303"} selected="selected"{/if}>303 See other</option>
        <option value="307"{if $url.redirect_type == "307"} selected="selected"{/if}>307 Temporary redirect</option>
      </select>
    </div>

    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" style="display:none" id="saver-gif" alt="" />
      <input type="button" value="Cancel" onclick="MODALS.hideViewer();" />
      <input type="button" name="action" onclick="return savePageUrlChanges();" value="Save" />
    </div>
  
  </div>
  
</form>

</div>