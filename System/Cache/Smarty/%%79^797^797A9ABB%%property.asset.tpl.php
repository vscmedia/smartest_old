<?php /* Smarty version 2.6.18, created on 2007-11-28 06:28:57
         compiled from /var/www/html/System/Applications/Items/Presentation/Fields/property.asset.tpl */ ?>
<div class="form-section-label"><?php if ($this->_tpl_vars['property']['required'] == 'TRUE'): ?><strong><?php endif; ?><?php echo $this->_tpl_vars['property']['name']; ?>
 (<?php echo $this->_tpl_vars['property']['varname']; ?>
)<?php if ($this->_tpl_vars['property']['required'] == 'TRUE'): ?></strong> *<?php endif; ?></div>
<select name="item[<?php echo $this->_tpl_vars['property']['id']; ?>
]" id="item_property_<?php echo $this->_tpl_vars['property']['id']; ?>
">
  <?php if ($this->_tpl_vars['property']['required'] != 'TRUE'): ?><option value="0"></option><?php endif; ?>
  <?php $_from = $this->_tpl_vars['property']['_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['asset']):
?>
    <option value="<?php echo $this->_tpl_vars['asset']['id']; ?>
"<?php if ($this->_tpl_vars['value'] == $this->_tpl_vars['asset']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['asset']['url']; ?>
</option>
  <?php endforeach; endif; unset($_from); ?>
</select>

<?php if ($this->_tpl_vars['asset']['type_info']['editable'] == 'true'): ?>
<input type="button" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
assets/editAsset?from=item_edit&amp;asset_id='+$('item_property_<?php echo $this->_tpl_vars['property']['id']; ?>
').value" value="Edit &gt;&gt;" />
<?php endif; ?>