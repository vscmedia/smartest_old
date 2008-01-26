<?php /* Smarty version 2.6.18, created on 2007-12-03 19:20:09
         compiled from /var/www/html/Presentation/Assets/saponin_menu_bar.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', '/var/www/html/Presentation/Assets/saponin_menu_bar.tpl', 5, false),)), $this); ?>
<div id="main-nav">

            <ul>
              <li><a href="/" onmouseover="hideAllMenus()" style="width:70px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'home'): ?> class="selected"<?php endif; ?>>Home</a></li>
              <li><a href="<?php echo smarty_function_url(array('to' => "page:about-us"), $this);?>
" onmouseover="mouseOverMenu(2)" style="width:85px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'about'): ?> class="selected"<?php endif; ?>>About Us</a></li>
              <li><a href="<?php echo smarty_function_url(array('to' => "page:technology"), $this);?>
" onmouseover="mouseOverMenu(3)" style="width:100px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'technology'): ?> class="selected"<?php endif; ?>>Technology</a></li>
              <li><a href="<?php echo smarty_function_url(array('to' => "page:product-applications"), $this);?>
" onmouseover="mouseOverMenu(4)" style="width:161px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'applications'): ?> class="selected"<?php endif; ?>>Product Applications</a></li>
              <li><a href="<?php echo smarty_function_url(array('to' => "page:products"), $this);?>
" onmouseover="mouseOverMenu(5)" style="width:145px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'sales_licensing'): ?> class="selected"<?php endif; ?>>Sales &amp; Licensing</a></li>
              <li><a href="<?php echo smarty_function_url(array('to' => "page:investors-media"), $this);?>
" onmouseover="mouseOverMenu(6)" style="width:150px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'investors_media'): ?> class="selected"<?php endif; ?>>Investors &amp; Media</a></li>
              <li><a href="<?php echo smarty_function_url(array('to' => "page:corporate-responsibility"), $this);?>
" onmouseover="mouseOverMenu(7)" style="width:187px"<?php if ($this->_tpl_vars['this']['fields']['category'] == 'corporate_responsibility'): ?> class="selected"<?php endif; ?>>Corporate Responsibility</a></li>
            </ul>

          </div>