<div id="work-area">
  
  <h3>Add to-do to this item</h3>
  
  <form action="{$domain}{$section}/insertTodoItem" method="post">
    
    <input type="hidden" name="item_id" value="{$item.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Who is this for?</div>
      <select name="todoitem_receiving_user_id">
        <option value="{$user.id}">Myself</option>
        {foreach from=$users item="other_user"}
        {if $other_user.id != $user.id}<option value="{$other_user.id}">{$other_user.full_name}</option>{/if}
        {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Task type</div>
      <select name="todoitem_type">
        <option value="SM_TODOITEMTYPE_EDIT_ITEM">Edit this item</option>
        <option value="SM_TODOITEMTYPE_APPROVE_ITEM">Approve this item</option>
        <option value="SM_TODOITEMTYPE_PUBLISH_ITEM">Publish this item</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Description of the task</div>
      <textarea name="todoitem_description" style="width:400px;height:80px"></textarea>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Priority</div>
      <select name="todoitem_priority">
        <option value="4">Urgent</option>
        <option value="3">High</option>
        <option value="2" selected="selected">Normal</option>
        <option value="1">Low</option>
        <option value="0">Backburner</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Size</div>
      <select name="todoitem_size">
        <option value="4">Huge</option>
        <option value="3">Big</option>
        <option value="2" selected="selected">Normal</option>
        <option value="1">Small</option>
        <option value="0">Niggling</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Assign" />
        <input type="button" value="Cancel" onclick="cancelForm()" />
      </div>
    </div>
    
  </form>
  
</div>
