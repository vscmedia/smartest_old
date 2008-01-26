<?php /* Smarty version 2.6.18, created on 2007-12-14 09:24:18
         compiled from /var/www/html/Presentation/Assets/central_column_dynamic_content.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'field', '/var/www/html/Presentation/Assets/central_column_dynamic_content.tpl', 9, false),array('function', 'template', '/var/www/html/Presentation/Assets/central_column_dynamic_content.tpl', 13, false),array('function', 'breadcrumbs', '/var/www/html/Presentation/Assets/central_column_dynamic_content.tpl', 61, false),array('function', 'container', '/var/www/html/Presentation/Assets/central_column_dynamic_content.tpl', 64, false),array('function', 'link', '/var/www/html/Presentation/Assets/central_column_dynamic_content.tpl', 70, false),array('function', 'placeholder', '/var/www/html/Presentation/Assets/central_column_dynamic_content.tpl', 96, false),)), $this); ?>
<table cellspacing="13" border="0" cellpadding="0">
  <tr>
    
    <td id="left-column" valign="top">
            
      <div class="menu-box">
        
        <?php ob_start(); ?><?php echo smarty_function_field(array('name' => 'upper_block_content'), $this);?>
<?php $this->_smarty_vars['capture']['upper_block_content'] = ob_get_contents();  $this->assign('upper_block_content', ob_get_contents());ob_end_clean(); ?>
        
        <?php if ($this->_tpl_vars['this']['fields']['upper_block_content'] == 'custom_search'): ?>
        
        <?php echo smarty_function_template(array('name' => "savannah_custom_search.tpl"), $this);?>

        
        <?php elseif ($this->_tpl_vars['this']['fields']['upper_block_content'] == 'news_articles'): ?>
        
        <?php echo smarty_function_template(array('name' => "left_column_news.tpl"), $this);?>

        
        <?php elseif ($this->_tpl_vars['this']['fields']['upper_block_content'] == 'presentations'): ?>
        
        <?php echo smarty_function_template(array('name' => "last_three_presentations.tpl"), $this);?>

        
        <?php else: ?>
        
        <?php echo smarty_function_template(array('name' => "last_three_press_releases.tpl"), $this);?>

        
        <?php endif; ?>
      </div>
      
            
      <div class="menu-box-bottom">
        
        <?php ob_start(); ?><?php echo smarty_function_field(array('name' => 'lower_block_content'), $this);?>
<?php $this->_smarty_vars['capture']['lower_block_content'] = ob_get_contents();  $this->assign('lower_block_content', ob_get_contents());ob_end_clean(); ?>
        
        <?php if ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'press_releases'): ?>
        
        <?php echo smarty_function_template(array('name' => "last_three_press_releases.tpl"), $this);?>

        
        <?php elseif ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'custom_search'): ?>
        
        <?php echo smarty_function_template(array('name' => "custom_search.tpl"), $this);?>

        
        <?php elseif ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'presentations'): ?>
        
        <?php echo smarty_function_template(array('name' => "last_three_presentations.tpl"), $this);?>

        
        <?php else: ?>
        
        <?php echo smarty_function_template(array('name' => "savannah_custom_search.tpl"), $this);?>

        
        <?php endif; ?>
      </div>
    </td>
    
    <td id="central-column" valign="top" onmouseover="hideAllMenus()">
      <div id="central-column-content">
        
        <?php if ($this->_tpl_vars['this']['fields']['category'] != 'home'): ?>
          <h3><?php echo $this->_tpl_vars['this']['page']['title']; ?>
</h3>
          <div id="breadcrumbs">You are in: <?php echo smarty_function_breadcrumbs(array(), $this);?>
</div>
        <?php endif; ?>
        
        <?php echo smarty_function_container(array('name' => 'central_column_dynamic_content'), $this);?>

        
        <div id="footer-container">
        
          <div id="footer">
          
            <?php echo smarty_function_link(array('to' => "page:site-map",'with' => 'Site Map','class' => "footer-nav-link"), $this);?>
 <span style="color:#000">|</span>
            <?php echo smarty_function_link(array('to' => "page:contact-us",'with' => 'Contact Us','class' => "footer-nav-link"), $this);?>
 <span style="color:#000">|</span>
            <?php echo smarty_function_link(array('to' => "page:aim-compliance",'with' => 'AIM Compliance','class' => "footer-nav-link"), $this);?>
 <span style="color:#000">|</span>
            <?php echo smarty_function_link(array('to' => "page:terms-conditions",'with' => "Terms &amp; Conditions",'class' => "footer-nav-link"), $this);?>

          
            <table id="clarity-parent" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td valign="middle">Savannah Diamonds is a</td>
                <td><a href="http://www.claritycapital.com/" target="_blank"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/clarity_logo_small.gif" alt="Clarity Capital" /></a></td>
                <td valign="middle">company.</td>
              </tr>
            </table>
          
            <div id="copyright">All Content Copyright &copy; Savannah Diamonds Limited 2007<br />
              Website by <a href="http://www.smoothmedia.com/" target="_blank">Smoothmedia</a> Inc.
            </div>
          
          </div>
        
        </div>
        
      </div>
    </td>
    
    <td id="right-column" valign="top">
      <div id="right-column-content">
        <?php echo smarty_function_placeholder(array('name' => 'right_col_top_image'), $this);?>

        <?php echo smarty_function_placeholder(array('name' => 'right_col_bottom_image'), $this);?>

        <?php echo smarty_function_placeholder(array('name' => 'right_col_extra_image'), $this);?>

      </div>
    </td>
    
  </tr>
</table>