<div id="work-area">
  <ul class="modal-options-grid">
    {if $allow_create_pages}<li id="create-modal-option-page"><a href="{$domain}smartest/page/new">Web page</a></li>{/if}
    {if $allow_create_files}<li id="create-modal-option-file"><a href="{$domain}smartest/file/new">File</a></li>{/if}
{if $allow_create_items}
{foreach from=$models item="model"}
    <li id="create-modal-option-item-class{$model.id}" class="create-modal-item"><a href="{$domain}smartest/items/{$model.varname}/new">{$model.name}</a></li>
{/foreach}
{/if}
    {if $allow_create_files}<li id="create-modal-option-filegroup"><a href="{$domain}assets/newAssetGroup">File group</a></li>{/if}
    {if $show_left_nav_options}<li id="create-modal-option-template"><a href="{$domain}templates/addTemplate">Template</a></li>{/if}
    {if $allow_create_models}<li id="create-modal-option-model"><a href="{$domain}smartest/model/new">Model</a></li>{/if}
    {if $show_left_nav_options}<li id="create-modal-option-set"><a href="{$domain}sets/addSet">Data set</a></li>{/if}
    {if $allow_create_sites}<li id="create-modal-option-site"><a href="{$domain}smartest/site/new">Site</a></li>{/if}
    {if $allow_create_users}<li id="create-modal-option-user"><a href="{$domain}smartest/users/add">User</a></li>{/if}
  </ul>
</div>