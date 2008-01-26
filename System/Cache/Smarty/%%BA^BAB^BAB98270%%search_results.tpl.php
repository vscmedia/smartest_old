<?php /* Smarty version 2.6.18, created on 2007-12-17 17:09:04
         compiled from /var/www/html/Presentation/Assets/search_results.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '/var/www/html/Presentation/Assets/search_results.tpl', 15, false),array('function', 'link', '/var/www/html/Presentation/Assets/search_results.tpl', 17, false),)), $this); ?>
<?php if (empty ( $this->_tpl_vars['this']['search_results'] )): ?>
  
  <p>Your search for "<?php echo $this->_tpl_vars['this']['page']['query']; ?>
" did not return any results</p>
  
<?php else: ?>

<?php $_from = $this->_tpl_vars['this']['search_results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object']):
?>

<div style="margin:0 0 10px 0">
  
  <h4><?php echo $this->_tpl_vars['object']['title']; ?>
</h4>
  
  <div class="list-item">
    <p>
      <?php echo ((is_array($_tmp=$this->_tpl_vars['object']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%A %B %e, %Y") : smarty_modifier_date_format($_tmp, "%A %B %e, %Y")); ?>
<br />
      <?php echo $this->_tpl_vars['object']['description']; ?>
<br />
      <?php echo smarty_function_link(array('to' => $this->_tpl_vars['object']['url'],'with' => 'Read More'), $this);?>

    </p>
  </div>
  
</div>

<?php endforeach; endif; unset($_from); ?>

<?php endif; ?>