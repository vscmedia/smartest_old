<?php /* Smarty version 2.6.18, created on 2007-12-02 15:23:31
         compiled from /var/www/html/System/Applications/Pages/Presentation/defineContainer.tpl */ ?>
<div id="work-area">
  
  <h3>Define Container</h3>
  <div class="instruction">Please choose a template to use in this container.</div>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page']['id']; ?>
" />
    <input type="hidden" name="container_id" value="<?php echo $this->_tpl_vars['container']['id']; ?>
" />
    <input type="hidden" name="asset_id" id="item_id_input" value="" />
  </form>
  
  <div id="options-view-chooser">
  <a href="javascript:nothing()" onclick="setView('list', 'options_grid')">List</a> /
  <a href="javascript:nothing()" onclick="setView('grid', 'options_grid')">Icons</a>
  </div>

  <ul class="options-grid" style="margin-top:0px" id="options_grid">
  <?php $_from = $this->_tpl_vars['templates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['asset']):
?>
  <li>
      <a href="javascript:nothing()" class="option" id="item_<?php echo $this->_tpl_vars['asset']['id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['asset']['id']; ?>
');" >
      <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/blank_page.png" /><?php echo $this->_tpl_vars['asset']['stringid']; ?>
</a>
  </li>
  <?php endforeach; endif; unset($_from); ?>
  </ul>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected Asset</b></li>
    <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'updateContainerDefinition\');}'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" alt=""> Use This Asset</a></li>
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/pageAssets?page_id=<?php echo $this->_tpl_vars['page']['id']; ?>
'" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/cross.png" border="0" alt=""> Cancel</a></li>
  </ul>
  
</div>