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
    <div class="form-section-label">Model</div>
    {$item._model.name}
  </div>
  <div id="sets" class="special-box">
       Sets: {if count($sets)}{foreach from=$sets item="set"}<a href="{$domain}sets/previewSet?set_id={$set.id}">{$set.label}</a> (<a href="{$domain}sets/transferSingleItem?item_id={$item.id}&amp;set_id={$set.id}&amp;transferAction=remove">remove</a>), {/foreach}{else}<em style="color:#666">None</em>{/if}
   {if count($possible_sets)}
           <div>
             <form action="{$domain}sets/transferSingleItem" method="post">
               <input type="hidden" name="item_id" value="{$item.id}" />
               <input type="hidden" name="transferAction" value="add" />
               Add this item to set:
               <select name="set_id">
   {foreach from=$possible_sets item="possible_set"}
                 <option value="{$possible_set.id}">{$possible_set.label}</option>
   {/foreach}
               </select>
               <input type="submit" value="Go" />
             </form>
           </div>
   {/if}
     </div>
  {if $has_page}
  <div class="edit-form-row">
    <div class="form-section-label">Link code</div>
    <code>[[{$item._model.name|varname}:{$item.slug}]]</code> <em>OR</em> <code>&lt;?sm:link to="metapage:id={$page.id}:id={$item.id}":?&gt;</code>
  </div>
  {/if}
  {if $item.created > 0}
  <div class="edit-form-row">
    <div class="form-section-label">Created:</div>
    {$item.created|date_format:"%A %B %e, %Y, %l:%M%p"}
  </div>
  {/if}
  {if $item.modified > 0}
  <div class="edit-form-row">
    <div class="form-section-label">Modified:</div>
    {$item.modified|date_format:"%A %B %e, %Y, %l:%M%p"}
  </div>
  {/if}
  <div class="edit-form-row">
    <div class="form-section-label">Author(s)</div>
    {$byline}
  </div>
  <div class="edit-form-row">
      <div class="form-section-label">Tags</div>
      {if $item.tags._empty}<em>No tags selected</em>{else}{$item.tags}{/if}
    </div>
  <div class="edit-form-row">
    <div class="form-section-label">Workflow Status</div>
    {$item._workflow_status}
  </div>
  
  </div>

<div id="actions-area">

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$item._model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" />&nbsp;Try to edit it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTodoItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Assign To-do</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/itemComments?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/comment.png" border="0" />&nbsp;View public comments</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$item._model.name} Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" class="right-nav-link">Back to {$item._model.plural_name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$item._model.id}';" class="right-nav-link">New {$item._model.name}</a></li>
  </ul>
  
</div>
