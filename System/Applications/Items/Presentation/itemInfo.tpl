<div id="work-area">
  <h3>{$item._model.name} Info</h3>
  <div class="edit-form-row">
    <div class="form-section-label">Title</div>
    {$item.name}
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Name</div>
    {$item.slug}
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Type</div>
    {$item._model.name}
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Author(s)</div>
    {$byline}
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Workflow Status</div>
    {$item._workflow_status}
  </div>
  {if $has_page}
  <div class="edit-form-row">
    <div class="form-section-label">Link code</div>
    &lt;?sm:link to="metapage:id={$page.id}:id={$item.id}"?&gt;
  </div>
  {/if}
</div>

<div id="actions-area">

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$item._model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" />&nbsp;Try to edit it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTodoItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Assign To-do</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$item._model.name} Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" class="right-nav-link">Back to {$item._model.plural_name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$item._model.id}';" class="right-nav-link">New {$item._model.name}</a></li>
  </ul>
  
</div>
