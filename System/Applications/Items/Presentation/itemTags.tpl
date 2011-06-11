<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Tags for this {$model.name} ({$item.name})</h3>
  <div class="instruction">Choose which tags this item is attached to. Some tags may not make sense for certain sites, but they can be ignored.</div>
  
  <form action="{$domain}{$section}/updateItemTags" method="post">
    
    <input type="hidden" name="item_id" value="{$item.id}" />
    
    {* <ul class="basic-list">
      {foreach from=$tags item="tag"}
      <li><input type="checkbox" name="tags[{$tag.id}]" id="tag_{$tag.id}"{if $tag.attached} checked="checked"{/if} /><label for="tag_{$tag.id}">{$tag.label}</label></li>
      {/foreach}
    </ul> *}
    
    <script type="text/javascript">
    var TL = new Smartest.UI.TagsList();
    </script>
    
    {foreach from=$tags item="tag"}
      <a class="tag{if $tag.attached} selected{/if}" href="javascript:TL.toggleItemTagged({$item.id}, {$tag.id});" id="tag-link-{$tag.id}">{$tag.label}</a>
    {/foreach}
  
    {* <div id="edit-form-layout">
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" name="action" value="Save" />
        </div>
      </div>
    </div> *}
  
  </form>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tagging Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}metadata/addTag?item_id={$item.id}'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" />&nbsp;Release for others to edit</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/approveItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Approve changes</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" />&nbsp;Publish it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item.itemclass_id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Finish editing for now</a></li>
  </ul>

</div>