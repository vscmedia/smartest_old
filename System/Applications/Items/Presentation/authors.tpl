<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Authors of this page</h3>
    
    <div class="instruction">Check the boxes next to the users you'd like to link to this page as authors.</div>
    
    <form action="{$domain}{$section}/updateAuthors" method="post">
    
      <input type="hidden" name="item_id" value="{$item.id}" />
    
      <ul class="basic-list scroll-list" style="height:350px;border:1px solid #ccc">
      
        {foreach from=$users item="user"}
        <li><input type="checkbox" name="users[{$user.id}]" id="user_{$user.id}"{if in_array($user.id, $author_ids)} checked="checked"{/if} /><label for="user_{$user.id}">{$user.full_name}</label></li>
        {/foreach}
      
      </ul>
  
      <div id="edit-form-layout">
        <div class="edit-form-row">
          <div class="buttons-bar">
            <input type="button" value="Cancel" onclick="cancelForm();" />
            <input type="submit" name="action" value="Save" />
          </div>
        </div>
      </div>
  
    </form>
  
</div>

<div id="actions-area">
  
</div>