<div id="work-area">
  
  {load_interface file="edit_asset_tabs.tpl"}
  
  <h3>Tags</h3>
  
  <script type="text/javascript">
  var TL = new Smartest.UI.TagsList();
  </script>
  
  <div style="text-align:justify">
  {foreach from=$tags item="tag"}
    <a class="tag{if $tag.attached} selected{/if}" href="javascript:TL.toggleAssetTagged({$asset.id}, {$tag.id});" id="tag-link-{$tag.id}">{$tag.label}</a>
  {/foreach}
  </div>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tagging Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}metadata/addTag?asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>
</div>