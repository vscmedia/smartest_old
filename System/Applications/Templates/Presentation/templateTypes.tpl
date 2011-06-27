<div id="work-area">

<h3>Templates</h3>

{load_interface file="template_browse_tabs.tpl"}

{if count($locations)}
  <div class="warning">
      <p>For smooth operation of the templates repository, the following locations need to be made writable:</p>
      <ul>
{foreach from=$locations item="l"}
        <li><code>{$l}</code></li>
{/foreach}        
      </ul>
  </div>
{/if}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="item_id_input" name="type" value="" />
</form>

<div class="instruction">There are six different kinds of template. Please select which type you'd like to work with.</div>

<ul class="options-grid-no-scroll" style="margin-top:0px">
  {foreach from=$types item="assetType"}
    <li ondblclick="window.location='{$domain}smartest/templates/{$assetType.id}'">
      <a href="javascript:nothing();" id="item_{$assetType.id}" class="option" onclick="setSelectedItem('{$assetType.id}', '{$assetType.label|escape:quotes}');">
        <img border="0" src="{$domain}Resources/Icons/folder.png" />{$assetType.label}s</a></li>{* $assetType.icon *}
  {/foreach}
</ul><br clear="all" />

</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><strong>Select template type</strong></li>
    <li class="permament-action"><a href="#" onclick="workWithItem('listByType')" class="right-nav-link"><img src="{$domain}Resources/Icons/page_white_stack.png" /> Browse these templates</a></li>
    <li class="permament-action"><a href="#" onclick="workWithItem('addTemplate')" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" /> Add a template of this type</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><strong>Options</strong></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}templates/import'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_magnify.png" /> Detect new templates</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}templates/addTemplate'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" /> Add a new template</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Recently edited templates</b></li>
    {foreach from=$recently_edited item="recent_template"}
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_template.action_url}'"><img border="0" src="{$recent_template.small_icon}" /> {$recent_template.label|summary:"30"}</a></li>
    {/foreach}
  </ul>
  
</div>