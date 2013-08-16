<div id="work-area">
  <form id="transferUrl" name="transferUrl" action="{$domain}{$section}/transferPageUrlAction" method="POST" style="margin:0px">
    
    <input type="hidden" name="page_id" value="{$pageInfo.id}" />
    <input type="hidden" name="page_webid" value="{$pageInfo.webid}" />
    <input type="hidden" name="url_id" value="{$url.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Transfer to page:</div>
      <select name="url_page_id" style="width:320px">
        {foreach from=$pages item="page"}
            {if $page.info.type != 'ITEMCLASS' && $page.info.id != $pageInfo.id}
            <option value="{$page.info.id}">+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title|escape}</option>
            {/if}
          {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Forwarding:</div>
      <select name="url_redirect_type" style="width:320px">
        <option value="OFF">None</option>
        <option value="301" selected="selected">301 Moved Permanently (SEO friendly - recommended)</option>
        <option value="302">302 Found (unspecified reason)</option>
        <option value="303">303 See other</option>
        <option value="307">307 Temporary redirect</option>
      </select>
    </div>
    
    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" style="display:none" id="saver-gif" alt="" />
      <input type="button" value="Cancel" onclick="MODALS.hideViewer();" />
      <input type="button" name="action" onclick="return effectPageUrlTransfer();" value="Save" />
    </div>
    
  </form>
</div>