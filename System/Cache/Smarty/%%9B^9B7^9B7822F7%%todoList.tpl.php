<?php /* Smarty version 2.6.18, created on 2007-11-28 10:54:37
         compiled from /var/www/html/System/Applications/Desktop/Presentation/todoList.tpl */ ?>
<div id="work-area">

  <h3>Your To-do List</h3>
  
  <div class="instruction">Assigned by you (<?php echo $this->_tpl_vars['num_self_assigned_tasks']; ?>
)</div>

  <div class="instruction">Assigned by others (<?php echo $this->_tpl_vars['num_assigned_tasks']; ?>
)</div>

  <div class="instruction">Duties (<?php echo $this->_tpl_vars['num_duty_items']; ?>
)</div>

  <ul class="todo-item">
    <?php $_from = $this->_tpl_vars['locked_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
    <li>Finish editing and release page: <?php echo $this->_tpl_vars['page']['title']; ?>
 [<a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/openPage?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
&amp;from=todoList">edit</a>] [<a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/releasePage?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
&amp;from=todoList">release</a>]</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['locked_items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <li>Finish editing and release item: <?php echo $this->_tpl_vars['item']['name']; ?>
 [<a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager/openItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
&amp;from=todoList">edit</a>] [<a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager/releaseItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
&amp;from=todoList">release</a>]</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['pages_awaiting_approval']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
    <li>Approve changes to page: <?php echo $this->_tpl_vars['page']['title']; ?>
 [<a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/preview?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
&amp;from=todoList">go</a>]</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['items_awaiting_approval']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <li>Approve changes to item: <?php echo $this->_tpl_vars['item']['name']; ?>
 [<a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager/editItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
&amp;from=todoList">go</a>]</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['pages_awaiting_publishing']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
    <li>Publish changes to page: <?php echo $this->_tpl_vars['page']['title']; ?>
 [<a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/publishPageConfirm?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
&amp;from=todoList">go</a>]</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['items_awaiting_publishing']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <li>Publish changes to item: <?php echo $this->_tpl_vars['item']['name']; ?>
 [<a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager/publishItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
&amp;from=todoList">go</a>]</li>
    <?php endforeach; endif; unset($_from); ?>
  </ul>

</div>

<div id="actions-area">
  
  <ul class="invisible-actions-list" id="selfassigned-specific-actions" style="display:none">
    <li><strong>Selected task</strong></li>
    <li>Delete</li>
    <li>Mark as Completed</li>
  </ul>
  
  <ul class="invisible-actions-list" id="assigned-specific-actions" style="display:none">
    <li><strong>Selected task</strong></li>
    <li>Mark as Completed</li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><strong>Todo List Options</strong></li>
    <li>Add a new task</li>
  </ul>
  
</div>