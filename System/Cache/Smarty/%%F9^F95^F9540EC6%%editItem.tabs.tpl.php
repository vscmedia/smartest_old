<?php /* Smarty version 2.6.18, created on 2007-11-28 06:28:57
         compiled from /var/www/html/System/Applications/Items/Presentation/editItem.tabs.tpl */ ?>
<ul class="tabset">
    <li<?php if ($this->_tpl_vars['method'] == 'editItem'): ?> class="current"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editItem?item_id=<?php echo $_GET['item_id']; ?>
">Item Properties</a></li>
    <li<?php if ($this->_tpl_vars['method'] == 'itemTags'): ?> class="current"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/itemTags?item_id=<?php echo $_GET['item_id']; ?>
">Tags</a></li>
</ul>
