<?php /* Smarty version 2.6.18, created on 2007-12-03 12:55:23
         compiled from /var/www/html/System/Applications/Pages/Presentation/editPage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/www/html/System/Applications/Pages/Presentation/editPage.tpl', 5, false),)), $this); ?>
<div id="work-area">

<?php if ($this->_tpl_vars['allow_edit']): ?>

<?php echo smarty_function_load_interface(array('file' => "editPage.tabs.tpl"), $this);?>


<?php echo smarty_function_load_interface(array('file' => "editPage.form.tpl"), $this);?>


<?php else: ?>

<h3>Edit Page</h3>

<div class="instruction">You can't currently edit this page</div>

<?php endif; ?>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editSite?site_id=<?php echo $this->_tpl_vars['pageInfo']['site_id']; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Edit Site Parameters</a></li>
    <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/preview?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_red.png" border="0" alt=""> Preview this page</a></li>
    <?php if ($this->_tpl_vars['allow_release']): ?><li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/releasePage?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/lock_open.png" border="0" alt=""> Release this page</a></li><?php endif; ?>
    <?php if ($this->_tpl_vars['allow_edit']): ?><li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/closeCurrentPage" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li><?php endif; ?>
  </ul>
</div>