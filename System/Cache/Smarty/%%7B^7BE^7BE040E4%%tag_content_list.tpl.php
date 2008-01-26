<?php /* Smarty version 2.6.18, created on 2007-11-25 22:00:39
         compiled from /var/vsc/clients/claritycapital/smartest/Presentation/Assets/tag_content_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/tag_content_list.tpl', 15, false),array('function', 'link', '/var/vsc/clients/claritycapital/smartest/Presentation/Assets/tag_content_list.tpl', 17, false),)), $this); ?>
<?php if (empty ( $this->_tpl_vars['this']['tagged_objects'] )): ?>
  
  <p>There are no items tagged with "<?php echo $this->_tpl_vars['this']['page']['tag']['label']; ?>
"</p>
  
<?php else: ?>

<?php $_from = $this->_tpl_vars['this']['tagged_objects']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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

<p><?php if ($this->_tpl_vars['this']['page']['is_tag_page']): ?><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/rss.png" />&nbsp;<a href="<?php echo $this->_tpl_vars['domain']; ?>
tags/<?php echo $this->_tpl_vars['this']['page']['tag']['name']; ?>
/feed">Feed for tag "<?php echo $this->_tpl_vars['this']['page']['tag']['label']; ?>
"</a><?php endif; ?></p>

<?php endif; ?>