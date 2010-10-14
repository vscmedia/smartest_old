<div id="work-area-full">
  
  <h3>Welcome to Smartest</h3>
  
  <div class="_new-desktop-column">
    <div class="_new-desktop-column-inner">
      
      <h4>Places</h4>
      <ul class="_new-desktop-options-list">
        <li><a href="{$domain}smartest/pages">Sitemap</a></li>
        <li><a href="{$domain}smartest/files">Files</a></li>
{foreach from=$models item="model"}
        <li><a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a></li>
{/foreach}
        <li><a href="{$domain}smartest/templates">Templates</a></li>
        <li><a href="{$domain}smartest/todo">Todo-list</a></li>
        <li><a href="{$domain}smartest/settings">Site settings</a></li>
        <li><a href="{$domain}smartest/users">Users &amp; Permissions</a></li>
      </ul>
      
      <h4>Common tasks</h4>
      <ul class="_new-desktop-options-list">
        <li><a href="{$domain}users/addUser">Add a new user</a></li>
        <li><a href="{$domain}websitemanager/clearPagesCache">Clear the pages cache</a></li>
        <li><a href="{$domain}websitemanager/releaseCurrentUserHeldPages">Release all pages</a></li>
{foreach from=$models item="model"}
        <li><a href="{$domain}datamanager/releaseUserHeldItems?class_id={$model.id}">Release all {$model.plural_name|lower}</a></li>
{/foreach}
      </ul>
      
    </div>
  </div>
  
  <div class="_new-desktop-column">
    <div class="_new-desktop-column-inner">
      
      <script type="text/javascript">
        var createNew = new Smartest.UI.ListSwitcher;
      </script>
      
      <h4>Create something new</h4>
      <ul class="_new-desktop-square-buttons">
        <li><a href="#" onclick="createNew.setOptionListVisible('file')">File</a></li>
        <li><a href="#" onclick="createNew.setOptionListVisible('template')">Template</a></li>
{foreach from=$models item="model"}
        <li><a href="{$domain}datamanager/addItem?class_id={$model.id}">{$model.name}</a></li>
{/foreach}
        <li><a href="#" onclick="createNew.setOptionListVisible('set')">Data set</a></li>
        <li><a href="#" onclick="createNew.setOptionListVisible('other')">Something else</a></li>
        <br clear="all" />
      </ul>
      
      <div class="">
      
        <ul class="_new-desktop-options-list" style="display:none" id="createnew-item">
{foreach from=$models item="model"}
          <li><a href="{$domain}datamanager/addItem?class_id={$model.id}">{$model.name}</a></li>
{/foreach}
        </ul>
      
        <ul class="_new-desktop-options-list" style="display:none" id="createnew-set">
{foreach from=$models item="model"}
          <li><a href="{$domain}sets/addSet?class_id={$model.id}">Set of {$model.plural_name}</a></li>
{/foreach}
        </ul>
      
        <ul class="_new-desktop-options-list" style="display:none" id="createnew-file">
{foreach from=$file_types item="file_type"}
          <li><a href="{$domain}assets/addAsset?asset_type={$file_type.id}">{$file_type.label}</a></li>
{/foreach}
        </ul>
      
        <ul class="_new-desktop-options-list" style="display:none" id="createnew-filegroup">
{foreach from=$placeholder_types item="placeholder_type"}
          <li><a href="{$domain}assets/newAssetGroup?filter_type={$placeholder_type.id}">{$placeholder_type.label}</a></li>
{/foreach}        
          <li class="divider"></li>
{foreach from=$file_types item="file_type"}
          <li><a href="{$domain}assets/newAssetGroup?filter_type={$file_type.id}">{$file_type.label}</a></li>
{/foreach}
        </ul>
      
        <ul class="_new-desktop-options-list" style="display:none" id="createnew-template">
{foreach from=$template_types item="template_type"}
          <li><a href="{$domain}templates/addTemplate?type={$template_type.id}">{$template_type.label}</a></li>
{/foreach}
        </ul>
      
        <ul class="_new-desktop-options-list" style="display:none" id="createnew-other">
          <li><a href="{$domain}datamanager/addItemClass">Model</a></li>
          <li><a href="{$domain}assets/newAssetGroup">File group</a></li>
          <li><a href="{$domain}users/addUser">User</a></li>
          <li><a href="{$domain}dropdowns/addDropDown">Dropdown menu</a></li>
          <li><a href="{$domain}metadata/addTag">Tag</a></li>
        </ul>
      
      </div>
      
    </div>
  </div>
  
  <div class="_new-desktop-column last">
    <div class="_new-desktop-column-inner">
      <h4>Things you've been working on</h4>
{foreach from=$models item="model"}
{if !empty($recently_edited.items[$model.id])}
      <p>{$model.plural_name}</p>
      <ul class="_new-desktop-options-list">
{foreach from=$recently_edited.items[$model.id] item="re"}
        <li><a href="{$re.action_url}"><img src="{$re.small_icon}"> {$re.label}</a></li>
{/foreach}
        <li><a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">See all...</a></li>
      </ul>
{/if}
{/foreach}

      <p>Pages</p>
      <ul class="_new-desktop-options-list">
{foreach from=$recently_edited.pages item="re"}
        <li><a href="{$re.action_url}"><img src="{$re.small_icon}"> {$re.label}</a></li>
{/foreach}
        <li><a href="{$domain}smartest/pages">Sitemap...</a></li>
      </ul>

      <p>Files</p>
      <ul class="_new-desktop-options-list">
{foreach from=$recently_edited.files item="re"}
        <li><a href="{$re.action_url}"><img src="{$re.small_icon}"> {$re.label}</a></li>
{/foreach}
        <li><a href="{$domain}smartest/files/types">Browse more files...</a></li>
      </ul>
      
      <p>Templates</p>
      <ul class="_new-desktop-options-list">
{foreach from=$recently_edited.templates item="re"}
        <li><a href="{$re.action_url}"><img src="{$re.small_icon}"> {$re.label}</a></li>
{/foreach}
      </ul>
      
    </div>
  </div>
  
</div>