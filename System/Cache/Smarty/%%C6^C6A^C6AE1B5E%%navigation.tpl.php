<?php /* Smarty version 2.6.18, created on 2007-11-25 20:38:11
         compiled from /var/vsc/clients/claritycapital/smartest/System/Presentation/Backend/navigation.tpl */ ?>
<div id="admin-menu">
  <ul>
    <li<?php if ($this->_tpl_vars['section'] == 'desktop' && $this->_tpl_vars['method'] != 'editSite'): ?> class="on"<?php else: ?> class="off"<?php endif; ?>><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest' >Desktop</a></li>
    <?php if ($this->_tpl_vars['show_left_nav_options']): ?>
    <li<?php if ($this->_tpl_vars['section'] == 'websitemanager'): ?> class="on"<?php else: ?> class="off"<?php endif; ?>><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest/pages' >Pages</a></li>
    <li<?php if ($this->_tpl_vars['section'] == 'datamanager' || $this->_tpl_vars['section'] == 'metadata'): ?> class="on"<?php else: ?> class="off"<?php endif; ?>><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest/data'>Data</a></li>
    <li<?php if ($this->_tpl_vars['section'] == 'assets'): ?> class="on"<?php else: ?> class="off"<?php endif; ?>><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest/assets'>Files</a></li>
	  <li<?php if ($this->_tpl_vars['section'] == 'templates'): ?> class="on"<?php else: ?> class="off"<?php endif; ?>><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest/templates'>Templates</a></li>
	  <li<?php if ($this->_tpl_vars['section'] == 'desktop' && $this->_tpl_vars['method'] == 'editSite'): ?> class="on"<?php else: ?> class="off"<?php endif; ?>><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest/settings'>Settings</a></li>
	  	  <?php endif; ?>
	  <li class="off"><a href='<?php echo $this->_tpl_vars['domain']; ?>
smartest/logout'>Sign Out</a></li>
  </ul>
</div>
