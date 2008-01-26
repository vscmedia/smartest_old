<?php /* Smarty version 2.6.18, created on 2007-12-03 09:03:53
         compiled from /var/www/html/System/Applications/Pages/Presentation/editPageUrl.tpl */ ?>
<script language="javascript"><?php echo '

function check(){
  var editForm = document.getElementById(\'editUrl\');
  if(editForm.page_url.value==\'\'){
    alert (\'please enter the url\');
    editForm.page_url.focus();
    return false;
  }else{
    return true;
  }
}

'; ?>
</script>

<div id="work-area">

<h3 id="pageName">Page Details: <?php echo $this->_tpl_vars['pageInfo']['title']; ?>
</h3>

<form id="editUrl" name="editUrl" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updatePageUrl" method="POST" style="margin:0px">

<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['pageInfo']['id']; ?>
">
<input type="hidden" name="page_webid" value="<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
">
<input type="hidden" name="url_id" value="<?php echo $this->_tpl_vars['url']['id']; ?>
">

<div id="edit-form-layout">

  <div class="edit-form-row">
    <div class="form-section-label">Address:</div>
      http://<?php echo $this->_tpl_vars['site']['domain']; ?>
/<input type="text" name="page_url" value="<?php echo $this->_tpl_vars['url']['url']; ?>
" style="width:200px" />
  </div>

  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
    <input type="submit" name="action" onclick="return check();" value="Save" />
  </div>
  
</div>
  
</form>

</div>