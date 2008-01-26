<?php /* Smarty version 2.6.18, created on 2007-11-26 01:23:30
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/MetaData/Presentation/defineFieldOnPage.tpl */ ?>
<div id="work-area">
<h3 id="definePageProperty">Define Page Property: <?php echo $this->_tpl_vars['field_name']; ?>
</h3>


<form id="defineProperty" name="defineProperty" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updatePagePropertyValue" method="POST" style="margin:0px">
<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page_id']; ?>
">
<input type="hidden" name="field_name" value="<?php echo $this->_tpl_vars['field_name']; ?>
">

<div id="edit-form-layout">
  <div class="edit-form-row">
    <div class="form-section-label">Property Value:</div>
    <?php if ($this->_tpl_vars['field_type'] == 'SM_DATATYPE_DROPDOWN_MENU'): ?>
    <select name="field_content">
      <?php $_from = $this->_tpl_vars['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['option']):
?>
      <option value="<?php echo $this->_tpl_vars['option']['value']; ?>
"<?php if ($this->_tpl_vars['option']['value'] == $this->_tpl_vars['value']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['option']['label']; ?>
</option>
      <?php endforeach; endif; unset($_from); ?>
    </select>
    <?php else: ?>
    <input type="text" name="field_content" value="<?php echo $this->_tpl_vars['value']; ?>
" />
    <?php endif; ?>
  </div>
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" name="action" value="Save" />
      <input type="button" value="Cancel" onclick="cancelForm();" />
    </div>
  </div>
</div>

  <tr>
    <td colspan="2" class="submit" align="right">
    	
  </tr>
</table>
</form>

</div>