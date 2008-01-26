<?php /* Smarty version 2.6.18, created on 2007-12-18 20:03:53
         compiled from /var/www/html/Presentation/Assets/last_three_press_releases.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'repeat', '/var/www/html/Presentation/Assets/last_three_press_releases.tpl', 2, false),array('function', 'property', '/var/www/html/Presentation/Assets/last_three_press_releases.tpl', 5, false),array('function', 'link', '/var/www/html/Presentation/Assets/last_three_press_releases.tpl', 11, false),array('modifier', 'summary', '/var/www/html/Presentation/Assets/last_three_press_releases.tpl', 10, false),)), $this); ?>
<h4>Press Releases</h4>
<?php $this->_tag_stack[] = array('repeat', array('from' => 'press_releases_main_list','limit' => 3)); $_block_repeat=true;smarty_block_repeat($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>

  <?php ob_start(); ?>metapage:name=press-release:id=<?php echo $this->_tpl_vars['repeated_item']['id']; ?>
<?php $this->_smarty_vars['capture']['link_contents'] = ob_get_contents();  $this->assign('link_contents', ob_get_contents());ob_end_clean(); ?>
  <?php ob_start(); ?><?php echo smarty_function_property(array('name' => 'body_text','context' => 'repeat'), $this);?>
<?php $this->_smarty_vars['capture']['body_text'] = ob_get_contents();  $this->assign('body_text', ob_get_contents());ob_end_clean(); ?>

  <p>
    <strong><?php echo $this->_tpl_vars['repeated_item']['name']; ?>
</strong><br />
    <?php echo smarty_function_property(array('name' => 'date_published','context' => 'repeat'), $this);?>
<br />
    <?php echo ((is_array($_tmp=$this->_tpl_vars['body_text'])) ? $this->_run_mod_handler('summary', true, $_tmp, 80) : smarty_modifier_summary($_tmp, 80)); ?>

    <br /><?php echo smarty_function_link(array('to' => $this->_tpl_vars['link_contents'],'with' => 'Read More'), $this);?>

  </p>

<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_repeat($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>