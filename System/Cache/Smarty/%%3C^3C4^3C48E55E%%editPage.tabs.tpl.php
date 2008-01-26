<?php /* Smarty version 2.6.18, created on 2007-11-26 14:06:51
         compiled from /var/www/html/System/Applications/Pages/Presentation/editPage.tabs.tpl */ ?>
<ul class="tabset">
    <li<?php if ($this->_tpl_vars['method'] == 'editPage'): ?> class="current"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editPage?page_id=<?php echo $_GET['page_id']; ?>
">Page Properties</a></li>
    <li<?php if ($this->_tpl_vars['method'] == 'pageAssets'): ?> class="current"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/pageAssets?page_id=<?php echo $_GET['page_id']; ?>
">Element Structure</a></li>
    <li<?php if ($this->_tpl_vars['method'] == 'pageTags'): ?> class="current"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/pageTags?page_id=<?php echo $_GET['page_id']; ?>
">Tags</a></li>
    <li<?php if ($this->_tpl_vars['method'] == 'preview'): ?> class="current"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/preview?page_id=<?php echo $_GET['page_id']; ?>
">Preview</a></li>
</ul>


<?php if (( $this->_tpl_vars['content']['pageInfo']['page_type'] == 'ITEMCLASS' || $this->_tpl_vars['content']['page']['page_type'] == 'ITEMCLASS' ) && $_GET['item_id']): ?>[<a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getPageLists?page_id=<?php echo $_GET['page_id']; ?>
">Item Data</a>]<?php endif; ?>