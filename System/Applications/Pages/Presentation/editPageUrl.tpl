<script language="javascript">{literal}

function check(){
  var editForm = document.getElementById('editUrl');
  if(editForm.page_url.value==''){
    alert ('please enter the url');
    editForm.page_url.focus();
    return false;
  }else{
    return true;
  }
}

{/literal}</script>

<div id="work-area">

<h3 id="pageName">Page Details: {$pageInfo.title}</h3>

<form id="editUrl" name="editUrl" action="{$domain}{$section}/updatePageUrl" method="POST" style="margin:0px">

<input type="hidden" name="page_id" value="{$pageInfo.id}">
<input type="hidden" name="page_webid" value="{$pageInfo.webid}">
<input type="hidden" name="url_id" value="{$url.id}">

<div id="edit-form-layout">
  {if $pageInfo.type == "ITEMCLASS" && ($url.type == "SM_PAGEURL_INTERNAL_FORWARD" || $url.type == "SM_PAGEURL_NORMAL")}<div class="warning">Editing this URL will affect all {$model.plural_name|lower}.</div>{/if}
  <div class="edit-form-row">
    <div class="form-section-label">Address:</div>
      http://{$site.domain}{$domain}<input type="text" name="page_url" value="{$url.url}" style="width:200px" />
      {if !$url.is_default}<input type="checkbox" name="forward_to_default" id="forward_to_default" value="1"{if $url.type == "SM_PAGEURL_INTERNAL_FORWARD" || $url.type == 'SM_PAGEURL_ITEM_FORWARD'} checked="checked"{/if} /><label for="forward_to_default">Forward to default URL</label>{/if}
  </div>

  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
    <input type="submit" name="action" onclick="return check();" value="Save" />
  </div>
  
</div>
  
</form>

</div>