<?php /* Smarty version 2.6.18, created on 2007-12-04 08:15:52
         compiled from /var/www/html/System/Applications/MetaData/Presentation/addTag.tpl */ ?>
<div id="work-area">
<h3>Add Tag</h3>

<div class="instruction">Enter the name of your new tag</div>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertTag" method="post">
  <div id="edit-form-layout">
    <div class="edit-form-row">
      <div class="form-section-label">Tag Name: </div>
      <input type="text" name="tag_label" />
    </div>
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