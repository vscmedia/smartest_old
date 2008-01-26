<?php /* Smarty version 2.6.18, created on 2007-12-04 21:40:04
         compiled from /var/www/html/Presentation/Assets/saponin_two_column.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'field', '/var/www/html/Presentation/Assets/saponin_two_column.tpl', 26, false),array('function', 'template', '/var/www/html/Presentation/Assets/saponin_two_column.tpl', 30, false),array('function', 'breadcrumbs', '/var/www/html/Presentation/Assets/saponin_two_column.tpl', 53, false),array('function', 'container', '/var/www/html/Presentation/Assets/saponin_two_column.tpl', 56, false),)), $this); ?>
    <table cellspacing="0" border="0" cellpadding="0" id="three-column-cheat">
      <tr>
        <td id="left-column" valign="top">
          
          <div class="menu-box">
            <h4>Custom Search</h4>
            <ul class="custom-search-links">
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/institutional-investor.html">Institutional Investor</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/individual-investor.html">Individual Investor</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/family-office.html">Family Office</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/journalists.html">Journalist</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/government.html">Government</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/analyst.html">Analyst</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/brokerage-house.html">Brokerage House</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/ngos.html">NGO</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/pharmaceutical.html">Pharmaceutical</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/cosmetics.html">Cosmetics</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/health-care.html">Health Care</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/animal-health-care.html">Animal Health Care</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/scientist.html">Scientist</a></li>
              <li><a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/purchasing-agent.html">Purchasing Agent</a></li>
            </ul>
          </div>
          
          <div class="menu-box-bottom">
                        <?php ob_start(); ?><?php echo smarty_function_field(array('name' => 'lower_block_content'), $this);?>
<?php $this->_smarty_vars['capture']['lower_block_content'] = ob_get_contents();  $this->assign('lower_block_content', ob_get_contents());ob_end_clean(); ?>

            <?php if ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'custom_search'): ?>

            <?php echo smarty_function_template(array('name' => "saponin_custom_search.tpl"), $this);?>


            <?php elseif ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'news_articles'): ?>

            <?php echo smarty_function_template(array('name' => "left_column_news.tpl"), $this);?>


            <?php elseif ($this->_tpl_vars['this']['fields']['lower_block_content'] == 'presentations'): ?>

            <?php echo smarty_function_template(array('name' => "last_three_presentations.tpl"), $this);?>


            <?php else: ?>

            <?php echo smarty_function_template(array('name' => "last_three_press_releases.tpl"), $this);?>


            <?php endif; ?>
          </div>
        </td>
    
    <td id="central-column" valign="top" onmouseover="hideAllMenus()">
      
      <div id="central-column-content">
        <?php if ($this->_tpl_vars['this']['fields']['category'] != 'home'): ?>
          <?php if ($this->_tpl_vars['this']['principal_item']['name']): ?><h3><?php echo $this->_tpl_vars['this']['principal_item']['name']; ?>
</h3><?php else: ?><h3><?php echo $this->_tpl_vars['this']['page']['title']; ?>
</h3><?php endif; ?>
          <div id="breadcrumbs"><?php echo smarty_function_breadcrumbs(array(), $this);?>
</div>
        <?php endif; ?>

        <?php echo smarty_function_container(array('name' => 'central_column_dynamic_content'), $this);?>

        <?php echo smarty_function_template(array('name' => "saponin_footer.tpl"), $this);?>
        

      </div>
    </td>
    
  </tr>
</table>