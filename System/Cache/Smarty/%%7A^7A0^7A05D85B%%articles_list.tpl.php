<?php /* Smarty version 2.6.18, created on 2007-11-26 03:13:18
         compiled from /var/vsc/clients/claritycapital/smartest/Presentation/Assets/articles_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'repeat', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/articles_list.tpl', 3, false),array('function', 'property', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/articles_list.tpl', 11, false),array('function', 'link', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/articles_list.tpl', 14, false),array('modifier', 'summary', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/articles_list.tpl', 13, false),)), $this); ?>
<div id="items-list">

<?php $this->_tag_stack[] = array('repeat', array('from' => 'recent_articles','limit' => '10')); $_block_repeat=true;smarty_block_repeat($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>

<?php ob_start(); ?>metapage:name=article:id=<?php echo $this->_tpl_vars['repeated_item']['id']; ?>
<?php $this->_smarty_vars['capture']['link_contents'] = ob_get_contents();  $this->assign('link_contents', ob_get_contents());ob_end_clean(); ?>

<div style="margin:0 0 10px 0">
  
  <h4><?php echo $this->_tpl_vars['repeated_item']['name']; ?>
</h4>
  
  <div class="list-item"><?php echo smarty_function_property(array('name' => 'published_at','context' => 'repeat'), $this);?>
<br />
    <?php ob_start(); ?><?php echo smarty_function_property(array('name' => 'article_text','context' => 'repeat'), $this);?>
<?php $this->_smarty_vars['capture']['article_text'] = ob_get_contents();  $this->assign('article_text', ob_get_contents());ob_end_clean(); ?>
    <p><?php echo ((is_array($_tmp=$this->_tpl_vars['article_text'])) ? $this->_run_mod_handler('summary', true, $_tmp) : smarty_modifier_summary($_tmp)); ?>
</p>
    <br /><?php echo smarty_function_link(array('to' => $this->_tpl_vars['link_contents'],'with' => 'Read More'), $this);?>

  </div>
  
</div>

<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_repeat($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

</div>