<?php /* Smarty version 2.6.18, created on 2007-12-04 09:58:21
         compiled from /var/www/html/Presentation/Assets/saponin_three_columns.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'field', '/var/www/html/Presentation/Assets/saponin_three_columns.tpl', 12, false),array('function', 'template', '/var/www/html/Presentation/Assets/saponin_three_columns.tpl', 16, false),array('function', 'breadcrumbs', '/var/www/html/Presentation/Assets/saponin_three_columns.tpl', 39, false),array('function', 'container', '/var/www/html/Presentation/Assets/saponin_three_columns.tpl', 42, false),array('function', 'placeholder', '/var/www/html/Presentation/Assets/saponin_three_columns.tpl', 51, false),)), $this); ?>
    <table cellspacing="0" border="0" cellpadding="0" id="three-column-cheat">
      <tr>
        <td id="left-column" valign="top">
          
          <div class="menu-box">
            <p>
              “Saponin is commercializing the Prairie Carnation™, a proprietary plant species with healthcare, cosmetic, vaccine and pharmaceutical applications”
            </p>
          </div>
          
          <div class="menu-box-bottom">
            <?php ob_start(); ?><?php echo smarty_function_field(array('name' => 'lower_block_content'), $this);?>
<?php $this->_smarty_vars['capture']['lower_block_content'] = ob_get_contents();  $this->assign('lower_block_content', ob_get_contents());ob_end_clean(); ?>

            <?php if ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'press_releases'): ?>

            <?php echo smarty_function_template(array('name' => "last_three_press_releases.tpl"), $this);?>


            <?php elseif ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'news_articles'): ?>

            <?php echo smarty_function_template(array('name' => "left_column_news.tpl"), $this);?>


            <?php elseif ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'presentations'): ?>

            <?php echo smarty_function_template(array('name' => "last_three_presentations.tpl"), $this);?>


            <?php else: ?>

            <?php echo smarty_function_template(array('name' => "saponin_custom_search.tpl"), $this);?>


            <?php endif; ?>
          </div>
        </td>
    
    <td id="central-column" valign="top" onmouseover="hideAllMenus()">
      
      <div id="central-column-content">
        <?php if ($this->_tpl_vars['this']['fields']['category'] != 'home'): ?>
          <h3><?php echo $this->_tpl_vars['this']['page']['title']; ?>
</h3>
          <div id="breadcrumbs"><?php echo smarty_function_breadcrumbs(array(), $this);?>
</div>
        <?php endif; ?>

        <?php echo smarty_function_container(array('name' => 'central_column_dynamic_content'), $this);?>

        
        <?php echo smarty_function_template(array('name' => "saponin_footer.tpl"), $this);?>


      </div>
    </td>
    
    <td id="right-column" valign="top">
      <div id="right-column-content">
        <?php echo smarty_function_placeholder(array('name' => 'right_col_top_image'), $this);?>

        <?php echo smarty_function_placeholder(array('name' => 'right_col_middle_image'), $this);?>

        <?php echo smarty_function_placeholder(array('name' => 'right_col_bottom_image'), $this);?>

      </div>
    </td>

    
  </tr>
</table>