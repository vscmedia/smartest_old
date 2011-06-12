<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Tags</h3>
  
  {if $show_tags}
  
  <div class="instruction">Tags exist across all your sites. Some tags may not make sense for certain sites, but they can be ignored.</div>
  
  <script type="text/javascript">
  var TL = new Smartest.UI.TagsList();
  </script>
  
  <div style="text-align:justify">
  {foreach from=$tags item="tag"}
    <a class="tag{if in_array($tag.id, $used_tags_ids)} selected{/if}" href="javascript:TL.togglePageTagged({$page.id}, {$tag.id});" id="tag-link-{$tag.id}">{$tag.label}</a>
  {/foreach}
  </div>
  
  {else if $page.type == 'ITEMCLASS'}
  
  {load_interface file="choose_item.tpl"}
  
  {/if}
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tagging Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}metadata/addTag'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>
</div>