<?php /* Smarty version 2.6.18, created on 2007-12-02 12:22:26
         compiled from /var/www/html/System/Applications/Pages/Presentation/addPage.start.tpl */ ?>
  <h3>Create a New Page</h3>
  
  <div class="instruction">Step 1 of 3: Choose which type of page you're going to make</div>
  <form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addPage" method="post">
    
    <input type="hidden" name="page_parent" value="<?php echo $this->_tpl_vars['page_parent']; ?>
" />
    <input type="hidden" name="stage" value="2">
    
    <select name="page_type">
      <option value="NORMAL" selected="selected">Regular Web-page</option>
      <option value="ITEMCLASS">Object Meta-page</option>
          </select>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Next &gt;&gt;" />
      </div>
    </div>
    
  </form>