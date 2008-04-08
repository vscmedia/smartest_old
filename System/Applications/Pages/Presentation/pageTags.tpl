<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Tags</h3>
  <div class="instruction">Choose which tags this page is attached to.</div>
  <div class="instruction">Tags exist across all your sites. Some pags may not make sense for certain sites, but they can be ignored.</div>
  
  <form action="{$domain}{$section}/updatePageTags" method="post">
    
    <input type="hidden" name="page_id" value="{$page.id}" />
    
    <ul class="basic-list">
      {foreach from=$tags item="tag"}
      <li><input type="checkbox" name="tags[{$tag.id}]" id="tag_{$tag.id}"{if $tag.attached} checked="checked"{/if} /><label for="tag_{$tag.id}">{$tag.label}</label></li>
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
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tagging Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}metadata/addTag'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>
</div>