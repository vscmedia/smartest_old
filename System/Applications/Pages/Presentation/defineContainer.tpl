<div id="work-area">
  
  <h3>Define Container</h3>
  <div class="instruction">Please choose a template to use in this container.</div>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="page_id" value="{$page.id}" />
    <input type="hidden" name="container_id" value="{$container.id}" />
    <input type="hidden" name="asset_id" id="item_id_input" value="" />
  </form>
  
  <div id="options-view-chooser">
  <a href="javascript:nothing()" onclick="setView('list', 'options_grid')">List</a> /
  <a href="javascript:nothing()" onclick="setView('grid', 'options_grid')">Icons</a>
  </div>

  <ul class="options-grid" style="margin-top:0px" id="options_grid">
  {foreach from=$templates item="asset"}
  <li>
      <a href="javascript:nothing()" class="option" id="item_{$asset.id}" onclick="setSelectedItem('{$asset.id}');" >
      <img border="0" src="{$domain}Resources/Icons/blank_page.png" />{$asset.stringid}</a>
  </li>
  {/foreach}
  </ul>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected Asset</b></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('updateContainerDefinition');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Use This Asset</a></li>
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$page.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Cancel</a></li>
  </ul>
  
</div>