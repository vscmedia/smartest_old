<?php /* Smarty version 2.6.18, created on 2007-12-03 18:18:47
         compiled from /var/www/html/System/Applications/Assets/Presentation/addPlaceholder.tpl */ ?>
<script language="javascript">

var customAssetClassName = false;

<?php echo '

function updateAssetClassName(){
	if(!customAssetClassName){
		// alert(\'test\');
		// $("assetclass_name").value = $("assetclass_label").value.toSlug();
	}
}

'; ?>

</script>

<div id="work-area">

<h3>Website Manager &gt; Assets &gt; Add a New Placeholder</h3>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
assets/insertPlaceholder" method="post" style="margin:0px">
  
<?php if ($this->_tpl_vars['name']): ?>
  <input type="hidden" name="placeholder_name" value="<?php echo $this->_tpl_vars['name']; ?>
" />
<?php endif; ?>

  <div id="edit-form-layout">

    <div class="edit-form-row">
      <div class="form-section-label">Label:</div>
      <input type="text" name="placeholder_label" id="placeholder_label" <?php if (! $this->_tpl_vars['name']): ?>onkeyup="updateAssetClassName();"<?php endif; ?> />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Markup/tag name: </div>
      <?php if ($this->_tpl_vars['name']): ?>{placeholder name="<?php echo $this->_tpl_vars['name']; ?>
"}<?php else: ?><input type="text" name="placeholder_name" id="placeholder_name" value="<?php echo $this->_tpl_vars['name']; ?>
" /><br />
        <span>If you don't enter a tag name, one will be generated for you.</span><?php endif; ?>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Content type:</div>
        <select name="placeholder_type">
          <?php $_from = $this->_tpl_vars['types']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['type']):
?>
          <?php if ($this->_tpl_vars['type']['id'] != 'SM_ASSETCLASS_CONTAINER'): ?><option value="<?php echo $this->_tpl_vars['type']['id']; ?>
"><?php echo $this->_tpl_vars['type']['label']; ?>
</option><?php endif; ?>
          <?php endforeach; endif; unset($_from); ?>
    
  </select>
  </div>
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
  </div>
</form>

</div>