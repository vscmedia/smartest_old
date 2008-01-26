<?php /* Smarty version 2.6.18, created on 2007-11-26 02:14:45
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/Fields/property.ml_text.tpl */ ?>
<div class="form-section-label"><?php if ($this->_tpl_vars['property']['required'] == 'TRUE'): ?><strong><?php endif; ?><?php echo $this->_tpl_vars['property']['name']; ?>
 (<?php echo $this->_tpl_vars['property']['varname']; ?>
)<?php if ($this->_tpl_vars['property']['required'] == 'TRUE'): ?></strong> *<?php endif; ?></div>
<textarea name="item[<?php echo $this->_tpl_vars['property']['id']; ?>
]" rows="3" cols="20" style="width:350px;height:80px"><?php echo $this->_tpl_vars['value']; ?>
</textarea>