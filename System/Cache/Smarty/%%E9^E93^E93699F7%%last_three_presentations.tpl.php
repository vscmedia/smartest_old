<?php /* Smarty version 2.6.18, created on 2007-11-26 02:56:15
         compiled from /var/vsc/clients/claritycapital/smartest/Presentation/Assets/last_three_presentations.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'repeat', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/last_three_presentations.tpl', 3, false),array('function', 'property', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/last_three_presentations.tpl', 5, false),array('function', 'link', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/last_three_presentations.tpl', 12, false),)), $this); ?>
<h4>Presentations</h4>

<?php $this->_tag_stack[] = array('repeat', array('from' => 'recent_presentations','limit' => 3)); $_block_repeat=true;smarty_block_repeat($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
  
  <?php ob_start(); ?><?php echo smarty_function_property(array('name' => 'presentation_file','context' => 'repeat'), $this);?>
<?php $this->_smarty_vars['capture']['download_asset_id'] = ob_get_contents();  $this->assign('download_asset_id', ob_get_contents());ob_end_clean(); ?>
  <?php ob_start(); ?>download:id=<?php echo $this->_tpl_vars['download_asset_id']; ?>
<?php $this->_smarty_vars['capture']['link_contents'] = ob_get_contents();  $this->assign('link_contents', ob_get_contents());ob_end_clean(); ?>

  <p>
    <strong><?php echo $this->_tpl_vars['repeated_item']['name']; ?>
</strong><br />
    <?php echo smarty_function_property(array('name' => 'presentation_date','context' => 'repeat'), $this);?>
<br />
    <?php echo smarty_function_property(array('name' => 'short_description','context' => 'repeat'), $this);?>

    <br /><?php echo smarty_function_link(array('to' => $this->_tpl_vars['link_contents'],'with' => 'Download'), $this);?>

  </p>

<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_repeat($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>