<div id="work-area">

  <h3>Your To-do List</h3>
  
  <div class="instruction">Assigned by you ({$num_self_assigned_tasks})</div>

  <div class="instruction">Assigned by others ({$num_assigned_tasks})</div>

  <div class="instruction">Duties ({$num_duty_items})</div>

  <ul class="todo-item">
    {foreach from=$locked_pages item="page"}
    <li>Finish editing and release page: {$page.title} [<a href="{$domain}websitemanager/openPage?page_id={$page.webid}&amp;from=todoList">edit</a>] [<a href="{$domain}websitemanager/releasePage?page_id={$page.webid}&amp;from=todoList">release</a>]</li>
    {/foreach}
    {foreach from=$locked_items item="item"}
    <li>Finish editing and release item: {$item.name} [<a href="{$domain}datamanager/openItem?item_id={$item.id}&amp;from=todoList">edit</a>] [<a href="{$domain}datamanager/releaseItem?item_id={$item.id}&amp;from=todoList">release</a>]</li>
    {/foreach}
    {foreach from=$pages_awaiting_approval item="page"}
    <li>Approve changes to page: {$page.title} [<a href="{$domain}websitemanager/preview?page_id={$page.webid}&amp;from=todoList">go</a>]</li>
    {/foreach}
    {foreach from=$items_awaiting_approval item="item"}
    <li>Approve changes to item: {$item.name} [<a href="{$domain}datamanager/editItem?item_id={$item.id}&amp;from=todoList">go</a>]</li>
    {/foreach}
    {foreach from=$pages_awaiting_publishing item="page"}
    <li>Publish changes to page: {$page.title} [<a href="{$domain}websitemanager/publishPageConfirm?page_id={$page.webid}&amp;from=todoList">go</a>]</li>
    {/foreach}
    {foreach from=$items_awaiting_publishing item="item"}
    <li>Publish changes to item: {$item.name} [<a href="{$domain}datamanager/publishItem?item_id={$item.id}&amp;from=todoList">go</a>]</li>
    {/foreach}
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