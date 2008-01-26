<?php /* Smarty version 2.6.18, created on 2007-12-01 18:35:25
         compiled from /var/www/html/System/Applications/Sets/Presentation/addSet.tpl */ ?>
<div id="work-area">
  
<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/sets">Sets</a> &gt; Create a new set</h3>
  
  <form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertSet">
  
    <div class="edit-form-layout">
    
			<div class="edit-form-row">
				<div class="form-section-label">Set Name:</div>
				<input type="text" name="set_name" id="set_name" value="Untitled Set" />
			</div>
				
			<div class="edit-form-row">
				<div class="form-section-label">With items from model:</div>
				<select name="set_model_id" id="model_select" >
			    <option value="">Please Choose...</option>
			    <?php $_from = $this->_tpl_vars['models']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['model']):
?>
				  <option <?php if ($this->_tpl_vars['model']['itemclass_id'] == $this->_tpl_vars['content']['model_id']): ?> selected<?php endif; ?> value="<?php echo $this->_tpl_vars['model']['itemclass_id']; ?>
"><?php echo $this->_tpl_vars['model']['itemclass_plural_name']; ?>
</option>
				  <?php endforeach; endif; unset($_from); ?>
				</select>
			</div>
				
			<div class="edit-form-row">
				<div class="form-section-label">Set Type</div>
				<select  name="set_type" id="set_type" >
					  <option value="">Please Choose...</option>
					  <option value="STATIC" <?php if ($this->_tpl_vars['content']['type'] == 'STATIC'): ?> selected<?php endif; ?>>Static (Folder)</option>
					  <option value="DYNAMIC" <?php if ($this->_tpl_vars['content']['type'] == 'DYNAMIC'): ?> selected<?php endif; ?> >Smart (Dynamic Saved Query)</option>
				</select>
			</div>
			
			<div class="edit-form-row">
			  <div class="form-section-label">Share this Set?</div>
			  <input type="checkbox" name="set_shared" /> Check here to make this set available to all sites.
			</div>
				
			<div class="edit-form-row">
				<div class="buttons-bar">
				  <input type="button" value="Cancel" />
					<input type="submit" value="Continue" />
				</div>
			</div>
		
		</div>

	</form>