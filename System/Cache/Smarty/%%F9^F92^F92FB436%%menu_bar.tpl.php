<?php /* Smarty version 2.6.18, created on 2007-11-25 20:47:47
         compiled from /var/vsc/clients/claritycapital/smartest/Presentation/Assets/menu_bar.tpl */ ?>
<div id="main-nav">
  <ul>
    <li><a href="/" <?php if ($this->_tpl_vars['this']['fields']['category'] == 'home'): ?> class="selected"<?php endif; ?> onmouseover="hideAllMenus()">Home</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(2)"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'about'): ?> class="selected"<?php endif; ?>>About Us</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(3)"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'operations'): ?> class="selected"<?php endif; ?>>Operations</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(4)"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'investors_media'): ?> class="selected"<?php endif; ?>>Investors &amp; Media</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(5)"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'corporate_responsibility'): ?> class="selected"<?php endif; ?>>Corporate Responsibility</a></li>
  </ul>
</div>