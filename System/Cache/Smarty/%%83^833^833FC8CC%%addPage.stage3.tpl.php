<?php /* Smarty version 2.6.18, created on 2007-12-02 12:24:01
         compiled from /var/www/html/System/Applications/Pages/Presentation/addPage.stage3.tpl */ ?>
  <h3>Create a New Page</h3>
  
  <div class="instruction-text">Step 3 of 3: Confirm the details of your new page</div>
  
  <form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertPage" method="post">
    
    <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
      <tr>
        <td style="width:150px;background-color:#fff" valign="top">Title:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['title']; ?>
</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">URL:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['newPage']['url']; ?>
</td>
      </tr>
      <?php if (is_numeric ( $this->_tpl_vars['newPage']['dataset_id'] ) && $this->_tpl_vars['newPage']['type'] == 'ITEMCLASS'): ?>
      <tr>
        <td style="background-color:#fff" valign="top">Represents model:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['model_plural']; ?>
</td>
      </tr>
      <?php endif; ?>
      <?php if (is_numeric ( $this->_tpl_vars['newPage']['dataset_id'] ) && $this->_tpl_vars['newPage']['type'] == 'TAG'): ?>
      <tr>
        <td style="background-color:#fff" valign="top">Retrieves tag:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['tag_label']; ?>
</td>
      </tr>
      <?php endif; ?>
      <tr>
        <td style="background-color:#fff" valign="top">Cache as HTML:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['cache_as_html']; ?>
</td>
      </tr>
      <?php if ($this->_tpl_vars['newPage']['cache_as_html'] == 'TRUE'): ?>
      <tr>
        <td style="background-color:#fff" valign="top">Cache Interval:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['cache_interval']; ?>
</td>
      </tr>
      <?php endif; ?>
      <tr>
        <td style="background-color:#fff" valign="top">Layout Preset:</td>
        <td style="background-color:#fff" valign="top"><?php if ($this->_tpl_vars['newPage']['preset']): ?><?php echo $this->_tpl_vars['newPage']['preset_label']; ?>
<?php else: ?><i>NONE</i><?php endif; ?></td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Main Template:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['draft_template']; ?>
</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Description:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['description']; ?>
</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Search Keywords:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['search_field']; ?>
</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta Description:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['meta_description']; ?>
</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta Keywords:</td>
        <td style="background-color:#fff" valign="top"><?php echo $this->_tpl_vars['newPage']['keywords']; ?>
</td>
      </tr>
    </table>
    
    <p>After the page has been built, take me:
      <select name="destination">
        
        <option value="PREVIEW">To preview this page</option>
        <option value="ELEMENTS">To the elements tree for this page</option>
        <option value="SITEMAP">Back to the site map</option>
        <option value="EDIT">To edit this page</option>
        
      </select>
    </p>
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="&lt;&lt; Back" onclick="window.location=sm_domain+sm_section+'/addPage?stage=2'" />
        <input type="submit" value="Finish" />
      </div>
    </div>
    
  </form>