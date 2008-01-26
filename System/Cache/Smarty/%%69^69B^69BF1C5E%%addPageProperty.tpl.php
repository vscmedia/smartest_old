<?php /* Smarty version 2.6.18, created on 2007-12-04 06:39:51
         compiled from /var/www/html/System/Applications/MetaData/Presentation/addPageProperty.tpl */ ?>
<div id="work-area">
<h3 id="definePageProperty">Add Property</h3>

<form id="definePageProperty" name="definePageProperty" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertPageProperty" method="POST" style="margin:0px">
<input type="hidden" name="site_id" value="<?php echo $this->_tpl_vars['content']['site_id']; ?>
">

<div id="edit-form-layout">
  <div class="edit-form-row">
    <div class="form-section-label">Property Name: </div>
    <input type="text" name="property_name" value="<?php echo $this->_tpl_vars['content']['name']; ?>
" />
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