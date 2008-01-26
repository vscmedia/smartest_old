<?php /* Smarty version 2.6.18, created on 2007-12-02 12:22:30
         compiled from /var/www/html/System/Applications/Pages/Presentation/addPage.stage2.tpl */ ?>
  <h3>Create a New Page</h3>
  
  <div class="instruction">Step 2 of 3: Please fill out the details below</div>
  
  <form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addPage" method="post">
  
    <input type="hidden" name="page_parent" value="<?php echo $this->_tpl_vars['page_parent']; ?>
" />
    <input type="hidden" name="stage" value="3">
    <input type="hidden" name="page_type" value="NORMAL">
    
    <div id="edit-form-layout">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Title:</div>
  	    <input type="text" name="page_title" id="page_title" value="<?php echo $this->_tpl_vars['newPage']['title']; ?>
" style="width:200px" />
  	  </div>
  	</div>
  	
  	<div id="edit-form-layout">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Address</div>
  	    <?php echo $this->_tpl_vars['domain']; ?>
<input type="text" name="page_url" id="page_url" value="<?php echo $this->_tpl_vars['newPage']['url']; ?>
" style="width:200px" />
  	    <?php if ($this->_tpl_vars['newPage']['type'] == 'ITEMCLASS'): ?><input type="button" value="&lt;&lt; Item URL Name" onclick="addField('page_url', 'name');" /><?php endif; ?>
  	    <?php if ($this->_tpl_vars['newPage']['type'] == 'ITEMCLASS'): ?><input type="button" value="&lt;&lt; Item Short ID" onclick="addField('page_url', 'id');" /><?php endif; ?>
  	  </div>
  	</div>
  	
  	<?php if ($this->_tpl_vars['newPage']['type'] == 'ITEMCLASS'): ?>
  	
  	<div id="edit-form-layout">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Select a Model</div>
  	    <select name="page_model">
  	      <?php $_from = $this->_tpl_vars['models']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['model']):
?>
  	      <option value="<?php echo $this->_tpl_vars['model']['id']; ?>
"<?php if ($this->_tpl_vars['newPage']['dataset_id'] == $this->_tpl_vars['model']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['model']['plural_name']; ?>
</option>
  	      <?php endforeach; endif; unset($_from); ?>
  	    </select>
  	  </div>
  	</div>
  	
  	<?php endif; ?>
  	
  	<?php if ($this->_tpl_vars['newPage']['type'] == 'TAG'): ?>
  	
  	<div id="edit-form-layout">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Select a Tag</div>
  	    <select name="page_tag">
  	      <?php $_from = $this->_tpl_vars['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tag']):
?>
  	      <option value="<?php echo $this->_tpl_vars['tag']['id']; ?>
"<?php if ($this->_tpl_vars['newPage']['dataset_id'] == $this->_tpl_vars['tag']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['tag']['label']; ?>
</option>
  	      <?php endforeach; endif; unset($_from); ?>
  	    </select>
  	  </div>
  	</div>
  	
  	<?php endif; ?>
  	
  	<div class="edit-form-row">
      <div class="form-section-label">Cache as Static HTML</div>
      <input type="radio" name="page_cache_as_html" id="page_cache_as_html_on" value="TRUE"<?php if ($this->_tpl_vars['newPage']['cache_as_html'] == 'TRUE'): ?> checked="checked"<?php endif; ?> />&nbsp;<label for="page_cache_as_html_on">Yes please</label>
      <input type="radio" name="page_cache_as_html" id="page_cache_as_html_off" value="FALSE"<?php if ($this->_tpl_vars['newPage']['cache_as_html'] == 'FALSE'): ?> checked="checked"<?php endif; ?> />&nbsp;<label for="page_cache_as_html_off">No, thanks</label>
    </div>
  	
    <div class="edit-form-row">
      <div class="form-section-label">Cache How Often?</div>
      <select name="page_cache_interval" style="width:300px">
        <option value="PERMANENT"<?php if ($this->_tpl_vars['newPage']['cache_interval'] == 'PERMANENT'): ?> selected="selected"<?php endif; ?>>Stay Cached Until Re-Published</option>
        <option value="MONTHLY"<?php if ($this->_tpl_vars['newPage']['cache_interval'] == 'MONTHLY'): ?> selected="selected"<?php endif; ?>>Every Month</option>
        <option value="DAILY"<?php if ($this->_tpl_vars['newPage']['cache_interval'] == 'DAILY'): ?> selected="selected"<?php endif; ?>>Every Day</option>
        <option value="HOURLY"<?php if ($this->_tpl_vars['newPage']['cache_interval'] == 'HOURLY'): ?> selected="selected"<?php endif; ?>>Every Hour</option>
        <option value="MINUTE"<?php if ($this->_tpl_vars['newPage']['cache_interval'] == 'MINUTE'): ?> selected="selected"<?php endif; ?>>Every Minute</option>
        <option value="SECOND"<?php if ($this->_tpl_vars['newPage']['cache_interval'] == 'SECOND'): ?> selected="selected"<?php endif; ?>>Every Second</option>
      </select>
    </div>
  	
  	<div id="edit-form-layout">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Use Preset Layout</div>
  	    <select name="page_preset" onchange="<?php echo 'if(this.value){document.getElementById(\'page_draft_template\').disabled=true;}else{document.getElementById(\'page_draft_template\').disabled=false;}'; ?>
">
  	      <?php $_from = $this->_tpl_vars['presets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['preset']):
?>
  	      <option value="<?php echo $this->_tpl_vars['preset']['plp_id']; ?>
"<?php if ($this->_tpl_vars['newPage']['preset'] == $this->_tpl_vars['preset']['plp_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['preset']['plp_label']; ?>
</option>
  	      <?php endforeach; endif; unset($_from); ?>
  	      <option value="">No preset</option>
  	    </select>
  	  </div>
  	</div>
  	
  	<div id="edit-form-layout">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Main Template</div>
  	    <select name="page_draft_template" id="page_draft_template"<?php if ($this->_tpl_vars['newPage']['preset']): ?> disabled="true"<?php endif; ?>>
  	      <?php $_from = $this->_tpl_vars['templates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['template']):
?>
  	      <option value="<?php echo $this->_tpl_vars['template']['filename']; ?>
"<?php if ($this->_tpl_vars['newPage']['draft_template'] == $this->_tpl_vars['template']['filename']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['template']['filename']; ?>
</option>
  	      <?php endforeach; endif; unset($_from); ?>
  	    </select>
  	  </div>
  	</div>
  	
  	<div class="edit-form-row">
      <div class="form-section-label">Search terms</div>
      <textarea name="page_search_field" style="width:500px;height:60px"><?php echo $this->_tpl_vars['newPage']['search_field']; ?>
</textarea>
    </div>
  	
  	<div class="edit-form-row">
      <div class="form-section-label">Page Description</div>
      <textarea name="page_description" style="width:500px;height:60px"><?php echo $this->_tpl_vars['newPage']['meta_description']; ?>
</textarea>
    </div>
  	
  	<div class="edit-form-row">
      <div class="form-section-label">Meta Description</div>
      <textarea name="page_meta_description" style="width:500px;height:60px"><?php echo $this->_tpl_vars['newPage']['meta_description']; ?>
</textarea>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Meta Keywords</div>
      <textarea name="page_keywords" style="width:500px;height:100px"><?php echo $this->_tpl_vars['newPage']['keywords']; ?>
</textarea>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Next &gt;&gt;" />
      </div>
    </div> 
  </form>