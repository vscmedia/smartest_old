<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Tags for this {$model.name} ({$item.name})</h3>
  <div class="instruction">Choose which tags this item is attached to. Some tags may not make sense for certain sites, but they can be ignored.</div>
  
  <script type="text/javascript">
  var TL = new Smartest.UI.TagsList();
  </script>
  
  <div style="text-align:justify">
  {foreach from=$tags item="tag"}
    <a class="tag{if $tag.attached} selected{/if}" href="javascript:TL.toggleItemTagged({$item.id}, {$tag.id});" id="tag-link-{$tag.id}">{$tag.label}</a>
  {/foreach}
  </div>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tagging Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}metadata/addTag?item_id={$item.id}{if $request_parameters.page_id}&amp;page_webid={$request_parameters.page_id}{/if}'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" />&nbsp;Release for others to edit</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/approveItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Approve changes</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" />&nbsp;Publish it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item.itemclass_id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Finish editing for now</a></li>
  </ul>

</div>